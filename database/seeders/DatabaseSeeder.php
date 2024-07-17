<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->insertStates();
    }

    private function insertStates(): void
    {
        $states = [
            ['name' => 'alabama', 'code' => 'AL'],
            ['name' => 'alaska', 'code' => 'AK'],
            ['name' => 'arizona', 'code' => 'AZ'],
            ['name' => 'arkansas', 'code' => 'AR'],
            ['name' => 'california', 'code' => 'CA'],
            ['name' => 'colorado', 'code' => 'CO'],
            ['name' => 'connecticut', 'code' => 'CT'],
            ['name' => 'delaware', 'code' => 'DE'],
            ['name' => 'district of columbia', 'code' => 'DC'],
            ['name' => 'florida', 'code' => 'FL'],
            ['name' => 'georgia', 'code' => 'GA'],
            ['name' => 'hawaii', 'code' => 'HI'],
            ['name' => 'idaho', 'code' => 'ID'],
            ['name' => 'illinois', 'code' => 'IL'],
            ['name' => 'indiana', 'code' => 'IN'],
            ['name' => 'iowa', 'code' => 'IA'],
            ['name' => 'kansas', 'code' => 'KS'],
            ['name' => 'kentucky', 'code' => 'KY'],
            ['name' => 'louisiana', 'code' => 'LA'],
            ['name' => 'maine', 'code' => 'ME'],
            ['name' => 'maryland', 'code' => 'MD'],
            ['name' => 'massachusetts', 'code' => 'MA'],
            ['name' => 'michigan', 'code' => 'MI'],
            ['name' => 'minnesota', 'code' => 'MN'],
            ['name' => 'mississippi', 'code' => 'MS'],
            ['name' => 'missouri', 'code' => 'MO'],
            ['name' => 'montana', 'code' => 'MT'],
            ['name' => 'nebraska', 'code' => 'NE'],
            ['name' => 'nevada', 'code' => 'NV'],
            ['name' => 'new hampshire', 'code' => 'NH'],
            ['name' => 'new jersey', 'code' => 'NJ'],
            ['name' => 'new mexico', 'code' => 'NM'],
            ['name' => 'new york', 'code' => 'NY'],
            ['name' => 'north carolina', 'code' => 'NC'],
            ['name' => 'north dakota', 'code' => 'ND'],
            ['name' => 'ohio', 'code' => 'OH'],
            ['name' => 'oklahoma', 'code' => 'OK'],
            ['name' => 'oregon', 'code' => 'OR'],
            ['name' => 'pennsylvania', 'code' => 'PA'],
            ['name' => 'rhode island', 'code' => 'RI'],
            ['name' => 'south carolina', 'code' => 'SC'],
            ['name' => 'south dakota', 'code' => 'SD'],
            ['name' => 'tennessee', 'code' => 'TN'],
            ['name' => 'texas', 'code' => 'TX'],
            ['name' => 'utah', 'code' => 'UT'],
            ['name' => 'vermont', 'code' => 'VT'],
            ['name' => 'virginia', 'code' => 'VA'],
            ['name' => 'washington', 'code' => 'WA'],
            ['name' => 'west virginia', 'code' => 'WV'],
            ['name' => 'wisconsin', 'code' => 'WI'],
            ['name' => 'wyoming', 'code' => 'WY']
        ];

        DB::table('states')->insert($states);
    }
}
