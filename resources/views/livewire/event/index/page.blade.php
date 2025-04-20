<?php

use App\Notifications\CancelledEventNotification;
use App\Traits\WithSortingAndSearching;
use App\Jobs\CancelledEventEmailJob;
use Illuminate\Http\RedirectResponse;

use Livewire\Attributes\Computed;
use Livewire\Features\SupportRedirects\Redirector;
use Livewire\Volt\Component;
use App\Models\Event;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

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
            $q->where('title', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                });
        });

    }

    /**
     * Redirects to the event creation route.
     *
     * @return Redirector
     */
    public function createEvent(): Redirector
    {
        return redirect()->route('event.create');
    }


    /**
     * Handles the redirection to the event view page.
     *
     * @param mixed $eventId The identifier of the event to view.
     * @return Redirector Redirects to the route associated with viewing the specified event.
     */
    public function viewEvent(mixed $eventId): Redirector
    {
        return redirect()->route('event.read', ['event' => $eventId]);
    }


    /**
     * Cancel an event by its ID.
     *
     * This method checks if the authenticated user has permission to cancel the event,
     * dispatches a job to send cancellation emails, and deletes the event. It also
     * displays a success message upon successful cancellation or aborts with a 403
     * error if the user lacks the necessary permissions.
     *
     * @param $eventId
     * @return void
     *
     */
    public function cancelEvent($eventId): void
    {
        if (!Auth::user()?->can('event-destroy')) {
            abort(403, 'You are not authorised to delete events!');
        }

        $event = Event::findOrFail($eventId);

        $event->attendees->each(function ($user) use ($event) {
            $user->notify(new CancelledEventNotification($event));
        });

        $event->delete();

        Flux::toast(
            heading: 'Event Cancelled.',
            text: 'The event has been cancelled successfully.',
            variant: 'success',
        );
    }


    /**
     * Retrieve mail records with their associated replies and users.
     * Applies search filters, sorting, and pagination to the query.
     *
     * @return array
     */
    public function with(): array
    {
        //Event::cleanExpired();

        $query = Event::query()->with('user');
        $query = $this->applySearchFilters($query);
        $query = $this->applySorting($query);


        return ['events' => $query->paginate(10)];
    }
}; ?>

<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Events') }}</flux:heading>
            <flux:subheading size="lg">{{ __('See what\'s new in Events') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between mb-5">
        <div class="flex items-center">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>
        </div>

        <div class="flex items-center">
            @can('event-create')
                <flux:button variant="primary" wire:click="createEvent()">Create Event</flux:button>
            @endcan
        </div>
    </div>

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$events" class="table-auto">
        @if ($events->count() > 0)
            <flux:table.columns>
                <flux:table.column sortable sorted="$sortBy === 'Created By'" :direction="$sortDirection"
                                   wire:click="sort('Created By')">Created By
                </flux:table.column>
                <flux:table.column sortable sorted="$sortBy === 'Title'" :direction="$sortDirection"
                                   wire:click="sort('Title')">Title
                </flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Attending</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
        @endif

        <flux:table.rows>
            @forelse ($events as $event)
                <flux:table.row :key="$event->id">
                    <flux:table.cell variant="strong">{{ $event->user->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $event->title ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->date->format('d M Y') ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->time->format('g:i A') ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->attendees->count() ?? 'N/A' }}</flux:table.cell>

                    <!--actions-->
                    <flux:table.cell class="max-w-lg">
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>

                                @can('event-read')
                                    <flux:menu.item icon="eye"
                                                    wire:click="viewEvent({{ $event->id ?? 'N/A' }})">View Event
                                    </flux:menu.item>
                                @endcan


                                @can('event-destroy')
                                    <flux:menu.item icon="archive-box-arrow-down"
                                                    wire:click="cancelEvent({{ $event->id ?? 'N/A' }})"
                                                    wire:confirm.prompt="Are you sure you want to cancel this event?\n\nType CANCEL to confirm|CANCEL">
                                        Cancel Event
                                    </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <div class="flex justify-center items-center h-full">
                    <flux:badge size="xl" color="teal" variant="subtle" class="my-3">
                        <flux:heading size="xl">No Events Created Yet</flux:heading>
                    </flux:badge>
                </div>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
