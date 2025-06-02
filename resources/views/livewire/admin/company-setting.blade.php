<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <x-page-heading headingText="Company Settings" descText="Manage your company settings" />

    <form wire:submit="updateCompanySetting" class="w-full space-y-6">
        <flux:input wire:model="name" label="Name" type="text" required autofocus autocomplete="name" />
        <flux:textarea wire:model="description" label="Description" type="text" required autocomplete="description" />
        <flux:input wire:model="address" label="Address" type="text" required autocomplete="address" />
        <flux:input wire:model="phone" label="Phone" type="tel" required autocomplete="phone" />
        <flux:input wire:model="value" label="Value" type="text" required autocomplete="value" />
        <flux:input wire:model="checkInTime" label="Check In Time" type="time" required
            autocomplete="check-in-time" />
        <flux:input wire:model="checkOutTime" label="Check Out Time" type="time" required
            autocomplete="check-out-time" />
        <flux:radio.group wire:model="workingDays" label="Working Days" variant="segmented">
            {{-- <flux:radio label="1" value="1" />
            <flux:radio label="2" value="2" />
            <flux:radio label="3" value="3" />
            <flux:radio label="4" value="4" /> --}}
            <flux:radio label="5" value="5" />
            <flux:radio label="6" value="6" />
            <flux:radio label="7" value="7" />
        </flux:radio.group>
        <flux:description class="-mt-4">
            Select the number of working days in a week. Starting from <b>Monday.</b> For example, if you select <b>5</b>, it means the working days are <b>Monday to Friday</b>.
        </flux:description>
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
