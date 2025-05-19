<div>
    <x-page-heading headingText="Employee Management" descText="Manage your employees" />

    <flux:modal.trigger name="main-modal">
        <flux:button icon="plus" variant="primary" type="button" class="w-fit">
            {{ __('Register an Employee') }}
        </flux:button>
    </flux:modal.trigger>

    <div class="w-full overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="text-left text-sm uppercase font-bold border-b">
                    <th class="p-4 w-12">{{ __('No') }}</th>
                    <th class="p-4">{{ __('Full Name') }}</th>
                    <th class="p-4">{{ __('Hire Date') }}</th>
                    <th class="p-4">{{ __('Department & Position') }}</th>
                    <th class="p-4">{{ __('Base Salary') }}</th>
                    <th class="p-4">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr class="border-b hover:bg-gray-50/5">
                        <td class="px-4 py-2">
                            {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $employee->full_name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($employee->hire_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $employee->position->name }}
                            <flux:text size="sm">
                                {{ $employee->position->department->name }} Dept.
                            </flux:text>
                        </td>
                        <td class="px-4 py-2">
                            Rp {{ number_format($employee->salary->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="openModal('view', {{ $employee->id }})" icon="eye" variant="filled" type="button">
                                    {{ __('View') }}
                                </flux:button>
                                <flux:button wire:click="openModal('edit', {{ $employee->id }})" icon="pencil-square"
                                    variant="primary" type="button">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button wire:click="openDeleteModal('{{ $employee->id }}', '{{ $employee->full_name }}')" icon="trash" variant="danger" type="button">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($employees->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            {{ __('No employees found.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{ $employees->links() }}


    {{-- Main Modal --}}
    <flux:modal wire:close="closeModal" name="main-modal" class="w-full md:min-w-3/4">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    @if ($isEditting)
                        Edit
                    @else
                        New
                    @endif Employee
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($isEditting)
                        Update employee to the system. This will allow you to manage your employees more effectively.
                    @else
                        Add a new employee to the system. This will allow you to manage your employees more effectively.
                    @endif
                </flux:text>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="email" label="Email" placeholder="Email" required />
                @if ($isEditting)
                    <flux:input wire:model="password" label="Password" placeholder="Password" type="password" required disabled />
                @else
                    <flux:input wire:model="password" label="Password" placeholder="Password" type="password" required />
                @endif
            </div>

            <flux:separator />

            <flux:input wire:model="fullName" label="Full Name" placeholder="Full Name" required />
            <flux:textarea wire:model="address" label="Address" placeholder="Address" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="phone" label="Phone" placeholder="Phone" type="tel" required />
                <flux:input wire:model="hireDate" label="Hire Date" placeholder="Hire Date" type="date" required />
                <flux:select label="Department" wire:model="selectedDepartmentId" wire:change="updatePositions" placeholder="Choose department..."
                    required>
                    @foreach ($departments as $department)
                        <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select label="Position" wire:model="selectedPositionId" placeholder="Choose position..." required>
                    @foreach ($positions as $position)
                        <flux:select.option value="{{ $position->id }}">{{ $position->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="bankName" label="Bank Name" placeholder="Bank Name" required />
                <flux:input wire:model="bankAccount" label="Bank Account" placeholder="Bank Account" required />
                <flux:input wire:model="npwp" label="NPWP" placeholder="NPWP" />
                <flux:input wire:model="salary" label="Base Salary" placeholder="Base Salary" type="number" min="0" required />
                <flux:select wire:model="payFrequency" label="Pay Frequency" placeholder="Pay Frequency" required>
                    <flux:select.option value="weekly">Weekly</flux:select.option>
                    <flux:select.option value="monthly">Monthly</flux:select.option>
                </flux:select>
                <flux:input wire:model="effectiveDate" label="Effective Date" placeholder="Effective Date" type="date" required />
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Delete --}}
    <flux:modal name="delete-modal" class="min-w-[22rem]" wire:close="closeModal">
        <form 
            wire:submit="delete"
        class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{ $fullName }}
                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this employee.</p>
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

    {{-- View Modal --}}
    <flux:modal wire:close="closeModal" name="view-modal" class="w-full md:min-w-3/4">
        <div class="space-y-6">
            <div class="flex flex-col gap-2">
                <flux:heading size="lg">Employee Details</flux:heading>
                <flux:description class="text-sm">
                    <p>Details of {{ $fullName }}</p>
                    <p>
                        This is the information you provided when registering this employee. Make sure to keep it up to date.
                    </p>
                </flux:description>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="email" label="Email" placeholder="Email" disabled />
                <flux:input wire:model="fullName" label="Full Name" placeholder="Full Name" disabled />
                <flux:input wire:model="address" label="Address" placeholder="Address" disabled />
                <flux:input wire:model="phone" label="Phone" placeholder="Phone" type="tel" disabled />
                <flux:input wire:model="hireDate" label="Hire Date" placeholder="Hire Date" type="date" disabled />
                <flux:select label="Department" wire:model="selectedDepartmentId" wire:change="updatePositions" placeholder="Choose department..."
                    disabled>
                    @foreach ($departments as $department)
                        <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select label="Position" wire:model="selectedPositionId" placeholder="Choose position..." disabled>
                    @foreach ($positions as $position)
                        <flux:select.option value="{{ $position->id }}">{{ $position->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="bankName" label="Bank Name" placeholder="Bank Name" disabled />
                <flux:input wire:model="bankAccount" label="Bank Account" placeholder="Bank Account" disabled />
                <flux:input wire:model="npwp" label="NPWP" placeholder="NPWP" disabled />
                <flux:input wire:model="salary" label="Base Salary" placeholder="Base Salary" type="number" min="0" disabled />
                <flux:input wire:model="payFrequency" label="Pay Frequency" placeholder="Pay Frequency" disabled />
                <flux:input wire:model="effectiveDate" label="Effective Date" placeholder="Effective Date" type="date" disabled />
            </div>
        </div>
    </flux:modal>
</div>
