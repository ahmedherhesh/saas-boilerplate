<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Jobs\AutoRenewal;
use App\Jobs\RenewSubscriptionInfo;
use App\Models\Plan;
use App\Models\Subscription;
use App\Traits\PaypalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Srmklive\PayPal\Services\PayPal;


class PayPalController extends Controller
{
    use PaypalTrait;

    public function index(Request $request)
    {
        $request->validate([
            'paypal_plan_id' => 'required|exists:plans,paypal_plan_id',
        ]);
        return view('paypal_checkout');
    }
    // for continue subscription after create we will add the approved data if the subscription is approved
    public function continue(Request $request)
    {
        $request->validate([
            'orderID' => 'required',
            'subscriptionID' => 'required|exists:subscriptions,payment_subscription_id'
        ]);

        $subscription = Subscription::wherePaymentSubscriptionId($request->subscriptionID)->first();

        $subscription->update([
            'user_id' => auth()->id(),
            'active' => 1,
            'payment_id' => $request->orderID,
        ]);

        if ($subscription->plan->period == 'year') {
            RenewSubscriptionInfo::dispatch($subscription->user)->delay(now()->addMonths(11));
            AutoRenewal::dispatch($subscription->user)->delay(now()->addMinute());
        } else {
            AutoRenewal::dispatch($subscription->user)->delay(now()->addDays($subscription->plan->days));
        }
    }
    // create access token
    public function resolveAccessToken()
    {
        $credentials = base64_encode(config('paypal.client_id') . ':' . config('paypal.client_secret'));

        return "Basic {$credentials}";
    }
    // use token to authorization
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }


    public function webhook(Request $request)
    {

        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);
        $webhook_body = json_decode(file_get_contents('php://input'));


        $status = $this->makeRequest(
            'POST',
            '/v1/notifications/verify-webhook-signature',
            [],
            [
                'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'],
                'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'],
                'cert_url' => $headers['PAYPAL-CERT-URL'],
                'auth_algo' => $headers['PAYPAL-AUTH-ALGO'],
                'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'],
                'webhook_id' => config('paypal.webhook_id'),
                'webhook_event' => $webhook_body
            ],
            [],
            $isJSONRequest = true,
        );

        $result = json_decode($status, true);
        if ($result['verification_status'] == "SUCCESS") {
            switch ($webhook_body->event_type) {

                case 'BILLING.SUBSCRIPTION.CREATED':
                    // Subscription created, handle the event
                    $plan = Plan::wherePaypalPlanId($webhook_body->resource->plan_id)->first();
                    $subscription = Subscription::create([
                        'plan_id' => $plan->id,
                        'title'   => $plan->title,
                        'price'   => $plan->price,
                        'currency'  => $plan->currency,
                        'images_count'  => $plan->images_count,
                        'payment_plan_id' => $webhook_body->resource->plan_id,
                        'payment_subscription_id' => $webhook_body->resource->id,
                        'payment_method' => 'paypal',
                        'subscriber_email' => property_exists($webhook_body->resource, 'subscriber') ? $webhook_body->resource->subscriber->email_address : null,
                        'ends_at' => now()->addDays($plan->days)
                    ]);
                    if ($subscription->subscriber_email) {

                        $last_subscription = Subscription::where('user_id', '!=', null)
                            ->where('subscriber_email', $subscription->subscriber_email)->latest()->first();

                        $subscription->update([
                            'user_id' => $last_subscription->user_id,
                            'active'  => 1
                        ]);
                        
                        if ($subscription->plan->period == 'year') {
                            RenewSubscriptionInfo::dispatch($subscription->user)->delay(now()->addMonths(11));
                            AutoRenewal::dispatch($subscription->user)->delay(now()->addMinute());
                        } else {
                            AutoRenewal::dispatch($subscription->user)->delay(now()->addDays($subscription->plan->days));
                        }
                    }

                    // Perform necessary actions for created subscription
                    // ...
                    break;
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    // Subscription activated, handle the event
                    Subscription::where('payment_subscription_id', $webhook_body->resource->id)->update([
                        'subscriber_email' => $webhook_body->resource->subscriber->email_address
                    ]);
                    // Perform necessary actions for activated subscription
                    // ...
                    break;

                case 'BILLING.SUBSCRIPTION.UPDATED':
                    // Subscription activated, handle the event

                    // Perform necessary actions for activated subscription
                    // ...
                    break;

                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    // Subscription cancelled, handle the event

                    // Perform necessary actions for cancelled subscription
                    // ...
                    break;

                    // Handle other subscription event types as needed
                case 'PAYMENT.SALE.COMPLETED':

                    // Subscription completed, handle the event
                    break;
                default:
                    // Unknown event type, log or handle accordingly
                    break;
            }
        }
    }

    public function autoRenewalDisable()
    {
        $subscribed = auth()->user()->subscribed;
        // Retrieve the subscription ID
        $subscriptionId = $subscribed->payment_subscription_id;
        $response = Http::withHeaders([
            'Authorization' => $this->resolveAccessToken()
        ])->withBody(json_encode([
            'reason' => "I don't renew my subscription"
        ]))->post(config('paypal.base_url') . "/v1/billing/subscriptions/$subscriptionId/cancel");
        // update in the database
        $subscribed->update(['auto_renewal' => 0]);
    }

    // public function autoRenewal()
    // {
    //     $subscribed = auth()->user()->subscribed;
    //     // Retrieve the subscription ID
    //     $subscriptionId = $subscribed->payment_subscription_id;

    //     $accessToken = 'Basic' . base64_encode(config('paypal.client_id') . ':' . config('paypal.client_secret'));

    //     // Set the subscription details
    //     $subscriptionData = [
    //         'plan_id' => $subscribed->payment_plan_id,
    //         'subscriber' => [
    //             'email_address' => 'sb-hefwd21187797@personal.example.com',
    //         ],
    //     ];

    //     // Create the subscription
    //     $createSubscriptionUrl = config('paypal.base_url') . '/v1/billing/subscriptions';
    //     Http::withHeaders([
    //         'Authorization' => $accessToken
    //     ])->post($createSubscriptionUrl, $subscriptionData);
    // }
}
