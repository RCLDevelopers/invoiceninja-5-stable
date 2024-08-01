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

namespace App\Factory;

use App\Models\CompanyGateway;

class CompanyGatewayFactory
{
    public static function create(int $company_id, int $user_id): CompanyGateway
    {
        $company_gateway = new CompanyGateway();
        $company_gateway->company_id = $company_id;
        $company_gateway->user_id = $user_id;
        $company_gateway->require_billing_address = false;
        $company_gateway->require_shipping_address = false;
        $company_gateway->config = encrypt(json_encode(new \stdClass()));
        $company_gateway->always_show_required_fields = true;

        return $company_gateway;
    }
}
