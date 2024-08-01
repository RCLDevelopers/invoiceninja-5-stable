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

namespace App\Services\Report;

use App\Export\CSV\BaseExport;
use App\Libraries\MultiDB;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Utils\Ninja;
use App\Utils\Number;
use App\Utils\Traits\MakesDates;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use League\Csv\Writer;

class ARDetailReport extends BaseExport
{
    use MakesDates;
    //Date
    //Invoice #
    //Status
    //Customer
    //Age - Days
    //Amount
    //Balance

    public Writer $csv;

    public string $date_key = 'created_at';

    public array $report_keys = [
        'date',
        'due_date',
        'invoice_number',
        'status',
        'client_name',
        'client_number',
        'id_number',
        'age',
        'amount',
        'balance',
    ];

    /**
        @param array $input
        [
            'date_range',
            'start_date',
            'end_date',
            'clients',
            'client_id',
        ]
    */
    public function __construct(public Company $company, public array $input)
    {
    }

    public function run()
    {
        MultiDB::setDb($this->company->db);
        App::forgetInstance('translator');
        App::setLocale($this->company->locale());
        $t = app('translator');
        $t->replace(Ninja::transformTranslations($this->company->settings));

        $this->csv = Writer::createFromString();
        \League\Csv\CharsetConverter::addTo($this->csv, 'UTF-8', 'UTF-8');

        $this->csv->insertOne([]);
        $this->csv->insertOne([]);
        $this->csv->insertOne([]);
        $this->csv->insertOne([]);
        $this->csv->insertOne([ctrans('texts.aged_receivable_detailed_report')]);
        $this->csv->insertOne([ctrans('texts.created_on'),' ',$this->translateDate(now()->format('Y-m-d'), $this->company->date_format(), $this->company->locale())]);

        if (count($this->input['report_keys']) == 0) {
            $this->input['report_keys'] = $this->report_keys;
        }

        $this->csv->insertOne($this->buildHeader());

        $query = Invoice::query()
                ->whereIn('invoices.status_id', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])
                ->withTrashed()
                ->whereHas('client', function ($query) {
                    $query->where('is_deleted', 0);
                })
                ->where('invoices.company_id', $this->company->id)
                ->where('invoices.is_deleted', 0)
                ->where('invoices.balance', '>', 0)
                ->orderBy('invoices.due_date', 'ASC');

        $query = $this->addDateRange($query, 'invoices');

        $query = $this->filterByClients($query);

        $query->cursor()
            ->each(function ($invoice) {
                $this->csv->insertOne($this->buildRow($invoice));
            });

        return $this->csv->toString();
    }

    private function buildRow(Invoice $invoice): array
    {
        $client = $invoice->client;

        return [
            $this->translateDate($invoice->date, $this->company->date_format(), $this->company->locale()),
            $this->translateDate($invoice->due_date, $this->company->date_format(), $this->company->locale()),
            $invoice->number,
            $invoice->stringStatus($invoice->status_id),
            $client->present()->name(),
            $client->number,
            $client->id_number,
            intval(abs(Carbon::parse($invoice->due_date)->diffInDays(now()))),
            Number::formatMoney($invoice->amount, $client),
            Number::formatMoney($invoice->balance, $client),
        ];
    }

    public function buildHeader(): array
    {
        $header = [];

        foreach ($this->input['report_keys'] as $value) {

            $header[] = ctrans("texts.{$value}");
        }

        return $header;
    }

}
