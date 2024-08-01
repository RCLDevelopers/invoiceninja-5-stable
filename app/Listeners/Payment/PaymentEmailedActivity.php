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

namespace App\Listeners\Payment;

use App\Libraries\MultiDB;
use App\Models\Activity;
use App\Repositories\ActivityRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentEmailedActivity implements ShouldQueue
{
    protected $activity_repo;

    /**
     * Create the event listener.
     *
     * @param ActivityRepository $activity_repo
     */
    public function __construct(ActivityRepository $activity_repo)
    {
        $this->activity_repo = $activity_repo;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle($event)
    {
        MultiDB::setDb($event->company->db);

        $fields = new \stdClass();

        $user_id = isset($event->event_vars['user_id']) ? $event->event_vars['user_id'] : $event->payment->user_id;

        $fields->user_id = $user_id;
        $fields->client_id = $event->payment->client_id;
        $fields->client_contact_id = $event->contact->id;
        $fields->company_id = $event->payment->company_id;
        $fields->activity_type_id = Activity::PAYMENT_EMAILED;
        $fields->payment_id = $event->payment->id;

        $this->activity_repo->save($fields, $event->payment, $event->event_vars);
    }
}
