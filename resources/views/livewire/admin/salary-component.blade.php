<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-page-heading headingText="Salary Component" descText="Manage your company salary components" />

    <h2 class="text-xl font-semibold">Allowances</h2>
    {{-- Add Allowance --}}
    <flux:button wire:click="openModal('allowance')" icon="plus" variant="primary" type="button" class="w-fit">
        {{ __('Add Allowance') }}
    </flux:button>

    {{-- Allowance Table --}}
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left text-sm uppercase font-bold border-b">
                <th class="p-4 w-12">{{ __('No') }}</th>
                <th class="p-4">{{ __('Name') }}</th>
                <th class="p-4">{{ __('Amount') }}</th>
                <th class="p-4">{{ __('Rule') }}</th>
                <th class="p-4">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allowances as $allowance)
                <tr class="border-b hover:bg-gray-50/5">
                    <td class="px-4 py-2">{{ $loop->iteration + ($allowances->currentPage() - 1) * $allowances->perPage() }}</td>
                    <td class="px-4 py-2">{{ $allowance->name }}</td>
                    <td class="px-4 py-2">
                        @if ($allowance->rule == 'fixed')
                            {{--  add currency --}}
                            Rp {{ number_format($allowance->amount, 0, ',', '.') }}
                            @else
                            {{--  jadiin persen --}}
                            {{ number_format($allowance->amount * 100, 0, ',', '.') }}%
                        @endif
                    </td>
                    <td class="px-4 py-2 capitalize">{{ $allowance->rule }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openModal('allowance', {{ $allowance->id }})" icon="pencil-square" variant="primary" type="button">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button wire:click="openDeleteModal('allowance', '{{ $allowance->id }}', '{{ $allowance->name }}')" icon="trash" variant="danger" type="button">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{$allowances->links()}}

    {{-- Main Modal Add and Edit --}}
    <flux:modal wire:close="closeModal" name="main-modal" class="md:w-96">
        <form 
            @if ($modalType == 'allowance')
                @if ($isEditting)
                    wire:submit="updateAllowance"
                @else
                    wire:submit="addAllowance"
                @endif
            @else
                @if ($isEditting)
                    wire:submit="updateDeduction"
                @else
                    wire:submit="addDeduction"
                @endif
            @endif
            class="space-y-6">
            <div>
                @if ($modalType == 'allowance')
                    <flux:heading size="lg">@if ($isEditting) Edit @else New @endif Allowance</flux:heading>
                    <flux:text class="mt-2">
                        @if ($isEditting)
                        Update allowance to the system. This will allow you to manage your allowances more effectively.
                        @else
                        Add a new allowance to the system. This will allow you to manage your allowances more effectively.
                        @endif
                    </flux:text>
                @else
                    <flux:heading size="lg">@if ($isEditting) Edit @else New @endif Deduction</flux:heading>
                    <flux:text class="mt-2">
                        @if ($isEditting)
                        Update deduction to the system. This will allow you to manage your deductions more effectively.
                        @else
                        Add a new deduction to the system. This will allow you to manage your deductions more effectively.
                        @endif
                    </flux:text>
                @endif
            </div>
            <flux:input wire:model="name" label="Name" placeholder="Name" required />
            <flux:textarea wire:model="description" label="Description" placeholder="Description" />
            <flux:input wire:model="amount" label="Amount" placeholder="Amount" required />
            @if ($modalType == 'allowance')
                <flux:text class="mt-2">
                    For Rule "Percentage".<br /> 1 is equal to 100%,<br /> 0.5 is equal to 50%.
                </flux:text>
                <flux:select label="Rule" wire:model="rule" placeholder="Choose rule..." required>
                    <flux:select.option value="fixed">Fixed</flux:select.option>
                    <flux:select.option value="percentage">Percentage</flux:select.option>
                </flux:select>
            @endif
            
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Delete --}}
    <flux:modal name="delete-modal" class="min-w-[22rem]" wire:close="closeModal">
        <form 
        @if ($modalType == 'allowance')
            wire:submit="deleteAllowance"
        @else
            wire:submit="deleteDeduction"
        @endif
        class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{ $name }}
                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this @if ($modalType == 'allowance') allowance @else deduction @endif.</p>
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
    
    <flux:separator />

    <h2 class="text-xl font-semibold">Deductions</h2>

    {{-- Add Deductions --}}
    <flux:button wire:click="openModal('deduction')" icon="plus" variant="primary" type="button" class="w-fit">
        {{ __('Add Deductions') }}
    </flux:button>

    {{-- Deductions Table --}}
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left text-sm uppercase font-bold border-b">
                <th scope="col" class="p-4 w-12">
                    {{ __('No') }}
                </th>
                <th scope="col" class="p-4">
                    {{ __('Name') }}
                </th>
                <th scope="col" class="p-4">
                    {{ __('Amount') }}
                </th>
                <th scope="col" class="p-4">
                    {{ __('Actions') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deductions as $deduction)
                <tr class="border-b hover:bg-gray-50/5">
                    <td class="px-4 py-2">
                        {{ $loop->iteration + ($deductions->currentPage() - 1) * $deductions->perPage() }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $deduction->name }}
                    </td>
                    <td class="px-4 py-2">
                            Rp {{ number_format($deduction->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openModal('deduction', {{ $deduction->id }})" icon="pencil-square" variant="primary" type="button">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button wire:click="openDeleteModal('deduction', '{{ $deduction->id }}', '{{ $deduction->name }}')" icon="trash" variant="danger" type="button">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{$deductions->links()}}

</div>
