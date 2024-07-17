<?php

namespace App\Jobs;

use App\Models\ZipCode;
use App\Services\USPSLookupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class USPSLookupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int    $gaCityId,
        private readonly string $cityName,
        private readonly string $stateCode
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(USPSLookupService $lookupService): void
    {
        $zipCodes = $lookupService->getZipCode($this->cityName, $this->stateCode);

        foreach ($zipCodes as $zipCode) {
            $data = [
                'ga_city_id' => $this->gaCityId,
                'zip_code' => $zipCode
            ];

            ZipCode::updateOrCreate($data, $data);
        }
    }
}
