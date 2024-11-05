<?php

namespace App\Jobs;

use App\Enums\ZipCodeSource;
use App\Models\GeoTarget;
use App\Models\ZipCode;
use App\Services\CanadaPostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CanadaPostRetrieveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int    $gaCityId,
        private readonly string $addressId
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(CanadaPostService $service): void
    {
        $gaCity = GeoTarget::where('place_id', '=', $this->gaCityId)->firstOrFail();

        $locations = $service->get($this->addressId, 'Items');

        foreach ($locations as $location) {
            if (
                strtolower($gaCity->place) == strtolower($location['City'])
                && strtolower($gaCity->state) == strtolower($location['ProvinceName'])
                && strtolower($gaCity->country) == strtolower($location['CountryName'])
            ) {
                ZipCode::updateOrCreate([
                    'ga_city_id' => $this->gaCityId,
                    'zip_code' => strtoupper($location['PostalCode']),
                    'data_source' => ZipCodeSource::CANADA_POST->value,
                ]);
            }
        }
    }
}
