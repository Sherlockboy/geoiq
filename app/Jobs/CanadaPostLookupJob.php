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
        private readonly int $recursionCount = 0,
    )
    {
        if ($this->recursionCount > 2) {
            throw new Exception("CanadaPostLookupJob reached 2 recursion count");
        }
    }

    /**
     * Execute the job.
     */
    public function handle(CanadaPostService $service): void
    {
        $locations = $service->find($this->address, 'Items');

        foreach ($locations as $location) {
            if ($location['Type'] == CanadaPostLocationTypes::ADDRESS->value) {
                CanadaPostRetrieveJob::dispatch($this->gaCityId, $location['Id']);
            } else {
                self::dispatch($this->gaCityId, $location['Id'], $this->recursionCount+1);
            }
        }
    }
}
