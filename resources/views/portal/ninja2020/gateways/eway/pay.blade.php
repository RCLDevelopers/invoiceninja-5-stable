@extends('portal.ninja2020.layout.payments', ['gateway_title' => ctrans('texts.credit_card'), 'card_title' =>
ctrans('texts.credit_card')])

@section('gateway_head')
    <meta name="public-api-key" content="{{ $public_api_key }}">
    <meta name="translation-card-name" content="{{ ctrans('texts.cardholder_name') }}">
    <meta name="translation-expiry_date" content="{{ ctrans('texts.date') }}">
    <meta name="translation-card_number" content="{{ ctrans('texts.card_number') }}">
    <meta name="translation-cvv" content="{{ ctrans('texts.cvv') }}">
@endsection

@section('gateway_content')
    <form action="{{ route('client.payments.response') }}" method="post" id="server-response">
        @csrf
        <input type="hidden" name="gateway_response">
        <input type="hidden" name="store_card" id="store_card">
        <input type="hidden" name="payment_hash" value="{{ $payment_hash }}">
        <input type="hidden" name="company_gateway_id" value="{{ $gateway->getCompanyGatewayId() }}">
        <input type="hidden" name="payment_method_id" value="1">
        <input type="hidden" name="token" id="token" value="">
        <input type="hidden" name="securefieldcode" value="">
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
        @endisset

        <label>
            <input type="radio" id="toggle-payment-with-credit-card" class="form-radio cursor-pointer" name="payment-type"
                checked />
            <span class="ml-1 cursor-pointer">{{ __('texts.new_card') }}</span>
        </label>
    @endcomponent

    @component('portal.ninja2020.components.general.card-element-single')
        <div id="eway-secure-panel"></div>
    @endcomponent

    @include('portal.ninja2020.gateways.includes.save_card')

    @include('portal.ninja2020.gateways.includes.pay_now', ['disabled' => true])
@endsection

@section('gateway_footer')
    <script src="https://secure.ewaypayments.com/scripts/eWAY.min.js" data-init="false"></script>
    @vite('resources/js/clients/payments/eway-credit-card.js')
@endsection
