<div>
    <x-page-heading headingText="Tax Settings" descText="Manage your company PPh tax settings" />

    <flux:modal.trigger name="main-modal">
        <flux:button icon="plus" variant="primary" type="button" class="w-fit">
            {{ __('Add Tax') }}
        </flux:button>
    </flux:modal.trigger>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left text-sm uppercase font-bold border-b">
                <th class="p-4 w-12">{{ __('No') }}</th>
                <th class="p-4">{{ __('Name') }}</th>
                <th class="p-4">{{ __('Rate') }}</th>
                <th class="p-4">{{ __('Threshold') }}</th>
                <th class="p-4">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($taxes as $tax)
                <tr class="border-b hover:bg-gray-50/5">
                    <td class="px-4 py-2">
                        {{ $loop->iteration + ($taxes->currentPage() - 1) * $taxes->perPage() }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $tax->name }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $tax->rate * 100 }}%
                    </td>
                    <td class="px-4 py-2">
                        @php
                            $threshold = explode('-', $tax->threshold);
                            $lowerBound = $threshold[0];
                            $upperBound = $threshold[1];
                        @endphp
                        {{ 'Rp ' . number_format($lowerBound, 0, ',', '.') }}
                        {{ __('to') }}
                        {{ 'Rp ' . number_format($upperBound, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openModal('edit', {{ $tax->id }})" icon="pencil-square"
                                variant="primary" type="button">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button  wire:click="openDeleteModal('{{ $tax->id }}', '{{ $tax->name }}')" icon="trash" variant="danger" type="button">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $taxes->links() }}

    {{-- Main Modal --}}
    <flux:modal wire:close="closeModal" name="main-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    @if ($isEditting)
                        Edit
                    @else
                        New
                    @endif Tax
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($isEditting)
                        Update tax to the system. This will allow you to manage your employee PPh tax more effectively.
                    @else
                        Add a new tax to the system. This will allow you to manage your employee PPh tax more
                        effectively.
                    @endif
                </flux:text>
            </div>
            <flux:input wire:model="name" label="Name" placeholder="Name" required />
            <flux:textarea wire:model="description" label="Description" placeholder="Description" />
            <flux:input wire:model="rate" label="Rate" placeholder="Rate" type="number" min="0" step="0.01"
                max="1" required />
            <flux:text class="mt-2">
                Rate value must be in percentage format, <br />e.g. 5% = 0.05
            </flux:text>

            <flux:input wire:model="lowerBound" label="Lower Bound" placeholder="Lower Bound" required />
            <flux:input wire:model="upperBound" label="Upper Bound" placeholder="Upper Bound" required />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Delete --}}
    <flux:modal name="delete-modal" class="min-w-[22rem]" wire:close="closeModal">
        <form 
            wire:submit="deleteTax"
        class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{ $name }}
                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this tax.</p>
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
