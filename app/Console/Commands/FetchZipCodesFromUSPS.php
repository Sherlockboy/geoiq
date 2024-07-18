<?php

namespace App\Console\Commands;

use App\Enums\Country;
use App\Jobs\USPSLookupJob;
use App\Services\QueueService;
use Illuminate\Console\Command;

class FetchZipCodesFromUSPS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:usps { --new : Pull zip codes from USPS for ga_city_id that does not exist in zip_codes table }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches zipcodes for US cities from USPS.com';

    /**
     * Execute the console command.
     */
    public function handle(QueueService $queueService): void
    {
        $pullOnlyNewTargets = !empty($this->option('new'));

        $targets = $queueService->getGeoTargetsToFetchZipCodes(Country::USA, ['state', 'postal code'], $pullOnlyNewTargets);

        $targets->each(fn($target) => USPSLookupJob::dispatch($target->place_id, $target->place, $target->code));
    }
}
