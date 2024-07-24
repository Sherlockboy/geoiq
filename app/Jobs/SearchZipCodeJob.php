<?php

namespace App\Jobs;

use App\Models\ZipCodeSearch;
use App\Services\ZipCodeOrgService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SearchZipCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $zipCode)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ZipCodeOrgService $service): void
    {
        $data = $service->getCities($this->zipCode);

        collect($data['rows'])->each(fn(array $row) => ZipCodeSearch::create($row));
    }
}
