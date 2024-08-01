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

namespace App\PaymentDrivers\Stripe\Jobs;

use App\Libraries\MultiDB;
use App\Models\Company;
use App\Models\CompanyGateway;
use App\PaymentDrivers\Stripe\Utilities;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StripeWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Utilities;

    public $tries = 1;

    public $deleteWhenMissingModels = true;

    public int $company_gateway_id;

    public string $company_key;

    private bool $url_found = false;

    private array $events = [
        'source.chargeable',
        'charge.succeeded',
        'charge.failed',
        'payment_intent.succeeded',
        'payment_intent.payment_failed',
        'mandate.updated',
        'checkout.session.completed',
        'payment_method.automatically_updated'
    ];

    public function __construct(string $company_key, int $company_gateway_id)
    {
        $this->company_key = $company_key;
        $this->company_gateway_id = $company_gateway_id;
    }

    public function handle()
    {
        MultiDB::findAndSetDbByCompanyKey($this->company_key);

        $company = Company::where('company_key', $this->company_key)->first();

        /** @var \App\Models\CompanyGateway $company_gateway **/
        $company_gateway = CompanyGateway::find($this->company_gateway_id);

        $stripe = $company_gateway->driver()->init();

        $endpoints = \Stripe\WebhookEndpoint::all([], $stripe->stripe_connect_auth);

        $webhook_url = $company_gateway->webhookUrl();

        foreach ($endpoints['data'] as $endpoint) {
            if ($endpoint->url === $webhook_url) {
                \Stripe\WebhookEndpoint::update($endpoint->id, ['enabled_events' => $this->events], $stripe->stripe_connect_auth);

                $this->url_found = true;
            }
        }

        /* Add new webhook */
        if (! $this->url_found) {
            \Stripe\WebhookEndpoint::create([
                'url' => $webhook_url,
                'enabled_events' => $this->events,
            ], $stripe->stripe_connect_auth);
        }
    }
}
