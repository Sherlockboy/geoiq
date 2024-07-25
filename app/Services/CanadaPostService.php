<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CanadaPostService
{
    public function __construct(private PendingRequest $httpClient)
    {
        $this->httpClient = Http::baseUrl('https://ws1.postescanada-canadapost.ca/Capture/Interactive')
            ->withHeaders([
                'accept' => '*/*',
                'accept-language' => 'en-US,en;q=0.9,uz;q=0.8',
                'origin' => 'https://www.canadapost-postescanada.ca',
                'priority' => 'u=1, i',
                'referer' => 'https://www.canadapost-postescanada.ca/cpc/en/tools/find-a-postal-code.page',
                'sec-ch-ua' => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"macOS"',
                'sec-fetch-dest' => 'empty',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'cross-site',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            ]);
    }

    public function find(string $address, string $key = null)
    {
        return $this->httpClient
            ->get('/Find/v1.00/json3ex.ws', [
                'Key' => 'EA98-JC42-TF94-JK98',
                'Text' => $address,
                'Countries' => 'CA',
                'Language' => 'en',
                'SOURCE' => 'PCA-SCRIPT',
                'SESSION' => config('services.canada-post.session_id'),
            ])
            ->json($key);
    }

    public function get(string $addressId, string $key = null)
    {
        return $this->httpClient
            ->get('/Retrieve/v1.00/json3ex.ws', [
                'Key' => 'EA98-JC42-TF94-JK98',
                'Id' => $addressId,
                'SOURCE' => 'PCA-SCRIPT',
                'SESSION' => config('services.canada-post.session_id'),
            ])
            ->json($key);
    }
}
