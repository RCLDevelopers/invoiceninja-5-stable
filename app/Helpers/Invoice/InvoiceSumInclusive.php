<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2024. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Helpers\Invoice;

use App\Models\Quote;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\RecurringQuote;
use App\Models\RecurringInvoice;
use Illuminate\Support\Collection;
use App\Utils\Traits\NumberFormatter;

class InvoiceSumInclusive
{
    use Taxer;
    use CustomValuer;
    use Discounter;
    use NumberFormatter;

    protected RecurringInvoice | Invoice | Quote | Credit | PurchaseOrder | RecurringQuote $invoice;

    public $tax_map;

    public $invoice_item;

    public $total_taxes;

    private $total;

    private $total_discount;

    private $total_custom_values;

    private $total_tax_map;

    private $sub_total;

    private $precision;

    private $rappen_rounding = false;

    public InvoiceItemSumInclusive $invoice_items;
    /**
     * Constructs the object with Invoice and Settings object.
     *
     * @param RecurringInvoice | Invoice | Quote | Credit | PurchaseOrder | RecurringQuote $invoice;
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;

        if ($this->invoice->client) {
            $this->precision = $this->invoice->client->currency()->precision;
            $this->rappen_rounding = $this->invoice->client->getSetting('enable_rappen_rounding');
        } else {
            $this->precision = $this->invoice->vendor->currency()->precision;
            $this->rappen_rounding = $this->invoice->vendor->getSetting('enable_rappen_rounding');
        }

        $this->tax_map = new Collection();
    }

    public function build()
    {
        $this->calculateLineItems()
             ->calculateDiscount()
             ->calculateInvoiceTaxes()
             ->calculateCustomValues()
             ->setTaxMap()
             ->calculateTotals() //just don't add the taxes!!
             ->calculateBalance()
             ->calculatePartial();

        return $this;
    }

    private function calculateLineItems()
    {
        $this->invoice_items = new InvoiceItemSumInclusive($this->invoice);
        $this->invoice_items->process();
        $this->invoice->line_items = $this->invoice_items->getLineItems();
        $this->total = $this->invoice_items->getSubTotal();
        $this->setSubTotal($this->invoice_items->getSubTotal());

        return $this;
    }

    private function calculateDiscount()
    {
        $this->total_discount = $this->discount($this->invoice_items->getSubTotal());

        $this->total -= $this->total_discount;

        return $this;
    }

    private function calculateCustomValues()
    {

        // $this->total_taxes += $this->multiInclusiveTax($this->invoice->custom_surcharge1, $this->invoice->custom_surcharge_tax1);
        $this->total_custom_values += $this->valuer($this->invoice->custom_surcharge1);

        // $this->total_taxes += $this->multiInclusiveTax($this->invoice->custom_surcharge2, $this->invoice->custom_surcharge_tax2);
        $this->total_custom_values += $this->valuer($this->invoice->custom_surcharge2);

        // $this->total_taxes += $this->multiInclusiveTax($this->invoice->custom_surcharge3, $this->invoice->custom_surcharge_tax3);
        $this->total_custom_values += $this->valuer($this->invoice->custom_surcharge3);

        // $this->total_taxes += $this->multiInclusiveTax($this->invoice->custom_surcharge4, $this->invoice->custom_surcharge_tax4);
        $this->total_custom_values += $this->valuer($this->invoice->custom_surcharge4);

        $this->total += $this->total_custom_values;

        return $this;
    }

    private function calculateInvoiceTaxes()
    {
        $amount = $this->total;

        if ($this->invoice->discount > 0 && $this->invoice->is_amount_discount) {
            $amount = $this->formatValue(($this->sub_total - $this->invoice->discount), 2);
        }

        if ($this->invoice->discount > 0 && ! $this->invoice->is_amount_discount) {
            $amount = $this->formatValue(($this->sub_total - ($this->sub_total * ($this->invoice->discount / 100))), 2);
        }

        //Handles cases where the surcharge is not taxed
        if(is_numeric($this->invoice->custom_surcharge1) && $this->invoice->custom_surcharge1 > 0 && $this->invoice->custom_surcharge_tax1) {
            $amount += $this->invoice->custom_surcharge1;
        }

        if(is_numeric($this->invoice->custom_surcharge2) && $this->invoice->custom_surcharge2 > 0 && $this->invoice->custom_surcharge_tax2) {
            $amount += $this->invoice->custom_surcharge2;
        }

        if(is_numeric($this->invoice->custom_surcharge3) && $this->invoice->custom_surcharge3 > 0 && $this->invoice->custom_surcharge_tax3) {
            $amount += $this->invoice->custom_surcharge3;
        }

        if(is_numeric($this->invoice->custom_surcharge4) && $this->invoice->custom_surcharge4 > 0 && $this->invoice->custom_surcharge_tax4) {
            $amount += $this->invoice->custom_surcharge4;
        }

        if (is_string($this->invoice->tax_name1) && strlen($this->invoice->tax_name1) > 1) {
            $tax = $this->calcInclusiveLineTax($this->invoice->tax_rate1, $amount);
            $this->total_taxes += $tax;

            $this->total_tax_map[] = ['name' => $this->invoice->tax_name1.' '.floatval($this->invoice->tax_rate1).'%', 'total' => $tax];
        }

        if (is_string($this->invoice->tax_name2) && strlen($this->invoice->tax_name2) > 1) {
            $tax = $this->calcInclusiveLineTax($this->invoice->tax_rate2, $amount);
            $this->total_taxes += $tax;
            $this->total_tax_map[] = ['name' => $this->invoice->tax_name2.' '.floatval($this->invoice->tax_rate2).'%', 'total' => $tax];
        }

        if (is_string($this->invoice->tax_name3) && strlen($this->invoice->tax_name3) > 1) {
            $tax = $this->calcInclusiveLineTax($this->invoice->tax_rate3, $amount);
            $this->total_taxes += $tax;
            $this->total_tax_map[] = ['name' => $this->invoice->tax_name3.' '.floatval($this->invoice->tax_rate3).'%', 'total' => $tax];
        }

        return $this;
    }

    /**
     * Calculates the balance.
     *
     * @return     self  The balance.
     */
    private function calculateBalance()
    {
        $this->setCalculatedAttributes();

        return $this;
    }

