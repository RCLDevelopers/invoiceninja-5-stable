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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientPortal\RegisterRequest;
use App\Livewire\BillingPortal\Authentication\ClientRegisterService;
use App\Models\Company;
use App\Utils\Ninja;
use App\Utils\Traits\GeneratesCounter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ContactRegisterController extends Controller
{
    use GeneratesCounter;

    public function __construct()
    {
        $this->middleware(['guest']);
    }

    public function showRegisterForm(string $company_key = '')
    {
        if (strlen($company_key) > 2) {
            $key = $company_key;
        } else {
            $key = request()->session()->has('company_key') ? request()->session()->get('company_key') : $company_key;
        }

        /** @var \App\Models\Company $company **/
        $company = Company::where('company_key', $key)->firstOrFail();

        App::forgetInstance('translator');
        $t = app('translator');
        $t->replace(Ninja::transformTranslations($company->settings));

        return render('auth.register', ['register_company' => $company, 'account' => $company->account, 'submitsForm' => false]);
    }

    public function register(RegisterRequest $request)
    {
        $request->merge(['company' => $request->company()]);

        $service = new ClientRegisterService(
            company: $request->company(),
        );

        $client = $service->createClient($request->all());
        $client_contact = $service->createClientContact($request->all(), $client);

        Auth::guard('contact')->loginUsingId($client_contact->id, true);

        return redirect()->intended(route('client.dashboard'));
    }
}
