<div>
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <span class="mr-2 text-sm hidden md:block">{{ ctrans('texts.per_page') }}</span>
            <select wire:model.live="per_page" class="form-select py-1 text-sm">
                <option>5</option>
                <option selected>10</option>
                <option>15</option>
                <option>20</option>
            </select>
        </div>
    </div>
    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full overflow-hidden rounded">
            <table class="min-w-full shadow rounded border border-gray-200 mt-4 credits-table bg-white">
                <thead>
                <tr>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary task_description">
                        <span role="button" wire:click="sortBy('description')" class="cursor-pointer">
                            {{ ctrans('texts.description') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-primary task_project">
                        <span role="button" wire:click="sortBy('description')" class="cursor-pointer">
                            {{ ctrans('texts.project') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-primary text-left text-xs leading-4 font-medium text-white uppercase tracking-wider task_status">
                        <span role="button" wire:click="sortBy('status_id')" class="cursor-pointer">
                            {{ ctrans('texts.status') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-primary text-left text-xs leading-4 font-medium text-white uppercase tracking-wider task_duration">
                        <span role="button" class="cursor-pointer">
                            {{ ctrans('texts.duration') }}
                        </span>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    <tr class="bg-white group hover:bg-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500 task_descripton">
                            {{ \Illuminate\Support\Str::limit($task->description, 80) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500 task_project">
                            {{ $task->project?->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500 task_status">
                            <div class="flex">
                            {!! $task->stringStatus() !!}

                            @if($task->invoice_id && ($task->invoice->status_id != \App\Models\Invoice::STATUS_DRAFT || $task->invoice->status_id != \App\Models\Invoice::STATUS_CANCELLED || !$task->invoice->is_deleted))

                            <a href="{{ route('client.invoice.show', $task->invoice->hashed_id) }}" class="button-link text-primary">
                               <img src="{{ asset('images/svg/dark/file-text.svg') }}" class="w-5 h-5 fill-current text-white mr-3 ml-1">
                            </a>

                            @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500 task_duration">
                            {{ \Carbon\CarbonInterval::seconds($task->calcDuration())->cascade()->forHumans() }}
                        </td>
                    </tr>
                        @if($show_item_description)
                            <tr><td width="100%" colspan="4">
                            <table class="min-w-full ml-5">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-gray-500 task_date">
                                            <span>
                                                {{ ctrans('texts.date') }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-gray-500 task_duration">
                                            <span>
                                                {{ ctrans('texts.duration') }}
                                            </span>
                                        </th>
                                        <th colspan="4" class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-white uppercase border-b border-gray-200 bg-gray-500 task_description">
                                            <span>
                                                {{ ctrans('texts.description') }}
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($task->processLogsExpandedNotation() as $log)
                                    @if(strlen($log['description']) > 1)
                                        <tr class="bg-white group border-b border-gray-100">
                                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 w-1/6 task_date">
                                                {{ $log['start_date']}}
                                            </td>
                                            <td class="px-6 py-4 text-sm leading-5 text-gray-500 w-1/6 task_duration">
                                                {{ $log['duration']}}
                                            </td>
                                            <td colspan="4" class="px-6 py-4 text-sm leading-5 text-gray-500 w-4/6 task_description">
                                                {!! nl2br(e($log['description'])) !!}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                            </td></tr>
                        @endif
                @endforeach
                @if($tasks->count() == 0)
                    <tr class="bg-white group hover:bg-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm leading-5 text-gray-500" colspan="100%">
                            {{ ctrans('texts.no_results') }}
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex justify-center md:justify-between mt-6 mb-6">
        @if($tasks->total() > 0)
            <span class="text-gray-700 text-sm hidden md:block">
                {{ ctrans('texts.showing_x_of', ['first' => $tasks->firstItem(), 'last' => $tasks->lastItem(), 'total' => $tasks->total()]) }}
            </span>
        @endif
        {{ $tasks->links('portal/ninja2020/vendor/pagination') }}
    </div>
</div>
