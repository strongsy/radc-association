<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ $title ?? 'Default Title' }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

<flux:header
    class="w-full border-b px-0! border-zinc-200 dark:border-zinc-700 flex flex-col transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
    <x-front-end-header />
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


   <x-front-end-footer />

</flux:footer>

@fluxScripts
@persist('toast')
<flux:toast position="top right" class="pt-24"/>
@endpersist
</body>
</html>

