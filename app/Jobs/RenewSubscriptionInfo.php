<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RenewSubscriptionInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $user_id;
    public function __construct($user_id)
    {
       $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::findOrFail($this->user_id);

        Mail::send('send-mail', [], function ($massage) use ($user) {
            $massage->to($user->email)->subject('Action Required For Your Subsription In ' . env('APP_NAME'));
        });

    }
}
