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

namespace Database\Factories;

use App\DataMapper\ClientSettings;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'website' => $this->faker->url(),
            'private_notes' => $this->faker->text(200),
            'balance' => 0,
            'paid_to_date' => 0,
            'vat_number' => $this->faker->numberBetween(123456789, 987654321),
            'id_number' => $this->faker->iban(),
            'custom_value1' => $this->faker->dateTime(),
            'custom_value2' => $this->faker->colorName(),
            'custom_value3' => $this->faker->word(),
            'custom_value4' => $this->faker->email(),
            'address1' => $this->faker->buildingNumber(),
            'address2' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country_id' => 4,
            'shipping_address1' => $this->faker->buildingNumber(),
            'shipping_address2' => $this->faker->streetAddress(),
            'shipping_city' => $this->faker->city(),
            'shipping_state' => $this->faker->state(),
            'shipping_postal_code' => $this->faker->postcode(),
            'shipping_country_id' => 4,
            'settings' => ClientSettings::defaults(),
            'client_hash' => \Illuminate\Support\Str::random(40),
            'routing_id' => rand(100000,200000),
        ];
    }
}
