<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ $title ?? 'Default Title' }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

<flux:header
    class="w-full border-b px-0! border-zinc-200 dark:border-zinc-700 flex flex-col transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">

    <!--navbar-->
    <flux:container class="flex flex-row space-x-20 w-full items-center justify-between">
        <a href="{{ route('home') }}" class="ml-2 mr-5 items-center space-x-2 lg:ml-0 hidden lg:flex" wire:navigate>
            <x-app-logo/>
        </a>
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2"/>

        <flux:navbar class="hidden lg:flex justify-end">
            <x-navbar-items/>
        </flux:navbar>

        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
                     aria-label="Toggle dark mode"/>
    </flux:container>

    <flux:separator size="lg" variant="subtle"/>

    <!--hero-->
    <flux:container class="flex flex-row gap-x-10 max-w-7xl text-center justify-between py-5">
        <flux:container class="flex flex-col px-0! sm:mb-6 lg:text-left lg:mb-0 lg:pl-0">
            <h1 class="mb-4 font-bold leading-tight text-4xl">
                Royal Army Dental Corps Association
            </h1>
            <flux:heading size="lg" level="2" class="mb-4 text-zinc-700! dark:text-zinc-400!">
                We aim to foster a safe and healthy environment for old comrades of the former Royal Army Dental
                Corps
                (RADC) to meet and socialise.
            </flux:heading>

            <a href="{{ route('register') }}">
                <flux:button variant="danger" icon-trailing="arrow-right" aria-label="Register button to join the community"
                >
                    Register
                </flux:button>
            </a>
        </flux:container>

        <!--youtube container-->
        <flux:container class="hidden lg:flex flex-col sm:mb-6 lg:text-left lg:mb-0 lg:pr-0">
            <iframe class="md:aspect-video md:h-42 lg:h-64 2xl:h-80 rounded-lg"
                    aria-label="Video of David Arkush, Japanese Prisoner of War."
                    src="https://www.youtube.com/embed/LNWeiDAxU10" title="YouTube video player"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </flux:container>
    </flux:container>
</flux:header>

<!--mobile sidebar-->
<flux:sidebar stashable sticky
              class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>
    <a href="{{ route('home') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0 lg:hidden" wire:navigate>
        <x-app-logo/>
    </a>

    <x-navbar-items/>

</flux:sidebar>

{{ $slot }}

<!--footer-->
<flux:footer
    class="w-full p-5 mt-10 border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 flex flex-col">


    <flux:container class="flex flex-col w-full px-0! md:flex-row mx-auto max-w-7xl items-center justify-between mt-5">
        <flux:container class="flex flex-col pl-0! justify-start w-full text-center mb-5 md:mb-0 md:flex-row">
            <flux:text>
                Copyright &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </flux:text>
        </flux:container>

        <flux:container class="flex justify-center text-center flex-col gap-x-3 w-full mb-5 md:mb-0 md:flex-row">
            <flux:link href="#" class="text-sm">Privacy Policy</flux:link>
        </flux:container>

        <flux:container class="flex flex-col lg:pr-0! w-full justify-end text-center mb-5 md:mb-0 md:flex-row">
            <flux:text>
                Made with <span class="accent-zinc-500">&hearts;</span> by <strong
                    class=" text-zinc-900 dark:text-zinc-50">Strongsy</strong>
            </flux:text>
        </flux:container>
    </flux:container>

</flux:footer>

@fluxScripts
@persist('toast')
<flux:toast position="top right" class="pt-24"/>
@endpersist
</body>
</html>

