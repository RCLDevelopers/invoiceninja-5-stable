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

namespace App\Repositories;

use App\Models\Design;
use Illuminate\Support\Str;

/**
 * Class for DesignRepository .
 */
class DesignRepository extends BaseRepository
{
    public function delete($design): Design
    {
        $design->name = $design->name.'_deleted_'.Str::random(5);

        /** Make sure this design was not a default design - if it is, set the Clean template as the default */

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $company = $user->company();
        $settings = $company->settings;

        if ($settings->invoice_design_id == $design->hashed_id) {
            $settings->invoice_design_id = 'Wpmbk5ezJn';
        }

        if ($settings->quote_design_id == $design->hashed_id) {
            $settings->quote_design_id = 'Wpmbk5ezJn';
        }

        if ($settings->credit_design_id == $design->hashed_id) {
            $settings->credit_design_id = 'Wpmbk5ezJn';
        }

        if ($settings->purchase_order_design_id == $design->hashed_id) {
            $settings->purchase_order_design_id = 'Wpmbk5ezJn';
        }

        $company->settings = $settings;
        $company->save();

        parent::delete($design);

        return $design;
    }
}
