<div>
    <x-page-heading headingText="Payrolls" descText="Manage your company payrolls" />

    <flux:button icon="plus" variant="primary" class="mb-4" wire:click="openModal">
        Create Payroll
    </flux:button>

    <div class="w-full overflow-x-auto mt-4">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Period
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Payment Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Notes
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-900 dark:divide-neutral-700">
                        @forelse ($payrolls as $payroll)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/30">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-neutral-200 capitalize">
                                    {{ \Carbon\Carbon::parse($payroll->payroll_period_start)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($payroll->payroll_period_end)->format('d M Y') }}</td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-neutral-200 capitalize">
                                    {{ \Carbon\Carbon::parse($payroll->payment_date)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400 max-w-sm truncate">
                                    {{ $payroll->notes }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    @if ($payroll->payrollDetails->isEmpty())
                                        <flux:badge variant="pill" color="yellow">Pending</flux:badge>
                                    @else
                                        <flux:badge variant="pill" color="blue">Generated</flux:badge>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex gap-2">
                                    @if ($payroll->payrollDetails->isNotEmpty())
                                        <flux:button icon="eye" variant="filled" type="button" class="w-fit"
                                            wire:click="openViewModal({{ $payroll->id }})">
                                            {{ __('View') }}
                                        </flux:button>
                                    @endif
                                    <flux:button icon="pencil" variant="primary" type="button" class="w-fit"
                                        wire:click="openModal({{ $payroll->id }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    @if ($payroll->payrollDetails->isEmpty())
                                        <flux:button icon="paper-airplane" variant="filled" type="button"
                                            class="w-fit" wire:click="openGenerateModal({{ $payroll->id }})">
                                            {{ __('Generate') }}
                                        </flux:button>

                                        <flux:button icon="trash" variant="danger" type="button" class="w-fit"
                                            wire:click="openDeleteModal({{ $payroll->id }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-400">
                                    You haven't made any leave requests yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $payrolls->links() }}
    </div>


    {{-- Main Modal --}}
    <flux:modal wire:close="closeModal" name="main-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    @if ($isEditting)
                        Edit
                    @else
                        New
                    @endif Payroll
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($isEditting)
                        You're about to edit this payroll.
                    @else
                        Create a new payroll for your employee.
                    @endif
                </flux:text>
            </div>

            <flux:input wire:model="periodStart" label="Period Start" type="date" required />
            <flux:input wire:model="periodEnd" label="Period End" type="date" required />
            <flux:input wire:model="paymentDate" label="Payment Date" type="date" required />
            <flux:textarea wire:model="notes" label="Notes" placeholder="Notes" required />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Delete --}}
    <flux:modal name="delete-modal" class="min-w-[22rem]" wire:close="closeModal">
        <form wire:submit="delete" class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{-- {{ $name }} --}}
                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this payroll.</p>
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

    {{-- Generate Modal --}}
    <flux:modal wire:close="closeModal" name="generate-modal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    Generate Payroll
                </flux:heading>
                <flux:text class="mt-2">
                    This will generate the payroll for all employees in this period.
                    This action cannot be reversed.
                </flux:text>
            </div>

            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <flux:heading size="sm">Period</flux:heading>
                    <flux:text class="mt-2">
                        {{ \Carbon\Carbon::parse($periodStart)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($periodEnd)->format('d M Y') }}
                    </flux:text>
                </div>
                <div>
                    <flux:heading size="sm">Payment Date</flux:heading>
                    <flux:text class="mt-2">
                        {{ \Carbon\Carbon::parse($paymentDate)->format('d M Y') }}
                    </flux:text>
                </div>
                <div>
                    <flux:heading size="sm">Notes</flux:heading>
                    <flux:text class="mt-2">
                        {{ $notes }}
                    </flux:text>
                </div>
            </div>

            <flux:separator />

            <form class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <flux:checkbox.group wire:model="selectedAllowances" label="APPLY ALLOWANCES">
                        @foreach ($allowances as $allowance)
                            <flux:checkbox value="{{ $allowance->id }}" label="{{ $allowance->name }}"
                                description="{{ $allowance->description }}" />
                        @endforeach
                    </flux:checkbox.group>
                    <flux:checkbox.group wire:model="selectedDeductions" label="APPLY DEDUCTIONS">
                        @foreach ($deductions as $deduction)
                            <flux:checkbox value="{{ $deduction->id }}" label="{{ $deduction->name }}"
                                description="{{ $deduction->description }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>

                <flux:separator />

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Generate</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
