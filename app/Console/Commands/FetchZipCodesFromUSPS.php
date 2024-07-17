<?php

namespace App\Console\Commands;

use App\Jobs\USPSLookupJob;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

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
    public function handle(): void
    {
        $pullOnlyNewTargets = !empty($this->option('new'));

        DB::table('geo_targets_mv as g')
            ->select(['g.place_id', 'g.place', 's.code'])
            ->join('states as s', 'g.state', '=', 's.name')
            ->when($pullOnlyNewTargets, function (Builder $query) {
                return $query->leftJoin('zip_codes as z', 'z.ga_city_id', '=', DB::raw('g.place_id::bigint'))
                    ->whereNull('z.id');
            })
            ->where('g.country_code', '=', 'us')
            ->whereNotIn('g.target_type', ['state', 'postal code'])
            ->get()
            ->each(
                fn($target) => USPSLookupJob::dispatch($target->place_id, $target->place, $target->code)
            );
    }
}
