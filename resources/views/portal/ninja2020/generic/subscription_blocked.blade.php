@extends('portal.ninja2020.layout.clean')
@section('meta_title', ctrans('texts.error'))

@section('body')

    <div class="flex h-screen">
        <div class="m-auto md:w-1/2 lg:w-1/2">
            <div class="flex flex-col items-center">
                
                @if($account && !$account->isPaid())
                    <div>
                        <img src="{{ asset('images/invoiceninja-black-logo-2.png') }}"
                             class="border-b border-gray-100 h-18 pb-4" alt="Invoice Ninja logo">
                    </div>
                @elseif(isset($company) && !is_null($company))
                    <div>
                        <img src="{{ $company->present()->logo()  }}"
                             class="mx-auto border-b border-gray-100 h-18 pb-4" alt="{{ $company->present()->name() }} logo">
                    </div>
                @endif
                <h1 class="text-center text-3xl mt-10">{{ ctrans("texts.subscription_blocked_title") }}</h1>
                <p class="text-center opacity-75 mt-10">{{ ctrans('texts.subscription_blocked') }}</p>
            </div>
        </div>
    </div>

@stop

@push('footer')

@endpush