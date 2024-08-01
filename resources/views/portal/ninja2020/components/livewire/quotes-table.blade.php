<div>
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <span class="hidden mr-2 text-sm md:block">{{ ctrans('texts.per_page') }}</span>
            <select wire:model.live="per_page" class="py-1 text-sm form-select">
                <option>5</option>
                <option selected>10</option>
                <option>15</option>
                <option>20</option>
            </select>
        </div>
        <div class="flex items-center">
            <div class="mr-3">
                <input wire:model.live="status" value="{{ App\Models\Quote::STATUS_SENT }}" value="sent" type="checkbox" class="cursor-pointer form-checkbox" id="sent-checkbox">
                <label for="sent-checkbox" class="text-sm cursor-pointer">{{ ctrans('texts.status_pending') }}</label>
            </div>
            <div class="mr-3">
                <input wire:model.live="status" value="{{ App\Models\Quote::STATUS_APPROVED }}" value="approved" type="checkbox" class="cursor-pointer form-checkbox" id="approved-checkbox">
                <label for="approved-checkbox" class="text-sm cursor-pointer">{{ ctrans('texts.approved') }}</label>
            </div>
            <div class="mr-3">
                <input wire:model.live="status" value="{{ App\Models\Quote::STATUS_EXPIRED }}" value="expired" type="checkbox" class="cursor-pointer form-checkbox" id="expired-checkbox">
                <label for="expired-checkbox" class="text-sm cursor-pointer">{{ ctrans('texts.expired') }}</label>
            </div>
        </div>
    </div>
    <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="inline-block min-w-full overflow-hidden align-middle rounded">
            <table class="min-w-full mt-4 border border-gray-200 rounded shadow quotes-table">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary">
                            <label>
                                <input type="checkbox" class="form-check form-check-parent">
                            </label>
                        </th>
                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary">
                            <span role="button" wire:click="sortBy('number')" class="cursor-pointer">
                                {{ ctrans('texts.quote_number') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary">
                            <span role="button" wire:click="sortBy('date')" class="cursor-pointer">
                                {{ ctrans('texts.quote_date') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary">
                            <span role="button" wire:click="sortBy('balance')" class="cursor-pointer">
                                {{ ctrans('texts.amount') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary">
                            <span role="button" wire:click="sortBy('date')" class="cursor-pointer">
                                {{ ctrans('texts.valid_until') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary">
                            <span role="button" wire:click="sortBy('status_id')" class="cursor-pointer">
                                {{ ctrans('texts.quote_status') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 bg-primary"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotes as $quote)
                        <tr class="bg-white group hover:bg-gray-100">
                            <td class="px-6 py-4 text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                <label>
                                    <input type="checkbox" class="form-check form-check-child" data-value="{{ $quote->hashed_id }}">
                                </label>
                            </td>
                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-nowrap">
                                {{ $quote->number }}
                            </td>
                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-nowrap">
                                {{ $quote->translateDate($quote->date, $quote->client->date_format(), $quote->client->locale()) }}
                            </td>
                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-nowrap">
                                {{ App\Utils\Number::formatMoney($quote->amount, $quote->client) }}
                            </td>
                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-nowrap">
                                {{ $quote->translateDate($quote->due_date, $quote->client->date_format(), $quote->client->locale()) }}
                            </td>
                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-nowrap">
                                {!! App\Models\Quote::badgeForStatus($quote->status_id) !!}
                            </td>
                            <td class="flex items-center justify-end px-6 py-4 text-sm font-medium leading-5 whitespace-nowrap">
                                <a href="{{ route('client.quote.show', $quote->hashed_id) }}" class="button-link text-primary">
                                    {{ ctrans('texts.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white group hover:bg-gray-100">
                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-nowrap" colspan="100%">
                                {{ ctrans('texts.no_results') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center mt-6 mb-6 md:justify-between">
        @if($quotes->total() > 0)
            <span class="hidden text-sm text-gray-700 md:block">
                {{ ctrans('texts.showing_x_of', ['first' => $quotes->firstItem(), 'last' => $quotes->lastItem(), 'total' => $quotes->total()]) }}
            </span>
        @endif
        {{ $quotes->links('portal/ninja2020/vendor/pagination') }}
    </div>
</div>

@push('footer')
    @vite('resources/js/clients/quotes/action-selectors.js')
@endpush
