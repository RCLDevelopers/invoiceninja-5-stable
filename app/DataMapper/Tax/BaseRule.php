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

namespace App\DataMapper\Tax;

use App\Models\Quote;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\DataProviders\USStates;
use App\DataMapper\Tax\ZipTax\Response;

class BaseRule implements RuleInterface
{
    /** EU TAXES */
    public bool $consumer_tax_exempt = false;

    public bool $business_tax_exempt = true;

    public bool $eu_business_tax_exempt = true;

    public bool $foreign_business_tax_exempt = true;

    public bool $foreign_consumer_tax_exempt = true;

    public string $seller_region = '';

    public string $client_region = '';

    public string $client_subregion = '';

    public array $eu_country_codes = [
            'AT', // Austria
            'BE', // Belgium
            'BG', // Bulgaria
            'CY', // Cyprus
            'CZ', // Czech Republic
            'DE', // Germany
            'DK', // Denmark
            'EE', // Estonia
            'ES', // Spain
            'FI', // Finland
            'FR', // France
            'GR', // Greece
            'HR', // Croatia
            'HU', // Hungary
            'IE', // Ireland
            'IT', // Italy
            'LT', // Lithuania
            'LU', // Luxembourg
            'LV', // Latvia
            'MT', // Malta
            'NL', // Netherlands
            'PL', // Poland
            'PT', // Portugal
            'RO', // Romania
            'SE', // Sweden
            'SI', // Slovenia
            'SK', // Slovakia
    ];

    public array $region_codes = [
            'AT' => 'EU', // Austria
            'BE' => 'EU', // Belgium
            'BG' => 'EU', // Bulgaria
            'CY' => 'EU', // Cyprus
            'CZ' => 'EU', // Czech Republic
            'DE' => 'EU', // Germany
            'DK' => 'EU', // Denmark
            'EE' => 'EU', // Estonia
            'ES' => 'EU', // Spain
            'FI' => 'EU', // Finland
            'FR' => 'EU', // France
            'GR' => 'EU', // Greece
            'HR' => 'EU', // Croatia
            'HU' => 'EU', // Hungary
            'IE' => 'EU', // Ireland
            'IT' => 'EU', // Italy
            'LT' => 'EU', // Lithuania
            'LU' => 'EU', // Luxembourg
            'LV' => 'EU', // Latvia
            'MT' => 'EU', // Malta
            'NL' => 'EU', // Netherlands
            'PL' => 'EU', // Poland
            'PT' => 'EU', // Portugal
            'RO' => 'EU', // Romania
            'SE' => 'EU', // Sweden
            'SI' => 'EU', // Slovenia
            'SK' => 'EU', // Slovakia

            'US' => 'US', // United States

            'AU' => 'AU', // Australia
    ];

    /** EU TAXES */


    public string $tax_name1 = '';
    public float $tax_rate1 = 0;

    public string $tax_name2 = '';
    public float $tax_rate2 = 0;

    public string $tax_name3 = '';
    public float $tax_rate3 = 0;

    protected ?Client $client;

    public ?Response $tax_data;

    public mixed $invoice;

    private bool $should_calc_tax = true;

    public function __construct()
    {
    }

    public function init(): self
    {
        return $this;
    }

    public function shouldCalcTax(): bool
    {
        return $this->should_calc_tax;
    }
    /**
     * Initializes the tax rule for the entity.
     *
     * @param  mixed $invoice
     * @return self
     */
    public function setEntity(mixed $invoice): self
    {
        $this->invoice = $invoice;

        $this->client = $invoice->client;

        $this->resolveRegions();

        if(!$this->isTaxableRegion()) {
            return $this;
        }

        $this->configTaxData();

        $this->tax_data = new Response($this->invoice->tax_data);

        return $this;
    }

    /**
     * Configigures the Tax Data for the entity
     *
     * @return self
     */
    private function configTaxData(): self
    {
        /* We should only apply taxes for configured states */
        if(!array_key_exists($this->client->country->iso_3166_2, $this->region_codes)) {
            nlog('Automatic tax calculations not supported for this country - defaulting to company country');
        }

        /** Harvest the client_region */

        /** If the tax data is already set and the invoice is marked as sent, do not adjust the rates */
        if($this->invoice->tax_data && $this->invoice->status_id > 1) {
            return $this;
        }

        /**
         * Origin - Company Tax Data
         * Destination - Client Tax Data
         *
         */

        $tax_data = false;

        if($this->seller_region == 'US' && $this->client_region == 'US') {

            $company = $this->invoice->company;

            /** If no company tax data has been configured, lets do that now. */
            /** We should never encounter this scenario */
            if(!$company->origin_tax_data) {
                $this->should_calc_tax = false;
                return $this;
            }

            /** If we are in a Origin based state, force the company tax here */
            if($company->origin_tax_data->originDestination == 'O' && ($company->tax_data?->seller_subregion == $this->client_subregion)) {

                $tax_data = $company->origin_tax_data;

            } elseif($this->client->tax_data) {

                $tax_data = $this->client->tax_data;

            }

        }

        /** Applies the tax data to the invoice */
        if(($this->invoice instanceof Invoice || $this->invoice instanceof Quote) && $tax_data) {

            $this->invoice->tax_data = $tax_data;

            if(\DB::transactionLevel() == 0) {

                try {
                    $this->invoice->saveQuietly();
                } catch(\Exception $e) {
                    nlog("Exception:: BaseRule::" . $e->getMessage());
                }

            }
        }

        return $this;

    }


