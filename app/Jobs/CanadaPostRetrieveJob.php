<?php

namespace App\Jobs;

use App\Enums\ZipCodeSource;
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
        $locations = $service->get($this->addressId, 'Items');

        foreach ($locations as $location) {
            $zipCodes = explode(' ', $location['PostalCode']);

            foreach ($zipCodes as $zipCode) {
                $data = [
                    'ga_city_id' => $this->gaCityId,
                    'zip_code' => $zipCode,
                    'data_source' => ZipCodeSource::CANADA_POST->value,
                ];

                ZipCode::updateOrCreate($data);
            }
        }
    }
}
