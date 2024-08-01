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

namespace App\Http\Controllers\Traits;

use App\Models\User;
use App\Utils\Traits\MakesHash;
use App\Utils\Traits\UserSessionAttributes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class VerifiesUserEmail.
 */
trait VerifiesUserEmail
{
    use UserSessionAttributes;
    use MakesHash;

    /**
     * @return \Illuminate\View\View
     */
    public function confirm()
    {
        $user = User::where('confirmation_code', request()->confirmation_code)->first();

        if (! $user) {
            return $this->render('auth.confirmed', [
                'root' => 'themes',
                'message' => ctrans('texts.wrong_confirmation'),
                'redirect_url' => request()->has('react') ? config('ninja.react_url')."/#/" : url('/')]);
        }

        $user->email_verified_at = now();
        $user->save();

        if (isset($user->oauth_user_id)) {
            return $this->render('auth.confirmed', [
                'root' => 'themes',
                'message' => ctrans('texts.security_confirmation'),
                'redirect_url' => request()->has('react') ? config('ninja.react_url')."/#/" : url('/'),
            ]);
        }

        if (is_null($user->password) || empty($user->password) || Hash::check('', $user->password)) {
            return $this->render('auth.confirmation_with_password', ['root' => 'themes', 'user_id' => $user->hashed_id, 'redirect_url' => request()->has('react') ? config('ninja.react_url')."/#/" : url('/')]);
        }

        return $this->render('auth.confirmed', [
            'root' => 'themes',
            'message' => ctrans('texts.security_confirmation'),
            'redirect_url' => request()->has('react') ? config('ninja.react_url')."/#/" : url('/'),
        ]);
    }

    public function confirmWithPassword()
    {
        $user = User::where('id', $this->decodePrimaryKey(request()->user_id))->firstOrFail();

        $validator = Validator::make(request()->all(), [
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        if ($validator->fails()) {
            return back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $user->password = Hash::make(request()->password);

        $user->email_verified_at = now();
        $user->confirmation_code = null;
        $user->save();

        return $this->render('auth.confirmed', [
            'root' => 'themes',
            'message' => ctrans('texts.security_confirmation'),
            'redirect_url' => request()->has('react') ? config('ninja.react_url')."/#/" : url('/'),
        ]);
    }
}
