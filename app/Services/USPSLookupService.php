<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class USPSLookupService
{
    public function __construct(private PendingRequest $httpClient)
    {
        $this->httpClient = Http::withHeaders([
            'accept' => 'application/json, text/javascript, */*; q=0.01',
            'accept-language' => 'en-US,en;q=0.9,uz;q=0.8',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'cookie' => config('services.usps.cookie'),
            'origin' => 'https://tools.usps.com',
            'priority' => 'u=1, i',
            'referer' => 'https://tools.usps.com/zip-code-lookup.htm?bycitystate',
            'sec-ch-ua' => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'x-requested-with' => 'XMLHttpRequest',
        ]);
    }

    public function getZipCode(string $city, string $stateCode): ?array
    {
        $response = $this->httpClient
            ->bodyFormat('form_params')
            ->post('https://tools.usps.com/tools/app/ziplookup/zipByCityState', [
                'city' => $city,
                'state' => $stateCode
            ])
            ->json();

        if (!$response['zipList']) {
            return null;
        }

        return collect($response['zipList'])
            ->map(fn(array $zip) => $zip['zip5'])
            ->toArray();
    }
}
