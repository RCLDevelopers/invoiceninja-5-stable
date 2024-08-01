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

namespace App\Models;

use App\DataMapper\CompanySettings;
use App\Models\Presenters\VendorPresenter;
use App\Services\Vendor\VendorService;
use App\Utils\Traits\AppSetup;
use App\Utils\Traits\GeneratesCounter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Laracasts\Presenter\PresentableTrait;

/**
 * App\Models\Vendor
 *
 * @property int $id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property int $user_id
 * @property int|null $assigned_user_id
 * @property int $company_id
 * @property int|null $currency_id
 * @property string|null $name
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property int|null $country_id
 * @property string|null $phone
 * @property string|null $private_notes
 * @property string|null $website
 * @property string|null $routing_id
 * @property bool $is_deleted
 * @property string|null $vat_number
 * @property string|null $transaction_name
 * @property string|null $number
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $custom_value3
 * @property string|null $custom_value4
 * @property string|null $vendor_hash
 * @property string|null $public_notes
 * @property string|null $id_number
 * @property int|null $language_id
 * @property int|null $last_login
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $assigned_user
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorContact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \App\Models\Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read mixed $hashed_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorContact> $primary_contact
 * @property-read int|null $primary_contact_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel exclude($columns)
 * @method static \Database\Factories\VendorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor filter(\App\Filters\QueryFilters $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel scope()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorContact> $contacts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorContact> $primary_contact
 * @mixin \Eloquent
 */
class Vendor extends BaseModel
{
    use SoftDeletes;
    use Filterable;
    use GeneratesCounter;
    use PresentableTrait;
    use AppSetup;

    protected $fillable = [
        'name',
        'assigned_user_id',
        'id_number',
        'vat_number',
        'phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'public_notes',
        'currency_id',
        'website',
        'transaction_name',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'number',
        'language_id',
        'classification',
    ];

    protected $casts = [
        'country_id' => 'string',
        'currency_id' => 'string',
        'is_deleted' => 'boolean',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
        'last_login' => 'timestamp',
    ];

    protected $touches = [];

    protected $with = [
        'contacts.company',
    ];

    protected $presenter = VendorPresenter::class;

    public function getEntityType()
    {
        return self::class;
    }

    public function primary_contact(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VendorContact::class)->where('is_primary', true);
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function assigned_user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'id')->withTrashed();
    }

    public function contacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VendorContact::class)->orderBy('is_primary', 'desc');
    }

    public function activities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function getCurrencyCode(): string
    {
        if ($this->currency()) {
            return $this->currency()->code;
        }

        return 'USD';
    }

    public function currency()
    {
        
        /** @var \Illuminate\Support\Collection<\App\Models\Currency> */
        $currencies = app('currencies');

        if (! $this->currency_id) {
            return $this->company->currency();
        }

        return $currencies->first(function ($item) {
            return $item->id == $this->currency_id;
        });
    }

    public function timezone()
    {
        return $this->company->timezone();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function translate_entity(): string
    {
        return ctrans('texts.vendor');
    }

    public function setCompanyDefaults($data, $entity_name): array
    {
        $defaults = [];

        if (! (array_key_exists('terms', $data) && strlen($data['terms']) > 1)) {
            $defaults['terms'] = $this->getSetting($entity_name.'_terms');
        } elseif (array_key_exists('terms', $data)) {
            $defaults['terms'] = $data['terms'];
        }

        if (! (array_key_exists('footer', $data) && strlen($data['footer']) > 1)) {
            $defaults['footer'] = $this->getSetting($entity_name.'_footer');
        } elseif (array_key_exists('footer', $data)) {
            $defaults['footer'] = $data['footer'];
        }

        if (strlen($this->public_notes) >= 1) {
            $defaults['public_notes'] = $this->public_notes;
        }

        return $defaults;
    }

    /**
     * Returns a vendor settings proxying company setting
     *
     * @param string $setting
     * @return mixed
     */
    public function getSetting($setting): mixed
    {
        if ((property_exists($this->company->settings, $setting) != false) && (isset($this->company->settings->{$setting}) !== false)) {
            return $this->company->settings->{$setting};
        } elseif (property_exists(CompanySettings::defaults(), $setting)) {
            return CompanySettings::defaults()->{$setting};
        }

        return '';
    }

    public function getMergedSettings(): object
    {
        return $this->company->settings;
    }

    public function purchase_order_filepath($invitation): string
    {
        $contact_key = $invitation->contact->contact_key;

        return $this->company->company_key.'/'.$this->vendor_hash.'/'.$contact_key.'/purchase_orders/';
    }

    public function locale(): string
    {
        return $this->language ? $this->language->locale : $this->company->locale();
    }

    public function language(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function date_format(): string
    {
        return $this->company->date_format();
    }

    public function backup_path(): string
    {
        return $this->company->company_key.'/'.$this->vendor_hash.'/backups';
    }

    public function service()
    {
        return new VendorService($this);
    }
}
