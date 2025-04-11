<?php

namespace App\Jobs;

use App\Mail\ContactUsMail;
use App\Mail\ContactUsAdminMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;

class ContactUsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;
    protected array $admin;

    //protected array data and admin;
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param array $admin
     */
    public function __construct(array $data, array $admin)
    {
        $this->data = $data;
        $this->admin = $admin;
    }

    public function handle(): void
    {
        Log::info('Handling job with mail data: ', $this->data);
        // Send email to the user
        Mail::to($this->data['email'])->send(new ContactUsMail($this->data));
        //Send email to admin
        foreach ($this->admin as $adminEmail) {
            Mail::to($adminEmail)->send(new ContactUsAdminMail(($this->data)));
        }
    }
}
