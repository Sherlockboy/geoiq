<?php

namespace App\Console\Commands;

use App\Jobs\SearchZipCodeJob;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandStatus;

class SearchZipCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:zip-codes {zip_codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $zipCodes = $this->argument('zip_codes');

        if (empty($zipCodes)) {
            return CommandStatus::INVALID;
        }

        $zipCodeArray = explode(',', $zipCodes);

        foreach ($zipCodeArray as $zipCode) {
            $zipCode = trim($zipCode);

            SearchZipCodeJob::dispatch($zipCode);
        }

        return CommandStatus::SUCCESS;
    }
}
