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

namespace App\Mail\Admin;

use App\Models\Invoice;
use App\Utils\HtmlEngine;
use App\Utils\Ninja;
use App\Utils\Traits\MakesHash;
use Illuminate\Support\Facades\App;
use stdClass;

class ClientPaymentFailureObject
{
    use MakesHash;

    public $client;

    public $error;

    public $company;

    public $payment_hash;

    private $invoices;

    /**
     * Create a new job instance.
     *
     * @param $client
     * @param $message
     * @param $company
     * @param $amount
     */
    public function __construct($client, $error, $company, $payment_hash)
    {
        $this->client = $client;

        $this->error = $error;

        $this->company = $company;

        $this->payment_hash = $payment_hash;

        $this->company = $company;
    }

    public function build()
    {
        if (! $this->payment_hash) {
            return;
        }

        App::forgetInstance('translator');
        /* Init a new copy of the translator*/
        $t = app('translator');
        /* Set the locale*/
        App::setLocale($this->client->locale());
        /* Set customized translations _NOW_ */
        $t->replace(Ninja::transformTranslations($this->company->settings));

        $this->invoices = Invoice::withTrashed()->whereIn('id', $this->transformKeys(array_column($this->payment_hash->invoices(), 'invoice_id')))->get();

        $mail_obj = new stdClass();
        $mail_obj->amount = $this->getAmount();
        $mail_obj->subject = $this->getSubject();
        $mail_obj->data = $this->getData();
        $mail_obj->markdown = 'email.client.generic';
        $mail_obj->tag = $this->company->company_key;
        $mail_obj->text_view = 'email.template.text';

        return $mail_obj;
    }

    private function getAmount()
    {
        return array_sum(array_column($this->payment_hash->invoices(), 'amount')) + $this->payment_hash->fee_total;
    }

    private function getSubject()
    {
        return
            ctrans(
                'texts.notification_invoice_payment_failed_subject',
                ['invoice' => implode(',', $this->invoices->pluck('number')->toArray())]
            );
    }

    private function getData()
    {
        $invitation = $this->invoices->first()->invitations->first();

        if (! $invitation) {
            throw new \Exception('Unable to find invitation for reference');
        }

        $signature = $this->client->getSetting('email_signature');
        $html_variables = (new HtmlEngine($invitation))->makeValues();
        $signature = str_replace(array_keys($html_variables), array_values($html_variables), $signature);

        $data = [
            'title' => ctrans(
                'texts.notification_invoice_payment_failed_subject',
                [
                    'invoice' => $this->invoices->first()->number,
                ]
            ),
            'greeting' => ctrans('texts.email_salutation', ['name' => $this->client->present()->name()]),
            'content' => ctrans('texts.client_payment_failure_body', ['invoice' => implode(',', $this->invoices->pluck('number')->toArray()), 'amount' => $this->getAmount()]),
            'signature' => $signature,
            'logo' => $this->company->present()->logo(),
            'settings' => $this->client->getMergedSettings(),
            'whitelabel' => $this->company->account->isPaid() ? true : false,
            'url' => $this->invoices->first()->invitations->first()->getPaymentLink(),
            'button' => ctrans('texts.pay_now'),
            'additional_info' => false,
            'company' => $this->company,
            'text_body' => ctrans('texts.client_payment_failure_body', ['invoice' => implode(',', $this->invoices->pluck('number')->toArray()), 'amount' => $this->getAmount()]),
            'additional_info' => $this->error ?? '',
        ];

        return $data;
    }
}
