<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RenewSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:renewInfo {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to user to tell him that subscription will be renewal after one month from now';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        $user = User::findOrFail($userId);

        Mail::send('send-mail', [], function ($massage) use ($user) {
            $massage->to($user->email)->subject('Action Required For Your Subsription In ' . env('APP_NAME'));
        });

        $this->info('Email sent to user: ' . $user->email);
    }
}
