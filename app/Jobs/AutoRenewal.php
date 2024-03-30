<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\Stripe;

class AutoRenewal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscription = $this->user->subscriptions->first();
        if ($subscription?->auto_renewal) {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $customer = \Stripe\Customer::create([
                'email' => $this->user->email,
                'source' => 'tok_visa', // Stripe token representing a card
            ]);

            \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    [
                        'price' => $subscription->payment_plan_id, // Stripe price ID
                    ],
                ],
            ]);
        }
    }
}
