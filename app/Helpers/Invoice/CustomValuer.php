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

/**
 * Class for discount calculations.
 */
trait CustomValuer
{
    public function valuer($custom_value)
    {
        if (isset($custom_value) && is_numeric($custom_value)) {
            return $custom_value;
        }

        return 0;
    }

    public function valuerTax($custom_value, $has_custom_invoice_taxes)
    {

        if (isset($custom_value) && is_numeric($custom_value) && $has_custom_invoice_taxes !== false) {
            return round($custom_value * ($this->invoice->tax_rate1 / 100), 2) + round($custom_value * ($this->invoice->tax_rate2 / 100), 2) + round($custom_value * ($this->invoice->tax_rate3 / 100), 2);
        }

        return 0;
    }

    public function multiInclusiveTax($custom_value, $has_custom_invoice_taxes)
    {

        if (isset($custom_value) && is_numeric($custom_value) && $has_custom_invoice_taxes !== false) {

            $tax = 0;

            $tax += $this->formatValue($custom_value - ($custom_value / (1 + ($this->invoice->tax_rate1 / 100))), 2);
            $tax += $this->formatValue($custom_value - ($custom_value / (1 + ($this->invoice->tax_rate2 / 100))), 2);
            $tax += $this->formatValue($custom_value - ($custom_value / (1 + ($this->invoice->tax_rate3 / 100))), 2);

            return round($tax, 2);
        }

        return 0;

    }
}
