<?php

namespace App\Console\Commands;

use App\Enums\ZipCodeSource;
use App\Models\ZipCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyPostalTargetsIntoZipCodesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:postal-targets-into-zip-codes-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $geoTargets = DB::table('geo_targets_mv')
            ->select([
                DB::raw('place_id::BIGINT as ga_city_id'),
                DB::raw('place::TEXT as zip_code')
            ])
            ->where('target_type', '=', 'postal code')
            ->get();

        foreach ($geoTargets as $geoTarget) {
            $data = [
                'ga_city_id' => $geoTarget->ga_city_id,
                'zip_code' => $geoTarget->zip_code,
                'data_source' => ZipCodeSource::GOOGLE->value
            ];

            ZipCode::updateOrCreate($data);
        }
    }
}
