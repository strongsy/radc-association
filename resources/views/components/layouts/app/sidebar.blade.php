<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <title>{{ config('app.name') }}</title>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>

                    <!--article route -->
                    @can('article-list')
                        <flux:navlist.item icon="pencil-square" :href="route('article.index')" :current="request()->routeIs('article.index')" wire:navigate>{{ __('Articles') }}</flux:navlist.item>
                    @endcan

                    <!-- mail route -->
                    @can('mail-list')
                        <flux:navlist.item icon="envelope" :href="route('mail.index')" :current="request()->routeIs('mail.index')" wire:navigate>{{ __('Emails') }}</flux:navlist.item>
                    @endcan

                    <!--event route -->
                    @can('event-list')
                        <flux:navlist.item icon="calendar-days" :href="route('event.index')" :current="request()->routeIs('event.index')" wire:navigate>{{ __('Events') }}</flux:navlist.item>
                    @endcan

                    <!--post route -->
                    @can('media-list')
                        <flux:navlist.item icon="photo" :href="route('media.index')" :current="request()->routeIs('media.index')" wire:navigate>{{ __('Media') }}</flux:navlist.item>
                    @endcan

                    <!--post route -->
                    @can('post-list')
                        <flux:navlist.item icon="microphone" :href="route('post.index')" :current="request()->routeIs('post.index')" wire:navigate>{{ __('Posts') }}</flux:navlist.item>
                    @endcan

                    <!--registrant route -->
                    @can('registrant-list')
                        <flux:navlist.item icon="user-plus" :href="route('registrant.index')" :current="request()->routeIs('registrant.index')" wire:navigate>{{ __('Registrants') }}</flux:navlist.item>
                    @endcan

                    <!--story route -->
                    @can('story-list')
                        <flux:navlist.item icon="paper-clip" :href="route('story.index')" :current="request()->routeIs('story.index')" wire:navigate>{{ __('Stories') }}</flux:navlist.item>
                    @endcan

                    <!-- user route -->
                    @can('users-list')
                        <flux:navlist.item icon="users" :href="route('user.index')" :current="request()->routeIs('user.index')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    @endcan

                </flux:navlist.group>
            </flux:navlist>


            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        @persist('toast')
        <flux:toast position="top right" class="pt-24"/>
        @endpersist
    </body>
</html>
