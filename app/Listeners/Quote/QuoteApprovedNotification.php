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

namespace App\Listeners\Quote;

use App\Jobs\Mail\NinjaMailer;
use App\Jobs\Mail\NinjaMailerJob;
use App\Jobs\Mail\NinjaMailerObject;
use App\Libraries\MultiDB;
use App\Mail\Admin\QuoteApprovedObject;
use App\Utils\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuoteApprovedNotification implements ShouldQueue
{
    use UserNotifies;

    public $delay = 8;

    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        MultiDB::setDb($event->company->db);

        $first_notification_sent = true;

        $quote = $event->quote;


        /* We loop through each user and determine whether they need to be notified */
        foreach ($event->company->company_users as $company_user) {
            /* The User */
            $user = $company_user->user;

            if (! $user) {
                continue;
            }

            /* Returns an array of notification methods */
            $methods = $this->findUserNotificationTypes($quote->invitations()->first(), $company_user, 'quote', ['all_notifications', 'quote_approved', 'quote_approved_all', 'quote_approved_user']);

            /* If one of the methods is email then we fire the EntitySentMailer */
            if (($key = array_search('mail', $methods)) !== false) {
                unset($methods[$key]);

                $nmo = new NinjaMailerObject();
                $nmo->mailable = new NinjaMailer((new QuoteApprovedObject($quote, $event->company, $company_user->portalType()))->build());
                $nmo->company = $quote->company;
                $nmo->settings = $quote->company->settings;

                $nmo->to_user = $user;

                (new NinjaMailerJob($nmo))->handle();

                $nmo = null;

                /* This prevents more than one notification being sent */
                $first_notification_sent = false;
            }
        }
    }
}
