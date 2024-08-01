@extends('portal.ninja2020.layout.payments', ['gateway_title' => 'ACSS', 'card_title' => 'ACSS'])

@section('gateway_head')

    @if($company_gateway->getConfigField('account_id'))
    <meta name="stripe-account-id" content="{{ $company_gateway->getConfigField('account_id') }}">
    <meta name="stripe-publishable-key" content="{{ config('ninja.ninja_stripe_publishable_key') }}">
    @else
    <meta name="stripe-publishable-key" content="{{ $company_gateway->getPublishableKey() }}">
    @endif
    <meta name="only-authorization" content="true">

@endsection

@section('gateway_content')
    <form action="{{ route('client.payment_methods.store', ['method' => App\Models\GatewayType::ACSS]) }}" method="post" id="server_response">
        @csrf
        <input type="hidden" name="company_gateway_id" value="{{ $company_gateway->gateway_id }}">
        <input type="hidden" name="payment_method_id" value="1">
        <input type="hidden" name="gateway_response" id="gateway_response">
        <input type="hidden" name="is_default" id="is_default">
        <input type="hidden" name="post_auth_response" value="{{ $post_auth_response }}">
    </form>

    <div class="alert alert-failure mb-4" hidden id="errors"></div>
        @component('portal.ninja2020.components.general.card-element-single', ['title' => 'SEPA', 'show_title' => false])
        <p>By clicking submit, you accept this Agreement and authorize {{ $company->present()->name() }} to debit the specified bank account for any amount owed for charges arising from the use of services and/or purchase of products.</p>
<br>
        <p>Payments will be debited from the specified account when an invoice becomes due.</p>
<br>
        <p>Where a scheduled debit date is not a business day, {{ $company->present()->name() }} will debit on the next business day.</p>
<br>
        <p>You agree that any payments due will be debited from your account immediately upon acceptance of this Agreement and that confirmation of this Agreement may be sent within 5 (five) days of acceptance of this Agreement. You further agree to be notified of upcoming debits up to 1 (one) day before payments are collected.</p>
<br>
        <p>You have certain recourse rights if any debit does not comply with this agreement. For example, you have the right to receive reimbursement for any debit that is not authorized or is not consistent with this PAD Agreement. To obtain more information on your recourse rights, contact your financial institution.</p>
<br>
        <p>You may amend or cancel this authorization at any time by providing the merchant with thirty (30) days notice at {{ $company->owner()->email }}. To obtain a sample cancellation form, or further information on cancelling a PAD agreement, please contact your financial institution.</p>
<br>
        <p>{{ $company->present()->name() }} partners with Stripe to provide payment processing.</p>
    

        <div>
            <label for="acss-name">
                <input class="input w-full" id="acss-name" type="text" placeholder="{{ ctrans('texts.bank_account_holder') }}" value="{{ $client->present()->name() }}">
            </label>
            <label for="acss-email" >
                <input class="input w-full" id="acss-email-address" type="email" placeholder="{{ ctrans('texts.email') }}" value="{{ $client->present()->email() }}">
            </label>
        </div>

        @endcomponent
    @component('portal.ninja2020.gateways.includes.pay_now', ['id' => 'authorize-acss'])
        {{ ctrans('texts.add_payment_method') }}
    @endcomponent
@endsection

@section('gateway_footer')
    <script src="https://js.stripe.com/v3/"></script>
    
    <script>
    
    @if($company_gateway->getConfigField('account_id'))
      var stripe = Stripe('{{ config('ninja.ninja_stripe_publishable_key') }}', {
        stripeAccount: '{{ $company_gateway->getConfigField('account_id') }}',
      });
    @else
      var stripe = Stripe('{{ $company_gateway->getPublishableKey() }}', {
      });
    @endif

        const accountholderName = document.getElementById('acss-name');
        const email = document.getElementById('acss-email-address');
        const submitButton = document.getElementById('authorize-acss');
        const clientSecret = "{{ $pi_client_secret }}";
        const errors = document.getElementById('errors');

        submitButton.addEventListener('click', async (event) => {
        event.preventDefault();
        errors.hidden = true;
        submitButton.disabled = true;

        const validEmailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

            if(email.value.length < 3 || ! email.value.match(validEmailRegex)){
                errors.textContent = "Please enter a valid email address.";
                errors.hidden = false;
                submitButton.disabled = false;
                return;
            }


            if(accountholderName.value.length < 3){
                errors.textContent = "Please enter a name for the account holder.";
                errors.hidden = false;
                submitButton.disabled = false;
                return;
            }

        const {setupIntent, error} = await stripe.confirmAcssDebitSetup(
            clientSecret,
            {
            payment_method: {
                billing_details: {
                name: accountholderName.value,
                email: email.value,
                },
            },
            }
        );

            // Handle next step based on SetupIntent's status.
            document.getElementById("gateway_response").value = JSON.stringify( setupIntent ?? error );
            document.getElementById("server_response").submit();

        });

    </script>


@endsection

