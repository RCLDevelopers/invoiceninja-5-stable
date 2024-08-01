@extends('portal.ninja2020.layout.payments', ['gateway_title' => 'BACS', 'card_title' => 'BACS'])

@section('gateway_head')

    @if($gateway->getConfigField('account_id'))
    <meta name="stripe-account-id" content="{{ $gateway->getConfigField('account_id') }}">
    <meta name="stripe-publishable-key" content="{{ config('ninja.ninja_stripe_publishable_key') }}">
    @else
    <meta name="stripe-publishable-key" content="{{ $gateway->getPublishableKey() }}">
    @endif
    <meta name="stripe-redirect-url" content="{{ $session->url }}">
    <meta name="only-authorization" content="true">

@endsection

@section('gateway_content')
    <form action="{{ route('client.payment_methods.store', ['method' => App\Models\GatewayType::BACS]) }}" method="post" id="server_response">
        @csrf
        <input type="hidden" name="company_gateway_id" value="{{ $gateway->gateway_id }}">
        <input type="hidden" name="payment_method_id" value="1">
        <input type="hidden" name="gateway_response" id="gateway_response">
        <input type="hidden" name="is_default" id="is_default">
    </form>

    <div class="alert alert-failure mb-4" hidden id="errors"></div>
    @component('portal.ninja2020.components.general.card-element', ['title' => ctrans('texts.method')])
        {{ ctrans('texts.bacs') }}
    @endcomponent

    @component('portal.ninja2020.gateways.includes.pay_now', ['id' => 'authorize-bacs'])
        {{ ctrans('texts.add_payment_method') }}
    @endcomponent
@endsection

@section('gateway_footer')
    <script src="https://js.stripe.com/v3/"></script>
    @vite('resources/js/clients/payments/stripe-bacs.js')
@endsection
