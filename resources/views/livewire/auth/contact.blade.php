<?php

use App\Jobs\ContactUsEmailJob;
use App\Models\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    public HoneypotData $extraFields;

    use UsesSpamProtection;

    public function mount(): void
    {
        $this->extraFields = new HoneypotData();
    }

    /**
     * Handle an incoming registration request.
     */
    public function contact(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'subject' => ['required', 'string', 'min:5', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $this->protectAgainstSpam();

        Mail::create($validated);

        // Flash a success message that will persist for the next request
        session()->flash('success', 'Your message has been submitted successfully!');


        // Define your recipients
        $recipients = [config('MAIL_TO_ADDRESS', 'sec@radc.org.uk')];

        // Dispatch the job
        ContactUsEmailJob::dispatch($validated, $recipients);


        $this->reset();
        //$this->redirectIntended(route('home', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Contact Us')"
                   :description="__('Enter your details and we will get back to you as soon as possible.')"/>


    @if(session('success'))
        <div class="alert alert-success ">
            <flux:heading size="sm" level="3" class="text-teal-600 dark:text-teal-400">
                {{ session('success') }}
            </flux:heading>

        </div>
    @endif


    <form wire:submit="contact" class="flex flex-col gap-6">
        <x-honeypot livewire-model="extraFields" />
        <input name="myField" hidden type="text">
        <!-- Name -->
        <flux:input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required=""
                autofocus
                autocomplete="name"
                placeholder="Full name"
        />

        <!-- Email Address -->
        <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required=""
                autocomplete="email"
                placeholder="email@example.com"
        />

        <!-- Subject -->
        <flux:input
            wire:model="subject"
            :label="__('Subject')"
            type="text"
            required=""
            autocomplete="Subject"
            placeholder="Message subject"
        />

        <!-- Message -->
        <flux:textarea
                wire:model="message"
                :label="__('Message')"
                type="text"
                required=""
                placeholder="Your message"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Submit') }}
            </flux:button>
        </div>
    </form>
</div>
