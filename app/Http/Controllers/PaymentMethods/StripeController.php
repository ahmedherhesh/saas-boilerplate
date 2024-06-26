<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Jobs\AutoRenewal;
use App\Jobs\RenewSubscriptionInfo;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate(['stripe_price_id' => 'required|exists:plans,stripe_price_id']);
        $request->session()->put('email', auth()->user()->email);

        Stripe::setApiKey(config('stripe.secret'));

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
        Stripe::setApiKey(config('stripe.secret'));

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = config('stripe.webhook_secret');

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
                $stripePriceId = $paymentIntent->lines->data[0]['plan'];
                $user = User::whereEmail($request->session()->get('email') ?? $paymentIntent->customer_email)->firstOrFail();
                $plan = Plan::whereStripePriceId($stripePriceId['id'])->firstOrFail();
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'title'   => $plan->title,
                    'price'   => $plan->price,
                    'currency'  => $plan->currency,
                    'images_count'  => $plan->images_count,
                    'payment_method' => 'stripe',
                    'payment_subscription_id' => $paymentIntent->subscription,
                    'payment_plan_id' => $plan->stripe_price_id,
                    'payment_id' =>  $paymentIntent->id,
                    'active' => 1,
                    'ends_at' => now()->addDays($plan->days)
                ]);
                if ($plan->period == 'year') {
                    RenewSubscriptionInfo::dispatch($user)->delay(now()->addMonths(11));
                    AutoRenewal::dispatch($user)->delay(now()->addYear());
                } else {
                    AutoRenewal::dispatch($user)->delay(now()->addDays($plan->days));
                }
                break;
            case 'checkout.session.completed':

                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        return response('', 200);
    }

    public function autoRenewalDisable()
    {

        $subscribed = auth()->user()->subscribed;
        if ($subscribed->payment_method == 'stripe') {

            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Retrieve the subscription ID
            $subscriptionId = $subscribed->payment_subscription_id;

            // Retrieve the subscription from Stripe
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            // Set the cancel_at_period_end parameter to true
            // $subscription->cancel_at_period_end = true;

            $subscription->update($subscriptionId, ['cancel_at_period_end' => true]);
        }

        // update in the database
        $subscribed->update(['auto_renewal' => 0]);

        return back();
    }
    public function success()
    {
        return inertia('Payments/Status/Success');
    }
}
