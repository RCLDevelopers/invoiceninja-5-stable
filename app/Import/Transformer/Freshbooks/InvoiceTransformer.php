<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Import\Transformer\Freshbooks;

use App\Import\ImportException;
use App\Import\Transformer\BaseTransformer;
use App\Models\Invoice;

/**
 * Class InvoiceTransformer.
 */
class InvoiceTransformer extends BaseTransformer
{
    /**
     * @param $line_items_data
     *
     * @return bool|array
     */
    public function transform($line_items_data)
    {
        $invoice_data = reset($line_items_data);

        if ($this->hasInvoice($invoice_data['Invoice #'])) {
            throw new ImportException('Invoice number already exists');
        }

        $invoiceStatusMap = [
            'sent'  => Invoice::STATUS_SENT,
            'draft' => Invoice::STATUS_DRAFT,
        ];

        $transformed = [
            'company_id'  => $this->company->id,
            'client_id'   => $this->getClient($this->getString($invoice_data, 'Client Name'), null),
            'number'      => $this->getString($invoice_data, 'Invoice #'),
            'date'        => isset($invoice_data['Date Issued']) ? $this->parseDate($invoice_data['Date Issued']) : null,
            'amount'      => 0,
            'status_id'   => $invoiceStatusMap[$status =
                    strtolower($this->getString($invoice_data, 'Invoice Status'))] ?? Invoice::STATUS_SENT,
            // 'viewed'      => $status === 'viewed',
        ];

        $line_items = [];
        foreach ($line_items_data as $record) {
            $line_items[] = [
                'product_key'        => $this->getString($record, 'Item Name'),
                'notes'              => $this->getString($record, 'Item Description'),
                'cost'               => $this->getFreshbookQuantityFloat($record, 'Rate'),
                'quantity'           => $this->getFreshbookQuantityFloat($record, 'Quantity'),
                'discount'           => $this->getFreshbookQuantityFloat($record, 'Discount Percentage'),
                'is_amount_discount' => false,
                'tax_name1'          => $this->getString($record, 'Tax 1 Type'),
                'tax_rate1'          => $this->calcTaxRate($record, 'Tax 1 Amount'),
                'tax_name2'          => $this->getString($record, 'Tax 2 Type'),
                'tax_rate2'          => $this->calcTaxRate($record, 'Tax 2 Amount'),
            ];
            $transformed['amount'] += $this->getFreshbookQuantityFloat($record, 'Line Total');
        }
        $transformed['line_items'] = $line_items;

        if (! empty($invoice_data['Date Paid'])) {
            $transformed['payments'] = [[
                'date'   => $this->parseDate($invoice_data['Date Paid']),
                'amount' => $transformed['amount'],
            ]];
        }

        return $transformed;
    }

    //Line Subtotal
    public function calcTaxRate($record, $field)
    {
        if (isset($record['Line Subtotal']) && $record['Line Subtotal'] > 0) {
            return ($record[$field] / $record['Line Subtotal']) * 100;
        }

        $tax_amount1 = isset($record['Tax 1 Amount']) ? $record['Tax 1 Amount'] : 0;

        $tax_amount2 = isset($record['Tax 2 Amount']) ? $record['Tax 2 Amount'] : 0;

        $line_total = isset($record['Line Total']) ? $record['Line Total'] : 0;

        $subtotal = $line_total - $tax_amount2 - $tax_amount1;

        if ($subtotal > 0) {
            return $record[$field] / $subtotal * 100;
        }

        return 0;
    }

    /** @return float  */
    public function getFreshbookQuantityFloat($data, $field)
    {
        return $data[$field];
    }
}
