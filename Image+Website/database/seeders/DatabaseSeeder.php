<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locations;
use App\Models\OpeningTimes;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
     //seeding for database
    public function run()
    {
        //open csv
        $locationsFile = fopen(base_path("database/data/location_data.csv"),"r");

        $id = 1;
        //ignore first line of csv
        fgetcsv($locationsFile, 2000, ",");
        //while not empty
        while(($csvData = fgetcsv($locationsFile, 2000, ",")) !== FALSE){
            //standardise postcode
            $postcode = strtolower($csvData['0']);
            $postcode = str_replace(' ','',$postcode);
            //create location
            Locations::create([
                "postcode" => $postcode
            ]);
            //create opening time for location on each day of the week, no entry for a day means location closed on that day
            for ($i=1; $i<8; $i++){
                //set foreign key
                OpeningTimes::create([
                    "location_id" => $id,
                    "day" => DatabaseSeeder::translateNumbersToDays($i),
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
