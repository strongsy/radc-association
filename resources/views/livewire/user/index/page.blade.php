<?php

use App\Jobs\RegistrantApprovedJob;
use App\Models\User;
use App\Traits\WithSortingAndSearching;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    public bool $showUserRolesModal = false;

    public array $selectedRoles = [];

    public array $availableRoles = [];

    public array $user = [];

    public array $roleColors = [
        'super-admin' => 'yellow',
        'admin' => 'red',
        'editor' => 'blue',
        'moderator' => 'green',
        'user' => 'purple',
    ];


    protected array $rules = [
        'selectedRoles' => 'array',
        'selectedRoles.*' => 'in_array:availableRoles', // Validates all items against $availableRoles
    ];


    /**
     * Assign roles to a user by loading the user's current roles, defining available roles,
     * and pre-selecting already assigned roles for user role management.
     *
     * @param mixed $user_id The ID of the user whose roles are being managed.
     *
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the user with the specified ID is not found.
     */
    #[NoReturn] public function assignRolesToUser(mixed $user_id): void
    {
        if (Auth::user() && Auth::user()->can('user-update')) {
            $user = User::with('roles')->findOrFail($user_id);

            $this->user = [
                'id' => $user->id ?? 'No ID',
                'name' => $user->name ?? 'No Name',
                'roles' => $user->roles ? $user->roles->pluck('name')->toArray() : [],
            ];

            // Load all available roles (hardcoded or fetched from a Role model)
            $this->availableRoles = ['admin', 'editor', 'moderator', 'user'];

            // Pre-select roles already assigned to the user
            $this->selectedRoles = $this->user['roles'] ?? [];

            $this->showUserRolesModal = true;
        } else {
            abort(403, 'You are not authorised to assign roles to users!');
        }

    }

    /**
     * Validate and save the selected roles for the user, then update the state and display a success notification.
     *
     * - Validates the selected roles against an array of available roles.
     * - Re-fetches the user model by ID and updates their roles using Spatie's `syncRoles` method.
     * - Resets modal visibility and related state properties after successful save.
     * - Optionally triggers a success notification for confirmation.
     *
     * @return void
     */
    public function saveRoles(): void
    {
        if (Auth::user() && Auth::user()->can('user-update')) {
            $this->validate([
                'selectedRoles' => 'array',
                'selectedRoles.*' => 'in:' . implode(',', $this->availableRoles),
            ]);

            // Re-fetch the user model
            $user = User::findOrFail($this->user['id']);
            $user->syncRoles($this->selectedRoles); // Spatie's method to sync roles

            // Close modal and reset state
            $this->showUserRolesModal = false;
            $this->user = [];
            $this->selectedRoles = [];

            // Optional: Add confirmation notification
            Flux::toast(
                heading: 'Success',
                text: 'User roles updated successfully.',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorised to update user roles!');
        }


    }


    /**
     * Delete a user based on the provided user ID.
     *
     * @param int|string $userId The ID of the user to be deleted.
     *
     * @return void
     */
    public function deleteUser(int|string $userId): void
    {
        if (Auth::user() && Auth::user()->can('user-destroy')) {
            $user = User::findOrFail($userId);

            $this->authorize('delete', $user);

            $user->delete();

            Flux::toast(
                heading: 'User Deleted.',
                text: 'The user has been deleted successfully.',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorised to delete users!');
        }

    }

    /**
     * Apply search filters to a query.
     *
     * @param $query
     * @return mixed
     */
    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('email', 'like', '%' . $this->search . '%')
                ->orWhere('name', 'like', '%' . $this->search . '%');
        });
    }

    /**
     * Retrieve the list of users with their associated roles, applying search, sorting, and pagination.
     *
     * @return array
     */
    public function with(): array
    {
        $query = User::query()->with('roles:name');

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'users' => $paginated,
        ];


    }
}; ?>

    <!--blade view-->
<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Users') }}</flux:heading>
            <flux:subheading size="lg">{{ __('List of approved users') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between">
        <div class="flex items-center">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>
        </div>
    </div>

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$users">
        @if ($users->count() > 0)
            <flux:table.columns>
                <flux:table.column>Affiliation</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                                   wire:click="sort('name')">
                    Name
                </flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'community'" :direction="$sortDirection"
                                   wire:click="sort('community')">Community
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'membership'" :direction="$sortDirection"
                                   wire:click="sort('membership')">Membership
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                                   wire:click="sort('created_at')">Authorised At
                </flux:table.column>
                <flux:table.column>Roles</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
        @endif

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="flex items-center gap-3">

                        <flux:dropdown hover="true" position="bottom center">
                            <flux:avatar icon="eye" as="button" size="sm"></flux:avatar>

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

                    <flux:table.cell>{{ $user['created_at']->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        @foreach ($user->roles as $role)
                            <flux:badge size="sm"
                                        color="{{ $roleColors[$role->name] }}">{{ ucfirst($role->name) ?? 'N/A' }}</flux:badge>
                        @endforeach
                    </flux:table.cell>

                    <!--actions-->
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                @can('user-update')
                                    <flux:menu.item icon="shield-check"
                                                    wire:click="assignRolesToUser({{ $user->id ?? 'N/A' }})">Roles
                                    </flux:menu.item>
                                @endcan

                                @can('user-destroy')
                                    <flux:menu.item icon="user-minus" wire:click="deleteUser({{ $user->id ?? 'N/A' }})"
                                                    wire:confirm.prompt="Are you sure you want to delete this user?\n\nType DELETE to confirm|DELETE">
                                        Delete
                                    </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:badge size="xl" color="teal" variant="subtle" class="my-3">
                    <flux:heading size="xl">No Users Yet</flux:heading>
                </flux:badge>
            @endforelse
        </flux:table.rows>

        <!--roles modal-->
        <flux:modal wire:model.self="showUserRolesModal" title="Assign Roles" size="lg" class="max-w-sm w-auto">
            <form wire:submit.prevent="saveRoles">

                <div class="grid grid-cols-2 items-center justify-between gap-4 mt-5">
                    @foreach ($availableRoles as $role)
                        <label class="block">
                            <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}"/>
                            <span class="ml-2">{{ ucfirst($role) ?? 'N/A' }}</span>
                        </label>
                    @endforeach

                </div>
                <div class="flex w-full items-end justify-end gap-4 mt-4">
                    <flux:button type="button" variant="primary" wire:click="showUserRolesModal = false">Cancel
                    </flux:button>
                    <flux:button type="submit" variant="danger" class="mt-4">Save</flux:button>
                </div>

            </form>
        </flux:modal>
    </flux:table>

</div>
