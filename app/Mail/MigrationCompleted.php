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

namespace App\Mail;

use App\Libraries\MultiDB;
use App\Models\Company;
use App\Utils\Ninja;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;

class MigrationCompleted extends Mailable
{
    public ?Company $company;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public int $company_id, public string $db, public string $check_data = '')
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        MultiDB::setDb($this->db);
        $this->company = Company::query()->find($this->company_id);

        App::forgetInstance('translator');
        $t = app('translator');
        $t->replace(Ninja::transformTranslations($this->company->settings));
        App::setLocale($this->company->getLocale());

        $data['settings'] = $this->company->settings;
        $data['company'] = $this->company->fresh();
        $data['whitelabel'] = $this->company->account->isPaid() ? true : false;
        $data['check_data'] = $this->check_data ?: '';
        $data['logo'] = $this->company->present()->logo();
        $data['url'] = Ninja::isHosted() ? config('ninja.react_url') : config('ninja.app_url');


        $data = array_merge($data, [
            'logo' => $this->company->present()->logo(),
            'settings' => $this->company->settings,
            'company' => $this->company,
            'client_count' => $this->company->clients()->count(),
            'product_count' => $this->company->products()->count(),
            'invoice_count' => $this->company->invoices()->count(),
            'quote_count' => $this->company->quotes()->count(),
            'credit_count' => $this->company->credits()->count(),
            'project_count' => $this->company->projects()->count(),
            'task_count' => $this->company->tasks()->count(),
            'vendor_count' => $this->company->vendors()->count(),
            'payment_count' => $this->company->payments()->count(),
            'recurring_invoice_count' => $this->company->recurring_invoices()->count(),
            'expense_count' => $this->company->expenses()->count(),
            'company_gateway_count' => $this->company->company_gateways()->count(),
            'client_gateway_token_count' => $this->company->client_gateway_tokens()->count(),
            'tax_rate_count' => $this->company->tax_rates()->count(),
            'document_count' => $this->company->documents()->count(),

        ]);

        $result = $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->text('email.import.completed_text', $data)
                    ->view('email.import.completed', $data);

        return $result;
    }
}
