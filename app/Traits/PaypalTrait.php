<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait PaypalTrait
{
    public function makeRequest($method, $requestURL, $queryParams = [], $formParams = [], $headers = [], $isJSONRequest = false)
    {
        $client = new Client([
            'base_uri' => config('paypal.base_url')
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $response = $client->request($method, $requestURL, [
            $isJSONRequest ? 'json' : 'form_params' => $formParams,
            'headers' => $headers,
            'query' => $queryParams
        ]);

        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }

        return $response;
    }
}
