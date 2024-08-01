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

namespace App\Http\Requests\Setup;

use App\Http\Requests\Request;

class StoreSetupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            /*System*/
            'url'              => 'required',
            /*Mail driver*/
            'privacy_policy'   => 'required',
            'terms_of_service' => 'required',
            'first_name'       => 'required',
            'last_name'        => 'required',
            'email'            => 'required|email',
            'password'         => 'required',
        ];

        if (! config('ninja.preconfigured_install')) {
            $rules = array_merge($rules, [
                /*Database*/
                'db_host'     => 'required',
                'db_database' => 'required',
                'db_username' => 'required',
                'db_password' => '',
            ]);
        }

        return $rules;
    }

    public function prepareForValidation()
    {
        $input = $this->all();

        $input['user_agent'] = request()->server('HTTP_USER_AGENT');

        $this->replace($input);
    }
}