    private function calculatePartial()
    {
        if (! isset($this->invoice->id) && isset($this->invoice->partial)) {
            $this->invoice->partial = max(0, min($this->formatValue($this->invoice->partial, 2), $this->invoice->balance));
        }

        return $this;
    }

    private function calculateTotals()
    {
        return $this;
    }

    public function getTotalSurcharges()
    {
        return $this->total_custom_values;
    }

    public function getRecurringInvoice()
    {
        $this->invoice->amount = $this->formatValue($this->getTotal(), $this->precision);
        $this->invoice->total_taxes = $this->getTotalTaxes();
        $this->invoice->balance = $this->formatValue($this->getTotal(), $this->precision);

        $this->invoice->saveQuietly();

        return $this->invoice;
    }

    public function getTempEntity()
    {
        $this->setCalculatedAttributes();

        return $this->invoice;
    }

    /**
     * @return Invoice | RecurringInvoice | Quote | Credit | PurchaseOrder
     */
    public function getInvoice()
    {
        //Build invoice values here and return Invoice
        $this->setCalculatedAttributes();
        $this->invoice->saveQuietly();

        return $this->invoice;
    }

    /**
     * @return Invoice | RecurringInvoice | Quote | Credit | PurchaseOrder
     */
    public function getQuote()
    {
        //Build invoice values here and return Invoice
        $this->setCalculatedAttributes();
        $this->invoice->saveQuietly();

        return $this->invoice;
    }

    /**
     * @return Invoice | RecurringInvoice | Quote | Credit | PurchaseOrder
     */
    public function getCredit()
    {
        //Build invoice values here and return Invoice
        $this->setCalculatedAttributes();
        $this->invoice->saveQuietly();

        return $this->invoice;
    }

    /**
     * @return Invoice | RecurringInvoice | Quote | Credit | PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        //Build invoice values here and return Invoice
        $this->setCalculatedAttributes();
        $this->invoice->saveQuietly();

        return $this->invoice;
    }

    /**
     * Build $this->invoice variables after
     * calculations have been performed.
     */
    private function setCalculatedAttributes()
    {
        /* If amount != balance then some money has been paid on the invoice, need to subtract this difference from the total to set the new balance */
        if($this->invoice->status_id == Invoice::STATUS_CANCELLED) {
            $this->invoice->balance = 0;
        } elseif ($this->invoice->status_id != Invoice::STATUS_DRAFT) {
            if ($this->invoice->amount != $this->invoice->balance) {
                $this->invoice->balance = $this->formatValue($this->getTotal(), $this->precision) - $this->invoice->paid_to_date;
            } else {
                $this->invoice->balance = $this->formatValue($this->getTotal(), $this->precision);
            }
        }

        /* Set new calculated total */
        $this->invoice->amount = $this->formatValue($this->getTotal(), $this->precision);

        if($this->rappen_rounding) {
            $this->invoice->amount = $this->roundRappen($this->invoice->amount);
            $this->invoice->balance = $this->roundRappen($this->invoice->balance);
        }

        $this->invoice->total_taxes = $this->getTotalTaxes();

        return $this;
    }

    public function roundRappen($value): float
    {
        return round($value / .05, 0) * .05;
    }

    public function getSubTotal()
    {
        return $this->sub_total;
    }

    public function getGrossSubTotal()
    {
        return $this->sub_total;
    }

    public function setSubTotal($value)
    {
        $this->sub_total = $value;

        return $this;
    }

    public function getTotalDiscount()
    {
        return $this->total_discount;
    }

    public function getTotalTaxes()
    {
        return $this->total_taxes;
    }

    public function getTotalTaxMap()
    {
        return $this->total_tax_map;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTaxMap()
    {
        if ($this->invoice->is_amount_discount) {
            $this->invoice_items->calcTaxesWithAmountDiscount();
            $this->invoice->line_items = $this->invoice_items->getLineItems();
        }

        $this->tax_map = collect();

        $keys = $this->invoice_items->getGroupedTaxes()->pluck('key')->unique();

        $values = $this->invoice_items->getGroupedTaxes();

        foreach ($keys as $key) {
            $tax_name = $values->filter(function ($value, $k) use ($key) {
                return $value['key'] == $key;
            })->pluck('tax_name')->first();

            $total_line_tax = $values->filter(function ($value, $k) use ($key) {
                return $value['key'] == $key;
            })->sum('total');

            //$total_line_tax -= $this->discount($total_line_tax);

            $this->tax_map[] = ['name' => $tax_name, 'total' => $total_line_tax];

            $this->total_taxes += $total_line_tax;
        }

        return $this;
    }

    public function getTaxMap()
    {
        return $this->tax_map;
    }

    public function getBalance()
    {
        return $this->invoice->balance;
    }

    public function getItemTotalTaxes()
    {
        return $this->getTotalTaxes();
    }

    public function purgeTaxes()
    {
        return $this;
    }
}
