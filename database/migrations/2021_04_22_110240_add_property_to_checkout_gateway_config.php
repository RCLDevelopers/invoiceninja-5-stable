<?php

use App\Models\CompanyGateway;
use App\Models\Gateway;
use App\Utils\Traits\AppSetup;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    use AppSetup;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gateway = Gateway::query()->where('key', '3758e7f7c6f4cecf0f4f348b9a00f456')->first();

        if ($gateway) {
            $fields = json_decode($gateway->fields);

            $fields->threeds = false;
            $gateway->fields = json_encode($fields);
            $gateway->save();
        }

        CompanyGateway::query()->where('gateway_key', '3758e7f7c6f4cecf0f4f348b9a00f456')->each(function ($checkout) {
            $config = json_decode(decrypt($checkout->config));

            $config->threeds = false;

            $config = encrypt(json_encode($config));

            $checkout->config = $config;
            $checkout->save();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