    /**
     * Resolve Regions & Subregions
     *
     * @return self
     */
    private function resolveRegions(): self
    {

        $this->client_region = $this->region_codes[$this->client->country->iso_3166_2];

        match($this->client_region) {
            'US' => $this->client_subregion = isset($this->invoice?->client?->tax_data?->geoState) ? $this->invoice->client->tax_data->geoState : $this->getUSState(),
            'EU' => $this->client_subregion = $this->client->country->iso_3166_2,
            'AU' => $this->client_subregion = 'AU',
            default => $this->client_subregion = $this->client->country->iso_3166_2,
        };

        return $this;

    }

    private function getUSState(): string
    {
        try {

            $states = USStates::$states;

            if(isset($states[$this->client->state])) {
                return $this->client->state;
            }

            return USStates::getState(strlen($this->client->postal_code ?? '') > 1 ? $this->client->postal_code : $this->client->shipping_postal_code);

        } catch (\Exception $e) {
            return 'CA';
        }
    }

    public function isTaxableRegion(): bool
    {
        return $this->client->company->tax_data->regions->{$this->client_region}->tax_all_subregions ||
        (property_exists($this->client->company->tax_data->regions->{$this->client_region}->subregions, $this->client_subregion) && ($this->client->company->tax_data->regions->{$this->client_region}->subregions->{$this->client_subregion}->apply_tax ?? false));
    }

    public function defaultForeign(): self
    {

        if($this->client_region == 'US' && isset($this->tax_data?->taxSales)) {

            $this->tax_rate1 = $this->tax_data->taxSales * 100;
            $this->tax_name1 = "{$this->tax_data->geoState} Sales Tax";

            return $this;

        } elseif($this->client_region == 'AU') { //these are defaults and are only stubbed out for now, for AU we can actually remove these

            $this->tax_rate1 = $this->client->company->tax_data->regions->AU->subregions->AU->tax_rate;
            $this->tax_name1 = $this->client->company->tax_data->regions->AU->subregions->AU->tax_name;

            return $this;
        }

        if(isset($this->client->company->tax_data->regions->{$this->client_region}->subregions->{$this->client_subregion})) {
            $this->tax_rate1 = $this->client->company->tax_data->regions->{$this->client_region}->subregions->{$this->client_subregion}->tax_rate;
            $this->tax_name1 = $this->client->company->tax_data->regions->{$this->client_region}->subregions->{$this->client_subregion}->tax_name;
        }

        return $this;
    }

    public function tax($item = null): self
    {

        if ($this->client->is_tax_exempt || !property_exists($item, 'tax_id')) {

            return $this->taxExempt($item);

        } elseif($this->client_region == $this->seller_region && $this->isTaxableRegion()) {

            $this->taxByType($item);

            return $this;

        } elseif($this->isTaxableRegion()) { //other regions outside of US

            match(intval($item->tax_id)) {
                Product::PRODUCT_TYPE_EXEMPT => $this->taxExempt($item),
                Product::PRODUCT_TYPE_REDUCED_TAX => $this->taxReduced($item),
                Product::PRODUCT_TYPE_OVERRIDE_TAX => $this->override($item),
                Product::PRODUCT_TYPE_ZERO_RATED => $this->zeroRated($item),
                default => $this->defaultForeign(),
            };

        }
        return $this;

    }

    public function zeroRated($item): self
    {
        $this->tax_rate1 = 0;
        $this->tax_name1 = ctrans('texts.zero_rated');

        return $this;
    }

    public function taxByType(mixed $type): self
    {
        return $this;
    }

    public function taxReduced($item): self
    {
        return $this;
    }

    public function taxExempt($item): self
    {
        return $this;
    }

    public function taxDigital($item): self
    {
        return $this;
    }

    public function taxService($item): self
    {
        return $this;
    }

    public function taxShipping($item): self
    {
        return $this;
    }

    public function taxPhysical($item): self
    {
        return $this;
    }

    public function default($item): self
    {
        return $this;
    }

    public function override($item): self
    {
        $this->tax_rate1 = $item->tax_rate1;
        $this->tax_name1 = $item->tax_name1;
        $this->tax_rate2 = $item->tax_rate2;
        $this->tax_name2 = $item->tax_name2;
        $this->tax_rate3 = $item->tax_rate3;
        $this->tax_name3 = $item->tax_name3;

        return $this;

    }

    public function calculateRates(): self
    {
        return $this;
    }

    public function regionWithNoTaxCoverage(string $iso_3166_2): bool
    {
        return ! in_array($iso_3166_2, array_merge($this->eu_country_codes, array_keys($this->region_codes)));
    }

}
