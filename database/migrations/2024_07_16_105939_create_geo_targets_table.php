<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = <<<SQL
            CREATE MATERIALIZED VIEW geo_targets_mv AS
            WITH cleaned AS (
                SELECT
                    criteria_id::BIGINT AS place_id,
                    TRIM(LOWER("name")) AS target_name,
                    TRIM(LOWER(canonical_name)) AS canonical_name,
                    parent_id::BIGINT AS parent_id,
                    TRIM(LOWER(country_code)) AS country_code,
                    TRIM(LOWER(target_type)) AS target_type,
                    string_to_array(REPLACE(TRIM(LOWER(canonical_name)), ' county', ''), ',') AS canonicals,
                    array_length(string_to_array(canonical_name, ','), 1) AS item_count
                FROM
                    google_geo_targets
                WHERE country_code IN ('US', 'CA') AND parent_id IS NOT NULL
            ),
            separated AS (
                SELECT
                    place_id,
                    parent_id,
                    CASE
                        WHEN item_count = 5 THEN concat(TRIM(canonicals[1]), ', ', TRIM(canonicals[2]))
                        ELSE target_name
                    END AS place,
                    CASE
                        WHEN item_count = 5 THEN TRIM(canonicals[3])
                        WHEN item_count = 4 THEN TRIM(canonicals[2])
                    END AS county,
                    CASE
                        WHEN item_count = 5 THEN TRIM(canonicals[4])
                        WHEN item_count = 4 THEN TRIM(canonicals[3])
                        WHEN item_count = 3 THEN TRIM(canonicals[2])
                        WHEN item_count = 2 THEN TRIM(canonicals[1])
                    END AS state,
                    CASE
                        WHEN item_count = 5 THEN TRIM(canonicals[5])
                        WHEN item_count = 4 THEN TRIM(canonicals[4])
                        WHEN item_count = 3 THEN TRIM(canonicals[3])
                        WHEN item_count = 2 THEN TRIM(canonicals[2])
                    END AS country,
                    canonical_name,
                    country_code,
                    target_type,
                    item_count AS canonical_count
                FROM
                    cleaned
            )
            SELECT * FROM separated;
        SQL;


        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS geo_targets_mv');
    }
};
