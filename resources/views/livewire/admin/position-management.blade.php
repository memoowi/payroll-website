<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-page-heading headingText="Position Management" descText="Manage your positions" />

    {{-- Add Positions --}}
    <flux:modal.trigger name="add-position">
        <flux:button icon="plus" variant="primary" type="button" class="w-fit">
            {{ __('Add Positions') }}
        </flux:button>
    </flux:modal.trigger>

    {{-- Modal Add / Edit Positions --}}
    <flux:modal wire:close="closeModal" name="add-position" class="md:w-96">
        <form wire:submit="addPosition" class="space-y-6">
            <div>
                <flux:heading size="lg">New Position</flux:heading>
                <flux:text class="mt-2">
                    Add a new position to the system.
                </flux:text>
            </div>
            <flux:input wire:model="name" label="Name" placeholder="Position name" required />
            <flux:textarea wire:model="description" label="Description" placeholder="Position description" />
            <flux:select label="Department" wire:model="selectedDepartmentId" placeholder="Choose department..." required>
                @foreach ($departments as $department )
                    <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

</div>