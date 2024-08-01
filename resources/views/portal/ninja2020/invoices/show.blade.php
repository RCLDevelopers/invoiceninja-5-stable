@extends('portal.ninja2020.layout.app')
@section('meta_title', ctrans('texts.view_invoice'))

@push('head')
    <meta name="show-invoice-terms" content="{{ $settings->show_accept_invoice_terms ? true : false }}">
    <meta name="require-invoice-signature" content="{{ $client->user->account->hasFeature(\App\Models\Account::FEATURE_INVOICE_SETTINGS) && $settings->require_invoice_signature }}">
    <meta name="show-required-fields-form" content="{{ auth()->guard('contact')->user()->showRff() }}" />
    @include('portal.ninja2020.components.no-cache')
    <script src="{{ asset('vendor/signature_pad@2.3.2/signature_pad.min.js') }}"></script>

@endpush

@section('header')
    @if($errors->any())
        <div class="alert alert-failure mb-4">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
@endsection

@section('body')

    @if($invoice->isPayable() && $client->getSetting('custom_message_unpaid_invoice'))
        @component('portal.ninja2020.components.message')
            <pre>{{ $client->getSetting('custom_message_unpaid_invoice') }}</pre>
        @endcomponent
    @elseif($invoice->status_id === 4 && $client->getSetting('custom_message_paid_invoice'))
        @component('portal.ninja2020.components.message')
            <pre>{{ $client->getSetting('custom_message_paid_invoice') }}</pre>
        @endcomponent
    @endif

    @if($invoice->isPayable())
        <form action="{{ ($settings->client_portal_allow_under_payment || $settings->client_portal_allow_over_payment) ? route('client.invoices.bulk') : route('client.payments.process') }}" method="post" id="payment-form">
            @csrf
            <input type="hidden" name="invoices[]" value="{{ $invoice->hashed_id }}">
            <input type="hidden" name="action" value="payment">

            <input type="hidden" name="company_gateway_id" id="company_gateway_id">
            <input type="hidden" name="payment_method_id" id="payment_method_id">
            <input type="hidden" name="signature">
            <input type="hidden" name="hash" value="{{ $hash }}">
            <input type="hidden" name="payable_invoices[0][amount]" value="{{ $invoice->partial > 0 ?  \App\Utils\Number::formatValue($invoice->partial, $invoice->client->currency()) : \App\Utils\Number::formatValue($invoice->balance, $invoice->client->currency()) }}">
            <input type="hidden" name="payable_invoices[0][invoice_id]" value="{{ $invoice->hashed_id }}">
            <input type="hidden" name="contact_first_name" value="{{ auth()->guard('contact')->user()->first_name }}">
            <input type="hidden" name="contact_last_name" value="{{ auth()->guard('contact')->user()->last_name }}">
            <input type="hidden" name="contact_email" value="{{ auth()->guard('contact')->user()->email }}">

            <div class="bg-white shadow sm:rounded-lg mb-4" translate>
                <div class="px-4 py-5 sm:p-6">
                    <div class="sm:flex sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ ctrans('texts.invoice_number_placeholder', ['invoice' => $invoice->number])}}
                                - {{ ctrans('texts.unpaid') }}
                            </h3>

                            @if($key)
                            <div class="btn hidden md:block" data-clipboard-text="{{url("client/invoice/{$key}")}}" aria-label="Copied!">
                                <div class="flex text-sm leading-6 font-medium text-gray-500">
                                    <p class="mr-2">{{url("client/invoice/{$key}")}}</p>
                                    <p><img class="h-5 w-5" src="{{ asset('assets/clippy.svg') }}" alt="Copy to clipboard"></p>
                                </div>
                            </div>
                            @endif


                        </div>
                        <div class="mt-5 sm:mt-0 sm:ml-6 flex justify-end">
                            <div class="inline-flex rounded-md shadow-sm">
                                <input type="hidden" name="invoices[]" value="{{ $invoice->hashed_id }}">
                                <input type="hidden" name="action" value="payment">

                                @if($settings->client_portal_allow_under_payment || $settings->client_portal_allow_over_payment)
                                    <button class="button button-primary bg-primary">{{ ctrans('texts.pay_now') }}</button>
                                @else
                                    @livewire('pay-now-dropdown', ['total' => $invoice->getPayableAmount(), 'company' => $company])
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="bg-white shadow sm:rounded-lg mb-4">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ ctrans('texts.invoice_number_placeholder', ['invoice' => $invoice->number])}}
                            - {{ \App\Models\Invoice::stringStatus($invoice->status_id) }}
                        </h3>

                            @if($key)
                            <div class="btn hidden md:block" data-clipboard-text="{{url("client/invoice/{$key}")}}" aria-label="Copied!">
                                <div class="flex text-sm leading-6 font-medium text-gray-500">
                                    <p class="pr-10">{{url("client/invoice/{$key}")}}</p>
                                    <p><img class="h-5 w-5" src="{{ asset('assets/clippy.svg') }}" alt="Copy to clipboard"></p>
                                </div>
                            </div>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('portal.ninja2020.components.entity-documents', ['entity' => $invoice])
    @livewire('pdf-slot', ['entity' => $invoice, 'invitation' => $invitation, 'db' => $invoice->company->db])

@endsection

@section('footer')
    @include('portal.ninja2020.invoices.includes.required-fields')
    @include('portal.ninja2020.invoices.includes.signature')
    @include('portal.ninja2020.invoices.includes.terms', ['entities' => [$invoice], 'variables' => $variables, 'entity_type' => ctrans('texts.invoice')])
@endsection

@push('head')
    @vite('resources/js/clients/invoices/payment.js')
    <script src="{{ asset('vendor/clipboard.min.js') }}" defer></script>

    <script type="text/javascript">

    document.addEventListener('DOMContentLoaded', () => {

        var clipboard = new ClipboardJS('.btn');

    });

    </script>
@endpush
