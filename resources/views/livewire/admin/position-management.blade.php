<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-page-heading headingText="Position Management" descText="Manage your positions" />

    {{-- Add Positions --}}
    <flux:modal.trigger name="position">
        <flux:button icon="plus" variant="primary" type="button" class="w-fit">
            {{ __('Add Positions') }}
        </flux:button>
    </flux:modal.trigger>

    {{-- Table --}}
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left text-sm uppercase font-bold border-b">
                <th class="p-4 w-12">{{ __('No') }}</th>
                <th class="p-4">{{ __('Name') }}</th>
                <th class="p-4">{{ __('Department') }}</th>
                <th class="p-4">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($positions as $position)
                <tr class="text-sm border-b border-neutral-500 hover:bg-gray-50/5">
                    <td class="px-4 py-2">{{ $loop->iteration + ($positions->currentPage() - 1) * $positions->perPage() }}</td>
                    <td class="px-4 py-2">{{ $position->name }}</td>
                    <td class="px-4 py-2">{{ $position->department->name }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openEditModal({{ $position->id }})" icon="pencil-square"
                                variant="primary" type="button">
                                {{ __('Edit') }}
                            </flux:button>

                            <flux:button
                                wire:click="openDeleteModal('{{ $position->id }}', '{{ $position->name }}')"
                                icon="trash" variant="danger" type="button">
                                {{ __('Delete') }}
                            </flux:button>

                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $positions->links() }}

    {{-- Modal Add / Edit Positions --}}
    <flux:modal wire:close="closeModal" name="position" class="md:w-96">
        <form @if($isEditting) wire:submit="updatePosition" @else wire:submit="addPosition" @endif class="space-y-6">
            <div>
                <flux:heading size="lg">@if($isEditting) Edit @else New @endif Position</flux:heading>
                <flux:text class="mt-2">
                    @if ($isEditting)
                    Update a position to the system.
                    @else
                    Add a new position to the system.
                    @endif
                </flux:text>
            </div>
            <flux:input wire:model="name" label="Name" placeholder="Position name" required />
            <flux:textarea wire:model="description" label="Description" placeholder="Position description" />
            <flux:select label="Department" wire:model="selectedDepartmentId" placeholder="Choose department..."
                required>
                @foreach ($departments as $department)
                    <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Delete --}}
    <flux:modal name="delete-position" class="min-w-[22rem]" wire:close="closeModal">
        <form wire:submit="deletePosition" class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{ $name }}

                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this position.</p>
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
