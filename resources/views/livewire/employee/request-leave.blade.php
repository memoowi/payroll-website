<div>
    <x-page-heading headingText="Request Leave" descText="Manage your leave requests and approvals" />

    <flux:button icon="plus" variant="primary" type="button" class="w-fit" wire:click="openModal">
        {{ __('Request Leave') }}
    </flux:button>




    {{-- Main Modal --}}
    <flux:modal wire:close="closeModal" name="main-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    @if ($isEditting)
                        Edit
                    @else
                        New
                    @endif Leave Request
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($isEditting)
                        You're about to edit this leave request.
                    @else
                        You're about to create a new leave request.
                    @endif
                </flux:text>
            </div>
            
            <flux:select label="Leave Type" wire:model="leaveType" placeholder="Choose Leave Type..." required>
                <flux:select.option value="sick">Sick</flux:select.option>
                <flux:select.option value="vacation">Vacation</flux:select.option>
                <flux:select.option value="personal">Personal</flux:select.option>
                <flux:select.option value="other">Other</flux:select.option>
            </flux:select>
            <flux:input wire:model="startDate" label="Start Date" type="date" required />
            <flux:input wire:model="endDate" label="End Date" type="date" required />
            <flux:textarea wire:model="reason" label="Reason" placeholder="Reason" required />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Delete --}}
    <flux:modal name="delete-modal" class="min-w-[22rem]" wire:close="closeModal">
        <form 
            wire:submit="deleteOvertime"
        class="space-y-6">
            <div>
                <flux:heading size="lg">Delete
                    {{-- {{ $name }} --}}
                    ?
                </flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this overtime.</p>
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
