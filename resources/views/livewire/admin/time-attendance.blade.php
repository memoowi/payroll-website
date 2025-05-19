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
    <div class="mt-4">
        {{ $attendances->links(data: ['scrollTo' => false]) }}
    </div>

</div>
