<?php

use App\Jobs\RegistrantApprovedJob;
use App\Models\Registrant;
use App\Models\User;
use App\Traits\WithSortingAndSearching;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, WithSortingAndSearching;


    /**
     * Approve a registrant and convert to user.
     *
     * @param Registrant $registrant
     *
     * @return void
     */
    public function approveRegistrant(Registrant $registrant): void
    {
        $validatedData = $this->validateRegistrant($registrant);
        $temporaryPassword = $this->generateTemporaryPassword();
        $this->createUserFromRegistrant($validatedData, $temporaryPassword);

        $registrant->delete();

        Flux::toast(
            heading: 'Registrant Approved.',
            text: 'The registrant has been approved and moved to the users table.',
            variant: 'success',
        );
    }

    /**
     * Validate registrant's data.
     *
     * @param Registrant $registrant
     *
     * @return array
     */
    private function validateRegistrant(Registrant $registrant): array
    {
        return Validator::make($registrant->toArray(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'community' => 'required|string|max:255',
            'membership' => 'required|string|max:255',
            'affiliation' => 'required|string|max:750',
            'is_subscribed' => 'boolean',
        ])->validate();
    }

    /**
     * Generate a random temporary password.
     *
     * @return string
     */
    private function generateTemporaryPassword(): string
    {
        return Str::random(10);
    }

    /**
     * Create a user from the validated registrant data.
     *
     * @param array $validatedData
     * @param string $temporaryPassword
     *
     * @return void
     */
    private function createUserFromRegistrant(array $validatedData, string $temporaryPassword): void
    {
        if (Auth::user() && Auth::user()->can('user-create')) {
            $validatedData['password'] = Hash::make($temporaryPassword);

            $user = User::create($validatedData);

            $user->assignRole('user');

            RegistrantApprovedJob::dispatch($validatedData);
            $user->sendEmailVerificationNotification();
        } else {
            abort(403, 'You are not authorised to create users!');
        }

    }

    /**
     * Deletes a registrant from the system.
     *
     * @param int|string $registrantId
     *
     * @return void
     */
    public function deleteRegistrant(int|string $registrantId): void
    {
        if ($user->can('user-destroy')) {
            $registrant = Registrant::findOrFail($registrantId);

            $this->authorize('delete', $registrant);

            $registrant->delete();

            Flux::toast(
                heading: 'Registrant Deleted.',
                text: 'The registrant has been deleted successfully.',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorised to delete registrants!');
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

    #[computed]
    public function with(): array
    {
        $query = Registrant::query();

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'registrants' => $paginated,
        ];

    }
}; ?>

<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Registrants') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Registrants awaiting approval') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between gap-4">
        <div class="flex items-center gap-4">
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

    <flux:table :paginate="$registrants">
        @if ($registrants->count() > 0)
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
                                   wire:click="sort('created_at')">Registered At
                </flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
        @endif

        <flux:table.rows>
            @forelse ($registrants as $registrant)
                <flux:table.row :key="$registrant->id">
                    <flux:table.cell class="flex items-center gap-3">

                        <flux:dropdown hover="true" position="bottom center">
                            <flux:avatar as="button" size="sm">{{ $registrant->initials() ?? 'N/A' }}</flux:avatar>

                            <flux:popover class="relative max-w-[15rem]">

                                <flux:heading class="mt-2">{{ $registrant->name ?? 'N/A' }}</flux:heading>

                                <flux:separator variant="subtle" class="mt-2"></flux:separator>

                                <flux:text class="mt-3">
                                    {{ $registrant->affiliation ?? 'N/A' }}
                                </flux:text>

                            </flux:popover>
                        </flux:dropdown>
                    </flux:table.cell>

                    <flux:table.cell variant="strong">{{ $registrant->name ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>{{ $registrant->email ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $registrant->community->variant() ?? 'N/A' }}">{{ $registrant->community ?? 'N/A' }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $registrant->membership->variant() }}">{{ $registrant->membership  ?? 'N/A' }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ $registrant->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>

                    <!--actions-->
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item icon="lock-open"
                                                wire:click="approveRegistrant({{ $registrant->id ?? 'N/A' }})"
                                                wire:confirm="Are you sure that you want to authorise this registrant to access the site?">
                                    Approve
                                </flux:menu.item>
                                <flux:menu.item icon="user-minus"
                                                wire:click="deleteRegistrant({{ $registrant->id ?? 'N/A' }})"
                                                wire:confirm="Are you sure that you want to delete this registrant?">
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <div class="flex justify-center items-center h-full">
                    <flux:badge size="xl" color="teal" variant="subtle" class="my-3">
                    <flux:heading size="xl">No Registrants Yet</flux:heading>
                    </flux:badge>
                </div>
            @endforelse
        </flux:table.rows>
    </flux:table>

</div>
