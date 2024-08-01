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

namespace App\Http\Requests\TwoFactor;

use App\Http\Requests\Request;

class EnableTwoFactorRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
        ;
    }

    public function rules()
    {
        return [
            'secret' => 'bail|required|string',
            'one_time_password' => 'bail|required|string',
        ];
    }

    public function prepareForValidation()
    {
    }
}
