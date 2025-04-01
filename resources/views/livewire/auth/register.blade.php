<?php

use App\Jobs\RegistrantJob;
use App\Models\Registrant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $community = '';
    public string $membership = '';
    public string $affiliation = '';
    public bool $is_subscribed = false;

    /**
     * Handle an incoming registration request.
     */
    public function registration(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class, 'unique:' . Registrant::class],
            'community' => ['required'],
            'membership' => ['required'],
            'affiliation' => ['required', 'string', 'min:50'],
            'is_subscribed' => ['boolean'],
        ]);

        Registrant::create($validated);

        // Define your recipients
        $recipients = [config('MAIL_TO_ADDRESS', 'sec@radc.org.uk')];

        // Dispatch the job
        RegistrantJob::dispatch($validated, $recipients);


        $this->reset();

        Flux::toast(
            'Registration Received!',
            'We have received your registration request and will respond by email if you submission is successful.',
            'success',
        );

        $this->redirectIntended(route('home', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Register for an account')"
                   :description="__('Enter your details and we will get back to you as soon as possible.')"/>

    <!-- Session Status -->
    <x-auth-session-status class="text-center bg-emerald-600  p-2 rounded-sm" :status="session('status')"/>

    <form wire:submit="registration" class="flex flex-col gap-6">
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

        <!-- Community -->
        <flux:select wire:model="community" label="Community" size="sm" placeholder="Choose community..." required="">
            <flux:select.option>Serving</flux:select.option>
            <flux:select.option>Reserve</flux:select.option>
            <flux:select.option>Veteran</flux:select.option>
            <flux:select.option>Civilian</flux:select.option>
            <flux:select.option>Other</flux:select.option>
        </flux:select>

        <!-- Membership -->
        <flux:select required="" wire:model="membership" label="Membership" size="sm"
                     placeholder="Choose membership...">
            <flux:select.option>Life</flux:select.option>
            <flux:select.option>Annual</flux:select.option>
            <flux:select.option>Unknown</flux:select.option>
        </flux:select>

        <!-- Password -->
        <flux:textarea
            wire:model="affiliation"
            :label="__('Affiliation')"
            type="text"
            required=""
            placeholder="Your affiliation"
        />

        <!-- subscribe for notifications -->
        <flux:checkbox
            value="subscribe"
            label="Subscribe"
            description="Receive notifications straight to your inbox."
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Submit') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate="route('login')">{{ __('Log in') }}</flux:link>
    </div>
</div>
