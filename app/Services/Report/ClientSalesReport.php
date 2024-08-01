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
use Illuminate\Support\Facades\App;
use League\Csv\Writer;

class ClientSalesReport extends BaseExport
{
    use MakesDates;
    //Name
    //Invoice count
    //Amount
    //Amount with Tax

    public Writer $csv;

    public string $date_key = 'created_at';

    public array $report_keys = [
        'client_name',
        'client_number',
        'id_number',
        'invoices',
        'amount',
        'balance',
        'total_taxes',
        'amount_paid',
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
        $this->csv->insertOne([ctrans('texts.client_sales_report')]);
        $this->csv->insertOne([ctrans('texts.created_on'),' ',$this->translateDate(now()->format('Y-m-d'), $this->company->date_format(), $this->company->locale())]);

        if (count($this->input['report_keys']) == 0) {
            $this->input['report_keys'] = $this->report_keys;
        }

        $this->csv->insertOne($this->buildHeader());

        Client::query()
            ->where('company_id', $this->company->id)
            ->where('is_deleted', 0)
            ->orderBy('balance', 'desc')
            ->cursor()
            ->each(function ($client) {

                $this->csv->insertOne($this->buildRow($client));

            });

        return $this->csv->toString();

    }

    private function buildRow(Client $client): array
    {
        $query = Invoice::query()->where('client_id', $client->id)
                                ->whereIn('status_id', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_PAID]);

        $query = $this->addDateRange($query, 'invoices');

        $amount = $query->sum('amount');
        $balance = $query->sum('balance');
        $paid = $amount - $balance;

        return [
            $client->present()->name(),
            $client->number,
            $client->id_number,
            $query->count(),
            Number::formatMoney($amount, $client),
            Number::formatMoney($balance, $client),
            Number::formatMoney($query->sum('total_taxes'), $client),
            Number::formatMoney($amount - $balance, $client),

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
