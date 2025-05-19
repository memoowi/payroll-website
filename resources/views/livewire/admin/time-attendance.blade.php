<div>
    <x-page-heading headingText="Time and Attendances" descText="Manage your time and attendances" />

    {{-- Month Filter Buttons --}}
    <div class="my-4 flex flex-wrap gap-2 items-center">
        <span class="text-sm font-medium text-gray-700 dark:text-neutral-300 mr-2">Filter by Month:</span>
        <button wire:click="clearMonthFilter"
                class="px-3 py-1.5 text-xs font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2
                       {{ !$selectedYearMonthFilter ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500' : 'bg-white dark:bg-neutral-700 text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-600 border border-gray-300 dark:border-neutral-600 focus:ring-indigo-500' }}">
            Show All
        </button>
        @if(!empty($monthLinks))
            @foreach ($monthLinks as $link)
                <button wire:click="applyMonthFilter('{{ $link['value'] }}')"
                        class="px-3 py-1.5 text-xs font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2
                               {{ $selectedYearMonthFilter == $link['value'] ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500' : 'bg-white dark:bg-neutral-700 text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-600 border border-gray-300 dark:border-neutral-600 focus:ring-indigo-500' }}">
                    {{ $link['display'] }}
                </button>
            @endforeach
        @else
            <p class="text-xs text-gray-500 dark:text-neutral-400">No specific months with attendance data found to filter by.</p>
        @endif
    </div>

    {{-- Attendances Table --}}
    <div class="w-full overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Employee
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Clock In
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Clock Out
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-900 dark:divide-neutral-700">
                        @forelse ($attendances as $attendance)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/30">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-neutral-200">{{ $attendance->employee->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $attendance->attendance_date ? \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-400">
                                    No attendances found{{ $selectedYearMonthFilter ? ' for ' . \Carbon\Carbon::createFromFormat('Y-m', $selectedYearMonthFilter)->format('F Y') : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="my-4">
        {{ $attendances->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:separator />
    <flux:heading size="xl" class="my-4">Overtimes</flux:heading>
    <flux:button icon="plus" variant="primary" type="button" class="w-fit" wire:click="openOvertimeModal">
        {{ __('Add Overtime') }}
    </flux:button>

    <div class="w-full overflow-x-auto mt-4">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Employee
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Date - Time
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Reason
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-900 dark:divide-neutral-700">
                        @forelse ($overtimes as $overtime)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/30">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-neutral-200">{{ $overtime->employee->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $overtime->overtime_date ? \Carbon\Carbon::parse($overtime->overtime_date)->format('d M Y') : 'N/A' }}
                                    <flux:text size="sm" variant="subtle">

                                        {{ $overtime->start_time ? \Carbon\Carbon::parse($overtime->start_time)->format('H:i') : 'N/A' }} - {{ $overtime->end_time ? \Carbon\Carbon::parse($overtime->end_time)->format('H:i') : 'N/A' }}
                                    </flux:text>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $overtime->duration }} minutes
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400 max-w-sm truncate">
                                    {{ $overtime->reason }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex gap-2">
                                    <flux:button icon="pencil" variant="primary" type="button" class="w-fit" wire:click="openOvertimeModal({{ $overtime->id }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:button icon="trash" variant="danger" type="button" class="w-fit" wire:click="openDeleteOvertimeModal({{ $overtime->id }})">
                                        {{ __('Delete') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-400">
                                    No overtimes found{{ $selectedYearMonthFilter ? ' for ' . \Carbon\Carbon::createFromFormat('Y-m', $selectedYearMonthFilter)->format('F Y') : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Overtime Modal --}}
    <flux:modal wire:close="closeModal" name="overtime-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    @if ($isEditting)
                        Edit
                    @else
                        New
                    @endif Overtime
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($isEditting)
                        Update overtime to the system. This will allow you to manage your overtime more effectively.
                    @else
                        Add a new overtime to the system. This will allow you to manage your overtime more effectively.
                    @endif
                </flux:text>
            </div>
            
            <flux:select label="Employee" wire:model="selectedEmployeeId" placeholder="Choose employee..." required>
                @foreach ($employees as $employee)
                    <flux:select.option value="{{ $employee->id }}">{{ $employee->full_name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="overtimeDate" label="Overtime Date" type="date" required />
            <flux:input wire:model="startTime" label="Start Time" type="time" required />
            <flux:input wire:model="endTime" label="End Time" type="time" required />
            <flux:textarea wire:model="reason" label="Reason" placeholder="Reason" required />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>
    {{-- Modal Delete --}}
    <flux:modal name="delete-modal" class="min-w-[22rem]" wire:close="closeModal">
        <form 
            wire:submit="deleteOvertime"
        class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{-- {{ $name }} --}}
                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this overtime.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">
                    Delete</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
