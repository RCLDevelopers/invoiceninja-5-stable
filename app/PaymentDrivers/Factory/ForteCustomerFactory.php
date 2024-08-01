<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\PaymentDrivers\Factory;

use App\Models\Client;
use App\Models\Company;

class ForteCustomerFactory
{
    public function convertToNinja(array $customer, Company $company): array
    {
        return
        collect([
          'name' => $customer['company_name'] ?? $customer['first_name'],
          'contacts' => [
              [
                  'first_name' => $customer['first_name'],
                  'last_name' => $customer['last_name'],
                  'email' => $this->getBillingAddress($customer)['email'],
                  'phone' => $this->getBillingAddress($customer)['phone'],
              ]
          ],
          'settings' => [
              'currency_id' => $company->settings->currency_id,
          ],
        ])->merge($this->getShippingAddress($customer))
        ->merge($this->getBillingAddress($customer))
        ->toArray();

    }

    // public function convertToGateway(Client $client): array
    // {

    // }

    private function getBillingAddress(array $customer): array
    {
        if(isset($customer['default_billing_address_token'])) {

            foreach($customer['addresses'] as $address) {

                if($address['address_token'] != $customer['default_billing_address_token']) {
                    continue;
                }

                return [
                    'address1' => $address['physical_address']['street_line1'],
                    'address2' => $address['physical_address']['street_line2'],
                    'city' => $address['physical_address']['locality'],
                    'state' => $address['physical_address']['region'],
                    'postal_code' => $address['physical_address']['postal_code'],
                    'country_id' => '840',
                    'email' => $address['email'],
                    'phone' => $address['phone'],
                ];

            }

        }

        if(isset($customer['addresses'][0])) {

            $address = $customer['addresses'][0];

            return [
                'address1' => $address['physical_address']['street_line1'],
                'address2' => $address['physical_address']['street_line2'],
                'city' => $address['physical_address']['locality'],
                'state' => $address['physical_address']['region'],
                'postal_code' => $address['physical_address']['postal_code'],
                'email' => $address['email'],
                'phone' => $address['phone'],
                'country_id' => '840',
            ];

        }

        return ['email' => '', 'phone' => ''];

    }

    private function getShippingAddress(array $customer): array
    {

        if(isset($customer['default_shipping_address_token'])) {

            foreach($customer['addresses'] as $address) {

                if($address['address_token'] != $customer['default_shipping_address_token']) {
                    continue;
                }

                return [
                    'shipping_address1' => $address['physical_address']['street_line1'],
                    'shipping_address2' => $address['physical_address']['street_line2'],
                    'shipping_city' => $address['physical_address']['locality'],
                    'shipping_state' => $address['physical_address']['region'],
                    'shipping_postal_code' => $address['physical_address']['postal_code'],
                    'shipping_country_id' => '840',
                ];

            }

        }

        if(isset($customer['addresses'][1])) {

            $address = $customer['addresses'][1];

            return [
                'shipping_address1' => $address['physical_address']['street_line1'],
                'shipping_address2' => $address['physical_address']['street_line2'],
                'shipping_city' => $address['physical_address']['locality'],
                'shipping_state' => $address['physical_address']['region'],
                'shipping_postal_code' => $address['physical_address']['postal_code'],
                'shipping_country_id' => '840',
                'email' => $address['email'],
                'phone' => $address['phone'],
            ];

        }

        return ['email' => '', 'phone' => ''];

    }
}
