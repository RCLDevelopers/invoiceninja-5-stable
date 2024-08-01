@extends('portal.ninja2020.layout.payments', ['gateway_title' => 'ACH', 'card_title' => 'ACH'])

@section('gateway_content')
    @if(count($tokens) > 0)
        <div class="alert alert-failure mb-4" hidden id="errors"></div>

        @include('portal.ninja2020.gateways.includes.payment_details')

        <form action="{{ route('client.payments.response') }}" method="post" id="server-response">
            @csrf
            <input type="hidden" name="company_gateway_id" value="{{ $gateway->getCompanyGatewayId() }}">
            <input type="hidden" name="payment_method_id" value="{{ $payment_method_id }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="currency" value="{{ $currency }}">
            <input type="hidden" name="payment_hash" value="{{ $payment_hash }}">
            <input type="hidden" name="source" value="">
        </form>

        @component('portal.ninja2020.components.general.card-element', ['title' => ctrans('texts.pay_with')])
            @if(count($tokens) > 0)
                @foreach($tokens as $token)
                    <label class="mr-4">
                        <input
                            type="radio"
                            data-token="{{ $token->hashed_id }}"
                            name="payment-type"
                            class="form-radio cursor-pointer toggle-payment-with-token"/>
                        <span class="ml-1 cursor-pointer">{{ ctrans('texts.bank_transfer') }} (*{{ $token->meta->last4 }})</span>
                    </label>
                @endforeach
            @endisset
        @endcomponent

    @else
        @component('portal.ninja2020.components.general.card-element-single', ['title' => 'ACH', 'show_title' => false])
            <span>{{ ctrans('texts.bank_account_not_linked') }}</span>
            <a class="button button-link text-primary"
               href="{{ route('client.payment_methods.index') }}">{{ ctrans('texts.add_payment_method') }}</a>
        @endcomponent
    @endif

    @include('portal.ninja2020.gateways.includes.pay_now')
@endsection

@push('footer')
    <script>
        Array
            .from(document.getElementsByClassName('toggle-payment-with-token'))
            .forEach((element) => element.addEventListener('click', (element) => {
                document.querySelector('input[name=source]').value = element.target.dataset.token;
            }));

        document.getElementById('pay-now').addEventListener('click', function () {
            document.getElementById('server-response').submit();
        });

        document.addEventListener('DOMContentLoaded', () => {
            let firstAccount = document
                .querySelector('.toggle-payment-with-token')

            firstAccount.checked = true;

            document.querySelector('input[name=source]').value = firstAccount.dataset.token;
        });
    </script>
@endpush
