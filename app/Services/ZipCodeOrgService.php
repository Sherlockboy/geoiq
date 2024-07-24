<?php

namespace App\Services;

use App\Enums\Country;
use App\Enums\ZipCodeSource;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ZipCodeOrgService
{
    public function __construct(private PendingRequest $httpClient)
    {
        $this->httpClient = Http::withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language' => 'en-US,en;q=0.9,uz;q=0.8',
            'Cache-Control' => 'max-age=0',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Cookie' => config('services.zip-code-org.cookie'),
            'Origin' => 'https://zipcode.org',
            'Referer' => 'https://zipcode.org/zip_code_search',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ]);
    }

    public function searchZipCode(string $zipCode): string
    {
        return $this->httpClient
            ->asForm()
            ->post('https://zipcode.org/zip_code_search', [
                'txtZipCode' => $zipCode,
                'txtTownCity' => '',
                'sltState' => '-1',
                'txtCounty' => '',
                'txtAreaCode' => '',
                'Submit' => 'Search',
            ])
            ->body();
    }

    public function getCities(string $zipCode): array
    {
        $html = $this->searchZipCode($zipCode);

        $crawler = new Crawler($html);

        $table = $crawler->filter('div.HTML_Block .HTML_Block_Detail table')->last();

        $data['headers'] = $table->filter('tr')->first()
            ->filter('td')
            ->each(fn(Crawler $cell) => Str::snake(strtolower($cell->text())));

        $data['rows'] = $table->filter('tr.TRDLRowOdd')
            ->each(function (Crawler $row) {
                return [
                    'zip_code' => $row->filter('td')->eq(0)->filter('a')->first()->text(),
                    'area_code' => $row->filter('td')->eq(1)->filter('a')->each(fn(Crawler $aTag) => $aTag->text()),
                    'city' => $row->filter('td')->eq(2)->filter('a')->first()->text(),
                    'county' => $row->filter('td')->eq(3)->filter('a')->first()->text(),
                    'state' => $row->filter('td')->eq(4)->filter('a')->first()->text(),
                    'country_code' => Country::USA->value,
                    'source' => ZipCodeSource::ZIP_CODE_ORG->value
                ];
            });

        return $data;
    }
}
