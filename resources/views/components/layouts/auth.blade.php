<x-layouts.auth.split :title="$title ?? null">
    {{ $slot }}
    @persist('toast')
    <flux:toast/>
    @endpersist
</x-layouts.auth.split>
