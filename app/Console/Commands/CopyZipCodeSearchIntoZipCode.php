<?php

namespace App\Console\Commands;

use App\Models\ZipCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandStatus;

class CopyZipCodeSearchIntoZipCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:zip-code-search-into-zip-code';

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
        $query = <<<SQL
            WITH geo_targets AS (
                SELECT
                    g.*,
                    s.code AS state_code
                FROM
                    geo_targets_mv g,
                    states s
                WHERE
                    LOWER(g.state) = LOWER(s.name)
            ),
            merged AS (
                SELECT
                    zs.zip_code::TEXT,
                    zs.source,
                    g.place_id::INT AS ga_city_id
                FROM
                    zip_code_searches zs
                    JOIN geo_targets g ON
                        (LOWER(zs.city) = LOWER(g.place) OR LOWER(zs.county) = LOWER(g.county))
                        AND LOWER(zs. "state") = LOWER(g.state_code)
                        AND LOWER(zs.country_code) = LOWER(g.country_code)
            ),
            zip_count AS (
                SELECT
                    zip_code,
                    count(*) AS total
                FROM
                    merged
                GROUP BY
                    1
            )
            SELECT
                CASE WHEN z.total > 1 THEN
                    g.parent_id
                ELSE
                    g.place_id
                END AS ga_city_id,
                m.zip_code,
                m.source
            FROM
                merged m
            JOIN geo_targets_mv g ON m.ga_city_id = g.place_id
            JOIN zip_count z ON m.zip_code = z.zip_code
            GROUP BY
                1,
                2,
                3;
        SQL;

        $zipCodes = DB::select($query);

        foreach ($zipCodes as $zipCode) {
            $data = [
                'ga_city_id' => $zipCode->ga_city_id,
                'zip_code' => $zipCode->zip_code,
                'data_source' => $zipCode->source,
            ];

            ZipCode::updateOrCreate($data);
        }

        return CommandStatus::SUCCESS;
    }
}
