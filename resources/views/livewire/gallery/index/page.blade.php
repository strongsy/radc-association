<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Gallery') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Upload files to bucket from here') }}</flux:subheading>
            <flux:separator variant="subtle" />
        </div>
    </div>

    <flux:input type="file" wire:model="logo" label="Logo"/>
    <flux:input type="file" wire:model="attachments" label="Attachments" multiple />
</div>
