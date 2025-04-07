<?php

use App\Jobs\RegistrantApprovedJob;
use App\Models\Registrant;
use App\Models\Mail;
use App\Models\Reply;
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

    public bool $showReplyFormId = false;

    public string $replySubject = '';

    public string $replyContent = '';

    public string $mailId = '';

    public bool $showRepliesModal = false;

    public Mail $selectedMail;

    public array $replies = [];


    /**
     * Show the reply modal and get the mail id
     * */
    public function showReplyModal($mailId): void
    {

        $this->mailId = $mailId;

        // Set the currently selected mail for reply
        $this->showReplyFormId = true;
    }

    /**
     * Handles the reply process for a mail, including validation, creation, and user notification.
     *
     * @return void
     */
    public function sendReply(): void
    {
        if ($user->can('mail-reply')) {
            // Validate reply content
            $validated = $this->validate([
                'replySubject' => ['required', 'string', 'min:5', 'max:250'],
                'replyContent' => ['required', 'string', 'min:10', 'max:1000'],
            ]);
            // Create a new reply
            Reply::create([
                'mail_id' => $this->mailId,
                'user_id' => auth()->id(), // Ensure the user is logged in
                'subject' => $this->replySubject,
                'message' => $this->replyContent,
            ]);
            //toast message
            Flux::toast(
                'Reply Sent.',
                'Your reply has been sent to the originator.',
                'success',
            );
            // Reset state
            $this->showReplyFormId = false;
            $this->replySubject = '';
            $this->replyContent = '';
        } else {
            abort(403, 'You are not authorised to reply to emails!');
        }


    }

    /**
     * Archive the email associated with the given email ID.
     *
     * @param int $mailId
     * @return void
     */
    public function archiveMail(int $mailId): void
    {
        if ($user->can('mail-destroy')) {
            $mail = Mail::findOrFail($mailId);

            $this->authorize('delete', $mail);

            $mail->delete();

            Flux::toast(
                heading: 'Mail Archived.',
                text: 'The email has been archived successfully.',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorised to archive emails!');
        }

    }

    /**
     * Display the modal to show replied mails for a specific mail.
     *
     * @param mixed $mailId The ID of the mail to fetch and display replies for.
     *
     * @return void
     */
    public function showRepliedMailsModal(mixed $mailId): void
    {
        $this->selectedMail = Mail::with(['replies.user', 'user'])->findOrFail($mailId);

        $this->replies = $this->selectedMail->replies;

        $this->showRepliesModal = true;
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
                ->orWhere('name', 'like', '%' . $this->search . '%')
                ->orWhere('message', 'like', '%' . $this->search . '%')
                ->orWhereRaw("DATE_FORMAT(created_at, '%d %b %Y, %l:%i %p') like ?", [
                    '%' . $this->search . '%',
                ])
                ->orWhereHas('replies.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });

            if (strtolower(trim($this->search)) === 'no replies') {
                $q->orWhereDoesntHave('replies');
            }
        });
    }

    /**
     * Retrieve mail records with their associated replies and users.
     * Applies search filters, sorting, and pagination to the query.
     *
     * @return array
     */
    public function with(): array
    {
        $query = Mail::query()->with('replies.user');

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(5);

        return ['mails' => $paginated];

    }
}; ?>

    <!--start blade view-->
<div>
    <div>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Email') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Emails awaiting a response.') }}</flux:subheading>
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

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$mails" class="table-auto">
        @if ($mails->count() > 0)
            <flux:table.columns>
                <flux:table.column sortable sorted="$sortBy === 'message'" :direction="$sortDirection"
                                   wire:click="sort('message')">Message
                </flux:table.column>
                <flux:table.column>Replies</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
        @endif

        <flux:table.rows>
            @forelse ($mails as $mail)
                <flux:table.row :key="$mail->id">

                    <flux:table.cell class="max-w-md text-wrap">
                        <flux:card class="flex flex-col gap-2">
                            <flux:heading size="sm" level="3">From: {{ $mail->name ?? 'N/A' }}</flux:heading>
                            <flux:heading size="sm" level="3">Email: {{ $mail->email ?? 'N/A' }}</flux:heading>
                            <flux:text
                                size="sm">{{ $mail->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:text>
                            <flux:heading size="sm" level="3">Subject: {{ $mail->subject ?? 'N/A' }}</flux:heading>
                            {{ $mail->message ?? 'N/A' }}
                        </flux:card>
                    </flux:table.cell>

                    <flux:table.cell class="max-w-md text-wrap">

                        @if($mail->replies->count() > 0)
                            <flux:card class="flex flex-col gap-2">
                                @foreach($mail->replies as $reply)
                                    <flux:heading size="sm" level="3">
                                        From: {{ $reply->user->name ?? 'N/A' }}</flux:heading>
                                    <flux:heading size="sm" level="3">
                                        Email: {{ $reply->user->email ?? 'N/A' }}</flux:heading>
                                    <flux:text
                                        size="sm">{{ $reply->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:text>
                                    <flux:heading size="sm" level="3">
                                        Subject: {{ $reply->subject ?? 'N/A' }}</flux:heading>
                                    <flux:text>{{ $reply->message ?? 'N/A' }}</flux:text>
                                @endforeach
                            </flux:card>
                        @else
                            <flux:badge color="red" variant="ghost">No Replies</flux:badge>
                        @endif
                    </flux:table.cell>

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
                    <flux:heading size="xl">No Mail Received Yet</flux:heading>
                </div>
            @endforelse
        </flux:table.rows>

        <!--reply modal-->
        <flux:modal wire:model.self="showReplyFormId" title="Message" size="lg" class="max-w-lg w-auto">
            <form wire:submit.prevent="sendReply">

                <div class="flex flex-col w-full mt-10 gap-5">
                    <flux:heading size="sm" level="3">To: {{ $mail->email ?? 'N/A' }}</flux:heading>
                    <flux:input
                        wire:model="replySubject"
                        name="replySubject"
                        label="Reply Subject"
                        required="true"
                        value="Re: {{ $mail->subject ?? 'N/A'}}"
                        placeholder="Enter your reply subject here..."
                        class="w-full h-full"/>

                    <flux:textarea
                        wire:model="replyContent"
                        name="replyContent"
                        label="Reply Content"
                        rows="20"
                        required="true"
                        placeholder="Enter your reply here..."
                        class="w-full h-full"/>

                </div>
                <div class="flex w-full items-end justify-end gap-4 mt-4">
                    <flux:button type="button" variant="primary" wire:click="showReplyFormId = false">Cancel
                    </flux:button>
                    <flux:button type="submit" variant="danger" class="mt-4">Send</flux:button>
                </div>
            </form>
        </flux:modal>

        <!--show replies modal-->
        <flux:modal wire:model.self="showRepliesModal" title="Replies" size="lg" class="max-w-lg w-auto">

            <div class="flex flex-col w-full mt-10 gap-5">
                @foreach($replies as $reply)
                    <flux:heading size="sm" level="3">From: {{ $reply->user->name ?? 'N/A' }}</flux:heading>
                    <flux:text>{{ $reply->message ?? 'N/A' }}</flux:text>
                @endforeach
            </div>
            <div class="flex w-full items-end justify-end gap-4 mt-4">
                <flux:button type="button" variant="primary" wire:click="showReplies = false">Cancel
                </flux:button>
            </div>
        </flux:modal>
    </flux:table>
</div>
