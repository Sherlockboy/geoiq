<?php

namespace App\Jobs;

use App\Enums\CanadaPostLocationTypes;
use App\Services\CanadaPostService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CanadaPostLookupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @throws Exception
     */
    public function __construct(
        private readonly int    $gaCityId,
        private readonly string $address,
        private readonly string $container = '',
        private readonly int $recursionCount = 1,
    )
    {
        if ($this->recursionCount > 3) {
            throw new Exception("CanadaPostLookupJob reached 4 recursion count");
        }
    }

    /**
     * Execute the job.
     */
    public function handle(CanadaPostService $service): void
    {
        $locations = $service->find(address: $this->address, container: $this->container, key: 'Items');

        foreach ($locations as $location) {
            if ($location['Type'] == CanadaPostLocationTypes::ADDRESS->value) {
                CanadaPostRetrieveJob::dispatch($this->gaCityId, $location['Id']);
            }

            if ($location['Type'] == CanadaPostLocationTypes::STREET->value) {
                self::dispatch($this->gaCityId, $location['Text'], $location['Id'], $this->recursionCount+1);
            }
        }
    }
}
