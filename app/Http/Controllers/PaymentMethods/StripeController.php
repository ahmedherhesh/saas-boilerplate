<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Jobs\AutoRenewal;
use App\Jobs\RenewSubscriptionInfo;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Customer;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate(['stripe_price_id' => 'required|exists:plans,stripe_price_id']);
        $request->session()->put('email', auth()->user()->email);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create(
            [
                'line_items' => [
                    [
                        'price' => $request->stripe_price_id,
                        'quantity' => 1
                    ]
                ],
                'mode' => 'subscription',
                'success_url' => route('stripe.success'),
                'cancel_url'  => route('dashboard')
            ]
        );
        return redirect()->away($session->url);
    }

    public function webhook(Request $request)
    {
        // The library needs to be configured with your account's secret key.
        // Ensure the key is kept out of any version control system you might be using.
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                break;
            case 'invoice.paid':
                $paymentIntent = $event->data->object;
                // Log::alert($paymentIntent);
                $stripePriceId = $paymentIntent->lines->data[0]['plan'];
                $user = User::whereEmail($request->session()->get('email') ?? $paymentIntent->customer_email)->firstOrFail();
                $plan = Plan::whereStripePriceId($stripePriceId['id'])->firstOrFail();
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'title'   => $plan->title,
                    'price'   => $plan->price,
                    'currency'  => $plan->currency,
                    'images_count'  => $plan->images_count,
                    'payment_method' => 'stripe',
                    'payment_subscription_id' => $paymentIntent->subscription,
                    'payment_plan_id' => $plan->stripe_price_id,
                    'payment_id' =>  $paymentIntent->id,
                    'ends_at' => now()->addDays($plan->days)
                ]);
                if ($plan->period == 'year') {
                    RenewSubscriptionInfo::dispatch($user->id)->delay(now()->addMonths(11));
                    AutoRenewal::dispatch($user->id)->delay(now()->addYear());
                } else {
                    AutoRenewal::dispatch($user->id)->delay(now()->addMonths(1));
                }
                break;
            case 'checkout.session.completed':
                Log::alert($event->data->object);
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        return response('', 200);
    }
    public function disableAutoRenewal()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve the subscription ID
        $subscriptionId = 'sub_123';

        // Retrieve the subscription from Stripe
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);

        // Set the cancel_at_period_end parameter to true
        // $subscription->cancel_at_period_end = true;

        // Save the updated subscription
        $subscription->update(['cancel_at_period_end' => true]);

        // OR

        // $subscription->cancel();
    }
    public function success()
    {
        return inertia('Payments/Status/Success');
    }
}
