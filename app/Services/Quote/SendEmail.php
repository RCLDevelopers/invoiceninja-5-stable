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

namespace App\Services\Quote;

use App\Models\Webhook;
use App\Models\ClientContact;
use App\Jobs\Entity\EmailEntity;

class SendEmail
{
    public $quote;

    protected $reminder_template;

    protected $contact;

    public function __construct($quote, $reminder_template = null, ClientContact $contact = null)
    {
        $this->quote = $quote;

        $this->reminder_template = $reminder_template;

        $this->contact = $contact;
    }

    /**
     * Builds the correct template to send.
     * @return void
     */
    public function run()
    {

        if (! $this->reminder_template) {
            $this->reminder_template = $this->quote->calculateTemplate('quote');
        }

        $this->quote->service()->markSent()->save();

        $this->quote->invitations->each(function ($invitation) {
            if (! $invitation->contact->trashed() && $invitation->contact->email) {
                EmailEntity::dispatch($invitation, $invitation->company, $this->reminder_template);
            }
        });

        $this->quote->sendEvent(Webhook::EVENT_SENT_QUOTE, "client");

    }
}
