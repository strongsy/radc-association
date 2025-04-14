@props([
    'search' => null,
    'sortBy' => null,
    'sortDirection' => null,
])

<div>
    <div class="flex space-x-2 my-3">
        {{-- Render search badge if applicable --}}
        @if($search)
            <flux:badge size="sm" color="emerald" variant="ghost">
                <flux:text>"{{ $search }}"</flux:text>

                <flux:button
                    size="sm"
                    type="button"
                    icon="x-mark"
                    variant="subtle"
                    wire:click="clearFilter('search')"
                ></flux:button>
            </flux:badge>
        @endif

        {{-- Render sort badge if applicable --}}
        @if($sortBy)
            <flux:badge color="rose" variant="ghost">
                <flux:text>{{ ucfirst($sortBy) ?? 'asc' }} ({{ ucfirst($sortDirection) ?? 'asc' }})</flux:text>
                <flux:button
                    size="sm"
                    type="button"
                    icon="x-mark"
                    variant="subtle"
                    wire:click="clearFilter('sort')"
                />
            </flux:badge>
        @endif
    </div>
</div>
