<?php

use App\Jobs\RegistrantApprovedJob;
use App\Models\Registrant;
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
        $validatedData['password'] = Hash::make($temporaryPassword);

        $user = User::create($validatedData);

        RegistrantApprovedJob::dispatch($validatedData);
        $user->sendEmailVerificationNotification();
    }

    /**
     * Deletes a registrant from the system.
     *
     * @param int|string $registrantId
     *
     * @return void
     */
    protected function deleteRegistrant(int|string $registrantId): void
    {
        $registrant = Registrant::findOrFail($registrantId);
        $registrant->delete();

        Flux::toast(
            heading: 'Registrant Deleted.',
            text: 'The registrant has been deleted successfully.',
            variant: 'success',
        );
    }

    #[computed]
    public function with(): array
    {
        $query = Registrant::query();

        // Apply search and order
        $filtered = $this->search($query);

        $sorted = $filtered->orderBy($this->sortBy, $this->sortDirection);

        // Paginate the data
        $paginated = $sorted->paginate(10);

        return ['registrants' => $paginated];

    }
}; ?>

<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Registrants') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('New registrants awaiting approval') }}</flux:subheading>
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

    <flux:table :paginate="$registrants">
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
                                    color="{{ $registrant->community->variant() ?? 'N/A' }}">{{ $registrant->community }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $registrant->membership->variant() ?? 'N/A' }}">{{ $registrant->membership }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ $registrant->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item icon="lock-open"
                                                wire:click="approveRegistrant({{ $registrant->id }})">Approve
                                </flux:menu.item>
                                <flux:menu.item icon="user-minus" wire:click="deleteRegistrant({{ $registrant->id }})">
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <div class="flex justify-center items-center h-full">
                    <flux:heading size="xl">No Registrants</flux:heading>
                </div>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Livewire component example code...
        use \Livewire\WithPagination;

        public $sortBy = 'date';
        public $sortDirection = 'desc';

        public function sort($column) {
            if ($this->sortBy === $column) {
                $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                $this->sortBy = $column;
                $this->sortDirection = 'asc';
            }
        }

        #[\Livewire\Attributes\Computed]
        public function orders()
        {
            return \App\Models\Order::query()
                ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
                ->paginate(5);
        }
    -->
</div>
