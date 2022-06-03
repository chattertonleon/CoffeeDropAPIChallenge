<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locations;
use App\Models\OpeningTimes;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $locationsFile = fopen(base_path("database/data/location_data.csv"),"r");

        $id = 1;
        $flag = TRUE;
        fgetcsv($locationsFile, 2000, ",");
        while(($csvData = fgetcsv($locationsFile, 2000, ",")) !== FALSE){
            $postcode = strtolower($csvData['0']);
            $postcode = str_replace(' ','',$postcode);
            Locations::create([
                "postcode" => $postcode
            ]);
            for ($i=1; $i<8; $i++){
                OpeningTimes::create([
                    "location_id" => $id,
                    "day" => LocationSeeder::translateNumbersToDays($i),
                    "opening_time" => $csvData[$i],
                    "closing_time" => $csvData[$i+7]
                ]);
            }
            $id++;
        }
    }

    private function translateNumbersToDays($number){
        switch($number){
            case 1:
                return "Monday";
                break;
            case 2:
                return "Tuesday";
                break;
            case 3:
                return "Wednesday";
                break;
            case 4:
                return "Thursday";
                break;
            case 5:
                return "Friday";
                break;
            case 6:
                return "Saturday";
                break;
            case 7:
                return "Sunday";
                break;
        }
    }
}
