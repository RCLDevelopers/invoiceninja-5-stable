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

namespace Tests\Feature;

use App\Models\TaskStatus;
use App\Utils\Traits\MakesHash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
use Tests\MockAccountData;
use Tests\TestCase;

/**
 * @test
 * @covers App\Http\Controllers\TaskStatusController
 */
class TaskStatusApiTest extends TestCase
{
    use MakesHash;
    use DatabaseTransactions;
    use MockAccountData;

    public $faker;

    protected function setUp() :void
    {
        parent::setUp();

        $this->makeTestData();

        Session::start();

        $this->faker = \Faker\Factory::create();

        Model::reguard();
    }

    public function testSorting()
    {
        TaskStatus::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id
        ]);
        
        
        $t = TaskStatus::where('company_id', '=', $this->company->id)->orderBy('id', 'desc');
        
        $this->assertEquals(10, $t->count());
        $task_status = $t->first();

        $id = $task_status->id;
        
        nlog("setting {$id} to index 1");

        $data = [
            'status_order' => 1,
        ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->put('/api/v1/task_statuses/'.$task_status->hashed_id, $data);

        $t = TaskStatus::where('company_id', '=', $this->company->id)->orderBy('status_order', 'asc')->first();

        $this->assertEquals($id, $t->id);
        
    }

    public function testTaskStatusGetFilter()
    {
        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->get('/api/v1/task_statuses?filter=xx');

        $response->assertStatus(200);
    }

    public function testTaskStatusPost()
    {
        $data = [
            'name' => $this->faker->firstName(),
        ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->post('/api/v1/task_statuses', $data);

        $response->assertStatus(200);
    }

    public function testTaskStatusPut()
    {
        $data = [
            'name' => $this->faker->firstName(),
        ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->put('/api/v1/task_statuses/'.$this->encodePrimaryKey($this->task_status->id), $data);

        $response->assertStatus(200);
    }

    public function testTaskStatusGet()
    {
        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->get('/api/v1/task_statuses/'.$this->encodePrimaryKey($this->task_status->id));

        $response->assertStatus(200);
    }

    public function testTaskStatusNotArchived()
    {
        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->get('/api/v1/task_statuses/'.$this->encodePrimaryKey($this->task_status->id));

        $arr = $response->json();

        $this->assertEquals(0, $arr['data']['archived_at']);
    }

    public function testTaskStatusArchived()
    {
        $data = [
            'ids' => [$this->encodePrimaryKey($this->task_status->id)],
        ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->post('/api/v1/task_statuses/bulk?action=archive', $data);

        $arr = $response->json();

        $this->assertNotNull($arr['data'][0]['archived_at']);
    }

    public function testTaskStatusRestored()
    {
        $data = [
            'ids' => [$this->encodePrimaryKey($this->task_status->id)],
        ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->post('/api/v1/task_statuses/bulk?action=restore', $data);

        $arr = $response->json();

        $this->assertEquals(0, $arr['data'][0]['archived_at']);
    }

    public function testTaskStatusDeletedFromDELETEROute()
    {
        // $data = [
        //     'ids' => [$this->encodePrimaryKey($this->task_status->id)],
        // ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->delete('/api/v1/task_statuses/'.$this->encodePrimaryKey($this->task_status->id));

        $arr = $response->json();
        // nlog($arr);

        $this->assertTrue($arr['data']['is_deleted']);
    }

    public function testTaskStatusDeleted()
    {
        $data = [
            'ids' => [$this->encodePrimaryKey($this->task_status->id)],
        ];

        $response = $this->withHeaders([
            'X-API-SECRET' => config('ninja.api_secret'),
            'X-API-TOKEN' => $this->token,
        ])->post('/api/v1/task_statuses/bulk?action=delete', $data);

        $arr = $response->json();

        $this->assertTrue($arr['data'][0]['is_deleted']);
    }
}
