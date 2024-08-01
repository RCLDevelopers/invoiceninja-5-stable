<?php
/**
 * Quote Ninja (https://quoteninja.com).
 *
 * @link https://github.com/quoteninja/quoteninja source repository
 *
 * @copyright Copyright (c) 2022. Quote Ninja LLC (https://quoteninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Services\Quote;

use App\Utils\Ninja;
use App\Models\Quote;
use App\Models\Webhook;
use Illuminate\Http\Request;
use App\Jobs\Entity\EmailEntity;
use App\Services\AbstractService;
use App\Events\Quote\QuoteWasEmailed;
use App\Utils\Traits\GeneratesCounter;

class TriggeredActions extends AbstractService
{
    use GeneratesCounter;

    private $request;

    private $quote;

    public function __construct(Quote $quote, Request $request)
    {
        $this->request = $request;

        $this->quote = $quote;
    }

    public function run()
    {
        if ($this->request->has('send_email') && $this->request->input('send_email') == 'true') {
            $this->quote = $this->quote->service()->markSent()->save();
            $this->sendEmail();
        }

        if ($this->request->has('mark_sent') && $this->request->input('mark_sent') == 'true') {
            $this->quote = $this->quote->service()->markSent()->save();
        }

        if ($this->request->has('convert') && $this->request->input('convert') == 'true') {
            $this->quote = $this->quote->service()->convert()->save();
        }

        // if ($this->request->has('approve') && $this->request->input('approve') == 'true' && in_array($this->quote->status_id, [Quote::STATUS_SENT, Quote::STATUS_DRAFT])) {
        if ($this->request->has('approve') && $this->request->input('approve') == 'true') {
            $this->quote = $this->quote->service()->approveWithNoCoversion()->save();
        }

        if ($this->request->has('save_default_footer') && $this->request->input('save_default_footer') == 'true') {
            $company = $this->quote->company;
            $settings = $company->settings;
            $settings->quote_footer = $this->quote->footer;
            $company->settings = $settings;
            $company->save();
        }

        if ($this->request->has('save_default_terms') && $this->request->input('save_default_terms') == 'true') {
            $company = $this->quote->company;
            $settings = $company->settings;
            $settings->quote_terms = $this->quote->terms;
            $company->settings = $settings;
            $company->save();
        }



        return $this->quote;
    }

    private function sendEmail()
    {
        $reminder_template = $this->quote->calculateTemplate('quote');
        // $reminder_template = 'email_template_quote';

        $this->quote->invitations->load('contact.client.country', 'quote.client.country', 'quote.company')->each(function ($invitation) use ($reminder_template) {
            EmailEntity::dispatch($invitation, $this->quote->company, $reminder_template);
        });

        if ($this->quote->invitations->count() > 0) {
            event(new QuoteWasEmailed($this->quote->invitations->first(), $this->quote->company, Ninja::eventVars(), 'quote'));
            $this->quote->sendEvent(Webhook::EVENT_SENT_QUOTE, "client");

        }
    }
}
