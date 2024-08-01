@extends('portal.ninja2020.layout.payments', ['gateway_title' => ctrans('texts.credit_card'), 'card_title' =>
ctrans('texts.credit_card')])

@section('gateway_head')
    <meta name="mollie-testmode" content="{{ $gateway->company_gateway->getConfigField('testMode') }}">
    <meta name="mollie-profileId" content="{{ $gateway->company_gateway->getConfigField('profileId') }}">

    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    
    <script src="{{ asset('build/public/js/card-js.min.js/card-js.min.js') }}"></script>
    <link href="{{ asset('build/public/css/card-js.min.css/card-js.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('gateway_content')
    <form action="{{ route('client.payments.response') }}" method="post" id="server-response">
        @csrf
        <input type="hidden" name="gateway_response">
        <input type="hidden" name="store_card">
        <input type="hidden" name="payment_hash" value="{{ $payment_hash }}">

        <input type="hidden" name="company_gateway_id" value="{{ $gateway->getCompanyGatewayId() }}">
        <input type="hidden" name="payment_method_id" value="{{ $payment_method_id }}">

        <input type="hidden" name="token">
    </form>

    <div class="alert alert-failure mb-4" hidden id="errors"></div>

    @component('portal.ninja2020.components.general.card-element', ['title' => ctrans('texts.payment_type')])
        {{ ctrans('texts.credit_card') }}
    @endcomponent

    @include('portal.ninja2020.gateways.includes.payment_details')

    @component('portal.ninja2020.components.general.card-element', ['title' => ctrans('texts.pay_with')])
        @if (count($tokens) > 0)
            @foreach ($tokens as $token)
                <label class="mr-4">
                    <input type="radio" data-token="{{ $token->token }}" name="payment-type"
                        class="form-radio cursor-pointer toggle-payment-with-token" />
                    <span class="ml-1 cursor-pointer">**** {{ $token->meta?->last4 }}</span>
                </label>
            @endforeach
        @endif

        <label>
            <input type="radio" id="toggle-payment-with-credit-card" class="form-radio cursor-pointer" name="payment-type"
                checked />
            <span class="ml-1 cursor-pointer">{{ __('texts.new_card') }}</span>
        </label>
    @endcomponent

    @component('portal.ninja2020.components.general.card-element-single')
        <div class="flex flex-col" id="mollie--payment-container">
            <label for="card-number">
                <span class="text-xs text-gray-900 uppercase">{{ ctrans('texts.card_number') }}</span>
                <div class="input w-full" type="text" id="card-number"></div>
                <div class="text-xs text-red-500 mt-1 block" id="card-number-error"></div>
            </label>

            <label for="card-holder" class="block mt-2">
                <span class="text-xs text-gray-900 uppercase">{{ ctrans('texts.name') }}</span>
                <div class="input w-full" type="text" id="card-holder"></div>
                <div class="text-xs text-red-500 mt-1 block" id="card-holder-error"></div>
            </label>

            <div class="grid grid-cols-12 gap-4 mt-2">
                <label for="expiry-date" class="col-span-4">
                    <span class="text-xs text-gray-900 uppercase">{{ ctrans('texts.expiry_date') }}</span>
                    <div class="input w-full" type="text" id="expiry-date"></div>
                    <div class="text-xs text-red-500 mt-1 block" id="expiry-date-error"></div>
                </label>

                <label for="cvv" class="col-span-8">
                    <span class="text-xs text-gray-900 uppercase">{{ ctrans('texts.cvv') }}</span>
                    <div class="input w-full border" type="text" id="cvv"></div>
                    <div class="text-xs text-red-500 mt-1 block" id="cvv-error"></div>
                </label>
            </div>
        </div>
    @endcomponent

    @include('portal.ninja2020.gateways.includes.save_card')
    @include('portal.ninja2020.gateways.includes.pay_now')
@endsection

@section('gateway_footer')
    <script src="https://js.mollie.com/v1/mollie.js"></script>
    @vite('resources/js/clients/payments/mollie-credit-card.js')
@endsection
