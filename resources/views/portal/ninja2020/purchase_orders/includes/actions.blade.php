<form action="{{ route('vendor.purchase_orders.bulk') }}" method="post" id="approve-form" />
@csrf

<input type="hidden" name="action" value="accept">
<input type="hidden" name="process" value="true">
<input type="hidden" name="purchase_orders[]" value="{{ $purchase_order->hashed_id }}">
<input type="hidden" name="signature">

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-start sm:justify-between">
            <div>
            
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ ctrans('texts.approve') }}
                </h3>
                
                <div class="btn hidden md:block" data-clipboard-text="{{url("vendor/purchase_order/{$key}")}}" aria-label="Copied!">
                    <div class="flex text-sm leading-6 font-medium text-gray-500">
                        <p class="mr-2">{{url("vendor/purchase_order/{$key}")}}</p>
                        <p><img class="h-5 w-5" src="{{ asset('assets/clippy.svg') }}" alt="Copy to clipboard"></p>
                    </div>
                </div>
            
            </div>

            <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
                @yield('quote-not-approved-right-side')

                <div class="inline-flex rounded-md shadow-sm">
                    <button onclick="setTimeout(() => this.disabled = true, 0); return true;" type="button"
                        class="button button-primary bg-primary"
                        id="approve-button">{{ ctrans('texts.accept') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

</form>
