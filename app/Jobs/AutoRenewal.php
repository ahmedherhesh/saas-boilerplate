<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
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

    public function stripeRenew($subscription)
    {
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

    public function paypalRenew($subscription)
    {

        $accessToken = 'Basic ' . base64_encode(config('paypal.client_id') . ':' . config('paypal.client_secret'));

        // Set the subscription details
        $body = [
            'plan_id' => $subscription->payment_plan_id,
            'subscriber' => [
                'email_address' => $subscription->subscriber_email,
            ],
        ];
        // Create the subscription
        Http::withHeaders([
            'Authorization' => $accessToken
        ])->post(config('paypal.base_url') . '/v1/billing/subscriptions', $body);
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscription = $this->user->subscriptions->first();
        if ($subscription?->auto_renewal) {
            if ($subscription->payment_method == 'stripe') {
                $this->stripeRenew($subscription);
            } else {
                $this->paypalRenew($subscription);
            }
        }
    }
}
