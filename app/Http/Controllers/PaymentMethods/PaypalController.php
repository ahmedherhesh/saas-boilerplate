<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PayPalController extends Controller
{
    /**
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function index()
    {
        return view('checkout');
    }

    /**
     * @return string
     */
    private function getAccessToken(): string
    {
        $headers = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode(config('paypal.client_id') . ':' . config('paypal.client_secret'))
        ];

        $response = Http::withHeaders($headers)
            ->withBody('grant_type=client_credentials')
            ->post(config('paypal.base_url') . '/v1/oauth2/token');

        return json_decode($response->body())->access_token;
    }

    /**
     * @return string
     */
    public function create(Request $request): string
    {
        // $id = uuid_create();

        // $headers = [
        //     'Content-Type'      => 'application/json',
        //     'Authorization'     => 'Bearer ' . $this->getAccessToken(),
        //     'PayPal-Request-Id' => $id,
        // ];

        // $body = [
        //     "intent"         => "CAPTURE",
        //     "purchase_units" => [
        //         [
        //             "reference_id" => $id,
        //             "amount"       => [
        //                 "currency_code" => "GBP",
        //                 "value"         => number_format($amount, 2),
        //             ]
        //         ]
        //     ]
        // ];

        // $response = Http::withHeaders($headers)
        //                 ->withBody(json_encode($body))
        //                 ->post(config('paypal.base_url'). '/v2/checkout/orders');

        // Session::put('request_id', $id);
        // Session::put('order_id', json_decode($response->body())->id);

        // return json_decode($response->body())->id;


        $client = new Client();

        $response = $client->post(config('paypal.base_url') . '/v1/billing/subscriptions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('paypal.client_secret'),
            ],
            'json' => [
                'plan_id' => $request->paypal_plan_id,
                'subscriber' => [
                    'name' => 'Ahmed Harhesh',
                    'email_address' => 'ahmedherhesh3@gmail.com',
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return redirect($data['links'][0]['href']);
    }

    /**
     * @return mixed
     */
    public function complete()
    {
        // $url = config('paypal.base_url') . '/v2/checkout/orders/' . Session::get('order_id') . '/capture';

        // $headers = [
        //     'Content-Type'  => 'application/json',
        //     'Authorization' => 'Bearer ' . $this->getAccessToken(),
        // ];

        // $response = Http::withHeaders($headers)
        //     ->post($url, null);

        // return json_decode($response->body());
    }
}
