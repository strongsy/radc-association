<?php

use App\Traits\WithSortingAndSearching;
use Illuminate\Http\RedirectResponse;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Volt\Component;
use App\Models\Event;
use Livewire\WithPagination;

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
                ->orWhere('name', 'like', '%' . $this->search . '%');
        });
    }

    public function createEvent()
    {
        return redirect()->route('event.create');
    }

    /**
     * Retrieve mail records with their associated replies and users.
     * Applies search filters, sorting, and pagination to the query.
     *
     * @return array
     */
    public function with(): array
    {
        Event::cleanExpired();

        $query = Event::query()->with('user');

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return ['events' => $paginated];

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
    <div class="flex flex-1/2 items-center justify-between">
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

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$events" class="table-auto">
        @if ($events->count() > 0)
            <flux:table.columns>
                <flux:table.column sortable sorted="$sortBy === 'title'" :direction="$sortDirection"
                                   wire:click="sort('title')">Title
                </flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
        @endif

        <flux:table.rows>
            @forelse ($events as $event)
                <flux:table.row :key="$event->id">

                    <flux:table.cell variant="strong">{{ $event->title ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $event->user->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->date->format('d M Y') ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell
                        size="sm">{{ $event->time->format('g:i A') ?? 'N/A' }}</flux:table.cell>

                    <!--actions-->
                    <flux:table.cell class="max-w-lg">
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>

                                @can('mail-reply')
                                    <flux:menu.item icon="envelope"
                                                    wire:click="showReplyModal({{ $mail->id ?? 'N/A' }})">Reply
                                    </flux:menu.item>
                                @endcan


                                @can('mail-destroy')
                                    <flux:menu.item icon="archive-box-arrow-down"
                                                    wire:click="archiveMail({{ $mail->id ?? 'N/A' }})"
                                                    wire:confirm="Are you sure you want to archive this email?">
                                        Archive
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

        <!--reply modal-->
        <flux:modal wire:model.self="showReplyFormId" title="Message" size="lg" class="max-w-lg w-auto">
            <form wire:submit.prevent="sendReply()">
                @csrf
                <div class="flex flex-col w-full mt-10 gap-5">
                    <flux:input
                        wire:model="name"
                        name="name"
                        label="To"
                        disabled="true"
                        required="true"
                        value="{{ $mail->name ?? 'N/A' }}"
                        placeholder="Enter your reply subject here..."
                        class="w-full h-full"/>

                    <flux:input
                        wire:model="email"
                        name="email"
                        label="Email"
                        disabled="true"
                        required="true"
                        value="{{ $mail->user->email ?? 'N/A'}}"
                        placeholder="Enter your reply subject here..."
                        class="w-full h-full"/>

                    <flux:input
                        wire:model="subject"
                        name="subject"
                        label="Subject"
                        required="true"
                        value="{{ $mail->subject ?? 'N/A'}}"
                        placeholder="Enter your reply subject here..."
                        class="w-full h-full"/>

                    <flux:editor
                        wire:model="message"
                        name="message"
                        label="Content"
                        rows="20"
                        required="true"
                        placeholder="Enter your reply here..."
                        class="w-full h-full"/>

                </div>
                <div class="flex w-full items-end justify-end gap-4 mt-4">
                    <flux:button type="button" variant="primary" wire:click="cancelReplyModal()">Cancel
                    </flux:button>
                    <flux:button type="submit" variant="danger" class="mt-4">Send</flux:button>
                </div>
            </form>
        </flux:modal>
    </flux:table>
</div>
