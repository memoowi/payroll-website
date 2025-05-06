<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-page-heading headingText="Company Settings" descText="Manage your company settings" />

    <form wire:submit="updateCompanySetting" class="w-full space-y-6">
        <flux:input wire:model="name" label="Name" type="text" required autofocus autocomplete="name" />
        <flux:textarea wire:model="description" label="Description" type="text" required autocomplete="description" />
        <flux:input wire:model="address" label="Address" type="text" required autocomplete="address" />
        <flux:input wire:model="phone" label="Phone" type="tel" required autocomplete="phone" />
        <flux:input wire:model="value" label="Value" type="text" required autocomplete="value" />
        

        <div class="flex items-center gap-4">
            <div class="flex items-center justify-end gap-3">
                <flux:button variant="danger" type="button" class="aspect-square" wire:click="resetFields">
                    <flux:icon.arrow-path class="size-4" />
                </flux:button>
                <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
            </div>

            <x-action-message class="me-3" on="updated-company-setting">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</div>
