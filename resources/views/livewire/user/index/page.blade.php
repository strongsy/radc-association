<?php

use App\Jobs\RegistrantApprovedJob;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    //import Livewire\Attributes\Url
    #[Url]
    public string $sortBy = 'name';

    //import Livewire\Attributes\Url
    #[Url]
    public string $sortDirection = 'desc';

    /**
     * Handle the updated search event and reset the pagination.
     *
     * @return void
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Apply search filters to the provided query based on the current search term.
     *
     * @param mixed $query
     *
     * @return mixed
     */
    protected function search(mixed $query): mixed
    {
        return $this->search === ''
            ? $query
            : $query
                ->where('email', 'like', '%' . $this->search . '%')
                ->orWhere('name', 'like', '%' . $this->search . '%');
    }


    /**
     * Deletes a user from the system.
     *
     * @param int|string $userId
     *
     * @return void
     */
    protected function deleteUser(int|string $userId): void
    {
        $user = User::findOrFail($userId);
        $user->delete();

        Flux::toast(
            heading: 'User Deleted.',
            text: 'The user has been deleted successfully.',
            variant: 'success',
        );
    }

    #[computed]
    public function with(): array
    {
        $query = User::query();

        // Apply search and order
        $filtered = $this->search($query);

        $sorted = $filtered->orderBy($this->sortBy, $this->sortDirection);

        // Paginate the data
        $paginated = $sorted->paginate(10);

        return ['users' => $paginated];

    }
}; ?>

<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Users') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('List of approved users') }}</flux:subheading>
            <flux:separator variant="subtle"/>
        </div>
    </div>

    <!-- search field -->
    <div class="grid grid-cols-12 items-center justify-between gap-4 mb-6">
        <div class="grid col-span-2 items-center gap-4">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>
        </div>
    </div>

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>Affiliation</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">
                Name
            </flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'community'" :direction="$sortDirection" wire:click="sort('community')">Community</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'membership'" :direction="$sortDirection" wire:click="sort('membership')">Membership</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Registered At</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="flex items-center gap-3">

                        <flux:dropdown hover="true" position="bottom center">
                            <flux:avatar as="button" size="sm">{{ $user->initials() ?? 'N/A' }}</flux:avatar>

                            <flux:popover class="relative max-w-[15rem]">

                                <flux:heading class="mt-2">{{ $user->name ?? 'N/A' }}</flux:heading>

                                <flux:separator variant="subtle" class="mt-2"></flux:separator>

                                <flux:text class="mt-3">
                                    {{ $user->affiliation ?? 'N/A' }}
                                </flux:text>

                            </flux:popover>
                        </flux:dropdown>
                    </flux:table.cell>

                    <flux:table.cell variant="strong">{{ $user->name ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>{{ $user->email ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $user->community->variant() ?? 'N/A' }}">{{ $user->community }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $user->membership->variant() ?? 'N/A' }}">{{ $user->membership }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ $user->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item icon="shield-check"
                                                wire:click="approveRegistrant({{ $user->id }})">Roles
                                </flux:menu.item>
                                <flux:menu.item icon="user-minus" wire:click="deleteUser({{ $user->id }})">
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <div class="flex justify-center items-center h-full">
                    <flux:heading size="xl">No Users</flux:heading>
                </div>
            @endforelse
        </flux:table.rows>
    </flux:table>

</div>
