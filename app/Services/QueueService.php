<?php

namespace App\Services;

use App\Enums\Country;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QueueService
{
    public function getGeoTargetsToFetchZipCodes(Country $country, array $excludedTargetTypes, bool $onlyUnprocessed = false): Collection
    {
        return DB::table('geo_targets_mv as g')
            ->select(['g.place_id', 'g.place', 's.code'])
            ->join('states as s', 'g.state', '=', 's.name')
            ->when($onlyUnprocessed, function (Builder $query) {
                return $query->leftJoin('zip_codes as z', 'z.ga_city_id', '=', DB::raw('g.place_id::bigint'))
                    ->whereNull('z.id');
            })
            ->where('g.country_code', '=', $country->value)
            ->whereNotIn('g.target_type', $excludedTargetTypes)
            ->get();
    }
}
