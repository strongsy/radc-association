<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    @persist('toast')
    <flux:toast position="top right" class="pt-24"/>
    @endpersist
</x-layouts.app.sidebar>
