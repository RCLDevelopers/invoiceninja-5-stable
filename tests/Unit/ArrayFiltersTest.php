<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace Tests\Unit;

use App\Models\Design;
use App\Models\Payment;
use App\Models\User;
use Tests\TestCase;

/**
 * @test
 */
class ArrayFiltersTest extends TestCase
{
    private string $import_version = '';
     
    private array $version_keys = [
        'baseline' =>[],
        '5.7.34' => [
            Payment::class => [
                'is_deleted',
                'amount',
            ]
        ],
        '5.7.35' => [
            Payment::class => [
                'date',
                'transaction_reference',
            ],
            User::class => [
                'user_logged_in_notification',
                'first_name',
                'last_name',
            ],
            Design::class => [
                'is_template',
            ]
        ],
        '5.7.36' => [
            Payment::class => [
                'type_id',
                'status_id',
            ],
        ],
        '5.7.37' => [
            Payment::class => [
                'currency_id',
                'hashed_id',
            ],
        ],
    ];

    protected function setUp() :void
    {
        parent::setUp();
    }

    public function testPaymentFilterFactory()
    {
        $p = Payment::factory()->make()->toArray();

        $this->assertIsArray($p);
    }

    public function testPaymentUnsetProps()
    {
        $p = Payment::factory()->make()->toArray();

        $version = '5.7.36';
        $current_version = config('ninja.app_version');

        $this->assertNotEquals($current_version, $version);

        $index = 0;
        $version_index = 0;

        foreach($this->version_keys as $key => $value) {
            if($version == $key) {
                $version_index = $index;
            }
        
            $index++;
        }

        $this->assertEquals(3, $version_index);

        $filters = collect($this->version_keys)->slice($version_index);

        $this->assertEquals(2, $filters->count());

        $x = collect($p)->diffKeys($filters->flatten()->flip());

        $this->assertEquals(4, $x->count());
    }

    public function testPaymentUnsetPropsScenario2()
    {
        $p = Payment::factory()->make()->toArray();

        $version = '5.7.35';
        $current_version = config('ninja.app_version');

        $this->assertNotEquals($current_version, $version);

        $index = 0;
        $version_index = 0;

        foreach($this->version_keys as $key => $value) {
            if($version == $key) {
                $version_index = $index;
            }
        
            $index++;
        }

        $this->assertEquals(2, $version_index);
        
        $index = 0;
        $version_index = 0;

        $filters = collect($this->version_keys)
        ->map(function ($value, $key) use ($version, &$version_index, &$index) {
            if($version == $key) {
                $version_index = $index;
            }
                    
            $index++;
            return $value;

        })
        ->slice($version_index)
        ->pluck(Payment::class);

        $this->assertEquals(3, $filters->count());

        $x = collect($p)->diffKeys($filters->flatten()->flip());

        $this->assertEquals(2, $x->count());
    }

    public function testWhenScenario()
    {
        $p = Payment::factory()->make()->toArray();

        $version = '5.7.35';
        $current_version = '5.7.35';

        $filters = collect($this->version_keys)
        ->map(function ($value, $key) use ($version, &$version_index, &$index) {
            if($version == $key) {
                $version_index = $index;
            }
                                
            $index++;
            return $value;

        })
        ->slice($version_index)
        ->pluck(Payment::class);

        $this->assertEquals(3, $filters->count());
    }

    public function testWhenScenario2()
    {
        $p = Payment::factory()->make()->toArray();

        $version = '5.7.33';
        $current_version = '5.7.35';

        $filters = collect($this->version_keys)
        ->map(function ($value, $key) use ($version, &$version_index, &$index) {
            if($version == $key) {
                $version_index = $index;
                nlog("version = {$version_index}");
            }
            $index++;
            return $value;

        })
        ->slice($version_index ?? 0)
        ->pluck(Payment::class);
        
        $x = collect($p)->diffKeys($filters->filter()->flatten()->flip());

        $this->assertEquals(5, $filters->count());
    }


    private function filterArray($class, array $obj_array)
    {
        $index = 0;
        $version_index = 0;

        $filters = collect($this->version_keys)
             ->map(function ($value, $key) use (&$version_index, &$index) {
                 if($this->import_version == $key) {
                     $version_index = $index;
                 }
                                                    
                 $index++;
                 return $value;

             })
             ->when($version_index == 0, function ($collection) {
                 return collect([]);
             })
             ->when($version_index > 0, function ($collection) use (&$version_index, $class) {
                 return $collection->slice($version_index)->pluck($class)->filter();
             });
        
        return collect($obj_array)->diffKeys($filters->flatten()->flip())->toArray();

        // return $filters->count() > 0 ?  collect($obj_array)->diffKeys($filters->flatten()->flip())->toArray() : $obj_array;
 
    }

    public function testFilterArrayOne()
    {
        $u = User::factory()->make()->toArray();

        $prop_count = count($u);

        $this->import_version = '5.7.42';

        $filtered_u = $this->filterArray(User::class, $u);

        $this->assertCount($prop_count, $filtered_u);
    }

}
