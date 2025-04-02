<?php

use App\Models\Media;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Request;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|mimes:jpeg,png|max:1024')]
    public $photo;

    public function save(): void
    {
        $path = $this->photo->store('uploads', 's3');

        /*session()->flash('message', 'File uploaded successfully');*/
    }
}; ?>

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Media') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Control site media from here') }}</flux:subheading>
        <flux:separator variant="subtle"/>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <flux:card>
            <form wire:submit.prevent="save" enctype="multipart/form-data">
                <input type="file" wire:model="photo" accept="image/*">

                @error('photo') <span class="error">{{ $message }}</span> @enderror

                <button type="submit">Save photo</button>
            </form>
        </flux:card>


    </div>
</div>
