<?php

namespace App\Console\Commands;

use App\Jobs\CanadaPostLookupJob;
use App\Services\QueueService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandStatus;

class FetchZipCodesFromCanadaPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:canada-post { --new : Pull zip codes from USPS for ga_city_id that does not exist in zip_codes table }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(QueueService $queueService): int
    {
        $pullOnlyNewTargets = !empty($this->option('new'));

        $targets = $queueService->getCanadaTargetsToFetchZipCodes(['state', 'postal code'], $pullOnlyNewTargets);

        $targets->each(
            fn($target) => CanadaPostLookupJob::dispatch(
                $target->place_id,
                str_replace(',canada', '', $target->canonical_name)
            )
        );

        return CommandStatus::SUCCESS;
    }
}
