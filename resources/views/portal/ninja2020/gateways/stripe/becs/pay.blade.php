@extends('portal.ninja2020.layout.payments', ['gateway_title' => 'BECS', 'card_title' => 'BECS'])

@section('gateway_head')

    @if($gateway->company_gateway->getConfigField('account_id'))
    <meta name="stripe-account-id" content="{{ $gateway->company_gateway->getConfigField('account_id') }}">
    <meta name="stripe-publishable-key" content="{{ config('ninja.ninja_stripe_publishable_key') }}">
    @else
    <meta name="stripe-publishable-key" content="{{ $gateway->company_gateway->getPublishableKey() }}">
    @endif

    <meta name="amount" content="{{ $stripe_amount }}">
    <meta name="country" content="{{ $country }}">
    <meta name="customer" content="{{ $customer }}">
    <meta name="pi-client-secret" content="{{ $pi_client_secret }}">

    <meta name="translation-name-required" content="{{ ctrans('texts.missing_account_holder_name') }}">
    <meta name="translation-email-required" content="{{ ctrans('texts.provide_email') }}">
    <meta name="translation-terms-required" content="{{ ctrans('texts.you_need_to_accept_the_terms_before_proceeding') }}">
@endsection

@section('gateway_content')
    <div class="alert alert-failure mb-4" hidden id="errors"></div>

    @include('portal.ninja2020.gateways.includes.payment_details')

    @component('portal.ninja2020.components.general.card-element', ['title' => ctrans('texts.payment_type')])
        {{ ctrans('texts.becs') }} ({{ ctrans('texts.bank_transfer') }})
    @endcomponent

    @include('portal.ninja2020.gateways.stripe.becs.becs_debit')
    @include('portal.ninja2020.gateways.includes.save_card')
    @include('portal.ninja2020.gateways.includes.pay_now')
@endsection

@push('footer')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('js/clients/payments/stripe-becs.js') }}"></script>
@endpush
