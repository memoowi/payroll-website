<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-page-heading headingText="Department Management" descText="Manage your departments" />

    {{-- Add Department --}}
    <flux:modal.trigger name="add-department">
        <flux:button icon="plus" variant="primary" type="button" class="w-fit">
            {{ __('Add Department') }}
        </flux:button>
    </flux:modal.trigger>

    {{-- Modal Add Department --}}
    <flux:modal wire:close="closeModal" name="add-department" class="md:w-96">
        <form wire:submit="addDepartment" class="space-y-6">
            <div>
                <flux:heading size="lg">New Department</flux:heading>
                <flux:text class="mt-2">
                    Add a new department to the system. This will allow you to manage your departments more
                    effectively.
                </flux:text>
            </div>
            <flux:input wire:model="name" label="Name" placeholder="Department name" required />
            <flux:textarea wire:model="description" label="Description" placeholder="Department description" />
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Table --}}
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left text-sm uppercase font-bold border-b">
                <th class="p-4 w-12">{{ __('No') }}</th>
                <th class="p-4">{{ __('Name') }}</th>
                <th class="p-4">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($departments as $department)
                <tr class="text-sm border-b border-neutral-500 hover:bg-gray-50/5">
                    <td class="px-4 py-2">{{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}</td>
                    <td class="px-4 py-2">{{ $department->name }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openEditModal({{ $department->id }})" icon="pencil-square"
                                variant="primary" type="button">
                                {{ __('Edit') }}
                            </flux:button>

                            <flux:button
                                wire:click="openDeleteModal('{{ $department->id }}', '{{ $department->name }}')"
                                icon="trash" variant="danger" type="button">
                                {{ __('Delete') }}
                            </flux:button>

                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $departments->links() }}


    {{-- Modal Delete --}}
    <flux:modal name="delete-department" class="min-w-[22rem]" wire:close="closeModal">
        <form wire:submit="deleteDepartment" class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{ $name }}

                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this department.</p>
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

    {{-- Modal Edit Department --}}
    <flux:modal wire:close="closeModal" name="edit-department" class="md:w-96">
        <form wire:submit="updateDepartment" class="space-y-6">
            <div>
                <flux:heading size="lg">Update Department</flux:heading>
                <flux:text class="mt-2">
                    Update a department to the system.
                </flux:text>
            </div>
            <flux:input wire:model="name" label="Name" placeholder="Department name" required />
            <flux:textarea wire:model="description" label="Description" placeholder="Department description" />
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
