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

use App\Models\RecurringQuote;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringQuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status_id' => RecurringQuote::STATUS_DRAFT,
            'number' => $this->faker->text(256),
            'discount' => $this->faker->numberBetween(1, 10),
            'is_amount_discount' => $this->faker->boolean(),
            'tax_name1' => 'GST',
            'tax_rate1' => 10,
            'tax_name2' => 'VAT',
            'tax_rate2' => 17.5,
            'tax_name3' => 'THIRDTAX',
            'tax_rate3' => 5,
            'custom_value1' => $this->faker->numberBetween(1, 4),
            'custom_value2' => $this->faker->numberBetween(1, 4),
            'custom_value3' => $this->faker->numberBetween(1, 4),
            'custom_value4' => $this->faker->numberBetween(1, 4),
            'is_deleted' => false,
            'po_number' => $this->faker->text(10),
            'date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'line_items' => false,
            'frequency_id' => RecurringQuote::FREQUENCY_MONTHLY,
            'last_sent_date' => $this->faker->date(),
            'next_send_date' => $this->faker->date(),
            'remaining_cycles' => $this->faker->numberBetween(1, 10),
        ];
    }
}
