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

namespace App\Http\Controllers\VendorPortal;

use App\Http\Controllers\Controller;
use App\Models\VendorContact;
use App\Utils\Traits\MakesHash;
use App\Utils\TranslationHelper;

class VendorContactController extends Controller
{
    use MakesHash;

    public const MODULE_RECURRING_INVOICES = 1;

    public const MODULE_CREDITS = 2;

    public const MODULE_QUOTES = 4;

    public const MODULE_TASKS = 8;

    public const MODULE_EXPENSES = 16;

    public const MODULE_PROJECTS = 32;

    public const MODULE_VENDORS = 64;

    public const MODULE_TICKETS = 128;

    public const MODULE_PROPOSALS = 256;

    public const MODULE_RECURRING_EXPENSES = 512;

    public const MODULE_RECURRING_TASKS = 1024;

    public const MODULE_RECURRING_QUOTES = 2048;

    public const MODULE_INVOICES = 4096;

    public const MODULE_PROFORMAL_INVOICES = 8192;

    public const MODULE_PURCHASE_ORDERS = 16384;

    public function edit(VendorContact $vendor_contact)
    {
        return $this->render('vendor_profile.edit', [
            'contact' => $vendor_contact,
            'vendor' => $vendor_contact->vendor,
            'settings' => $vendor_contact->vendor->company->settings,
            'company' => $vendor_contact->vendor->company,
            'sidebar' => $this->sidebarMenu(),
            'countries' => TranslationHelper::getCountries(),
        ]);
    }

    public function update(VendorContact $vendor_contact)
    {
        $vendor_contact->fill(request()->all());
        $vendor_contact->vendor->fill(request()->all());
        $vendor_contact->push();

        return back()->withSuccess(ctrans('texts.profile_updated_successfully'));
    }

    private function sidebarMenu(): array
    {
        $enabled_modules = auth()->guard('vendor')->user()->company->enabled_modules;
        $data = [];

        // TODO: Enable dashboard once it's completed.
        // $this->settings->enable_client_portal_dashboard
        // $data[] = [ 'title' => ctrans('texts.dashboard'), 'url' => 'client.dashboard', 'icon' => 'activity'];

        if (self::MODULE_PURCHASE_ORDERS & $enabled_modules) {
            $data[] = ['title' => ctrans('texts.purchase_orders'), 'url' => 'vendor.purchase_orders.index', 'icon' => 'file-text'];
        }

        // $data[] = ['title' => ctrans('texts.documents'), 'url' => 'client.documents.index', 'icon' => 'download'];

        return $data;
    }
}
