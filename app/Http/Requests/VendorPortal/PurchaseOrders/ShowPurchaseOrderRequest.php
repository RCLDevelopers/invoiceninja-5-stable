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

namespace App\Http\Requests\VendorPortal\PurchaseOrders;

use App\Http\Requests\Request;
use App\Http\ViewComposers\PortalComposer;

class ShowPurchaseOrderRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return (int) auth()->guard('vendor')->user()->vendor_id === (int) $this->purchase_order->vendor_id
            && (bool)(auth()->guard('vendor')->user()->company->enabled_modules & PortalComposer::MODULE_PURCHASE_ORDERS);
    }
}
