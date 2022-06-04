<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locations;
use App\Models\OpeningTimes;
use App\Http\Resources\LocationResource;
use Carbon\Carbon;
use DB;

class LocationController extends Controller
{
    //creates a new location
    public function createNewLocation(Request $request){

        $jsonRaw = $request->getContent();
        $jsonDecoded = json_decode($jsonRaw,true);
        $postcodeFromJson = $jsonDecoded['postcode'];

        //updates postcode to standardised lowercase string and replaces spaces
        $postcode = strtolower($postcodeFromJson);
        $postcode = str_replace(' ','',$postcode);

        //splits opening and closing times into two seperate iterable arrays
        $openingTimes = (array)$jsonDecoded['opening_times'];
        $closingTimes = (array)$jsonDecoded['closing_times'];

        //creates locations entry with postcode
        Locations::create([
            'postcode' => $postcode
        ]);

        //gets id of new entry
        $locationId = Locations::where('postcode',$postcode)->value('id');

        //constant of days in week
        define('DAYSINWEEK', array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'));

        //get the days open and from this work out the days a location is closed from all those that are not in days
        //open but are in DAYSINWEEK
        $daysOpen = array_keys($openingTimes);
        $daysClosed = array_diff(array_map('strtolower',DAYSINWEEK),$daysOpen);

        //input opening and closing times into OpeningTimes database for a location
        foreach(DAYSINWEEK as $day){

            $dayInput = "";
            $openingTime = "";
            $closingTime = "";

            //standardise day name in database to lowercase
            $dayLowerCase = strtolower($day);

            //if the location is open on a particular day, enter it into the database
            if (in_array(strtolower($day),$daysOpen)){
                $dayInput = $day;
                $openingTime = $openingTimes[$dayLowerCase];
                $closingTime = $closingTimes[$dayLowerCase];
            }

            //mass insert so time has to be manually calculated due to using insert over eloquent Create
            $now = Carbon::now('utc')->toDateTimeString();

            OpeningTimes::insert([
                'location_id'=> $locationId,
                'day' => $day,
                'opening_time' => $openingTime,
                'closing_time' => $closingTime,
                'created_at'=> $now,
                'updated_at'=> $now
            ]);
        }

        //return updated database information incase front end wants to automatically update it
        $returnJson = [];

        $postcodesAndIds = Locations::select('id','postcode')->get();

        foreach ($postcodesAndIds as $postcodeAndId){
            array_push(
                $returnJson,
                [
                    'postcode' => $postcodeAndId['postcode'],
                    OpeningTimes::select('day','opening_time','closing_time')->where('location_id',$postcodeAndId['id'])->get()
                ]
            );
        }

        return response()->json($returnJson);
    }

    //find a location that is closest to a given postcode
    public function getByPostcode(Request $request){

        //decode json and extract postcode
        $jsonRaw = $request->getContent();
        $jsonDecoded = json_decode($jsonRaw,true);
        $postcode = $jsonDecoded['postcode'];

        //run algorithm to get closest location in db
        $closestPoint = LocationController::getClosestDropPoint($postcode);

        //standardise postcode to lowercase with no spaces
        $closestPostCode = strtolower($closestPoint['result']['postcode']);
        $closestPostCode = str_replace(' ','',$closestPostCode);

        //get the information for a particular location
        $locationDetails = DB::table('locations')
                               ->join('opening_times','locations.id','=','opening_times.location_id')
                               ->select('opening_times.day','opening_times.opening_time','opening_times.closing_time')
                               ->where('locations.postcode',$closestPostCode)
                               ->get();
        //
        return response()->json([
            'opening_times'=>$locationDetails,
            'address' => new LocationResource($closestPoint['result'])
        ]);
    }

    //runs database and postcodes.io api queries to find closest location of a given postcode to an exist drop off location
    private function getClosestDropPoint($queryPostcode){

        //get all postcodes for a store
        $storePostcodes = Locations::select('postcode')->get(['postcode']);

        //query the api for the query postcode and retrieve its longitude and latitude
        $queryPostCodeInfo = LocationController::getPostcodeAPIInfo($queryPostcode);
        $queryLongitude = $queryPostCodeInfo['result']['longitude'];
        $queryLatitude = $queryPostCodeInfo['result']['latitude'];

        $closestStore = [];
        //Astronomically large number as placeholder, could be inplemented better by first entry in $storePostcode distance
        $closestStoreDistance = 10000000000000000;

        //for every drop off location calculate the haversine distance to the query postcode returning only the closest
        foreach ($storePostcodes as $storePostcode){

            $response = LocationController::getPostcodeAPIInfo($storePostcode['postcode']);
            $storeLongitude = $response['result']['longitude'];
            $storeLatitude = $response['result']['latitude'];
            $distance = LocationController::haversine($queryLatitude, $queryLongitude, $storeLatitude, $storeLongitude);

            if ($distance < $closestStoreDistance){
                $closestStore = $response;
                $closestStoreDistance = $distance;
            }
        }

        return $closestStore;
    }

    //Calcuate haversine distance between two locations
    //got implementation here: https://thisinterestsme.com/php-haversine-formula-function/
    //checked implementation against formulas provided here to ensure correct: https://en.wikipedia.org/wiki/Haversine_formula
    private function haversine($lat1,$long1,$lat2,$long2){
        $earth_radius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($long2 - $long1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;
        return $d;
    }

    //queury the postcodes.io api
    private function getPostcodeAPIInfo($postcode){
        $response = file_get_contents('http://api.postcodes.io/postcodes/'.$postcode);
        return json_decode($response,true);
    }
}
