<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locations;
use App\Models\OpeningTimes;
use Carbon\Carbon;
use DB;

class LocationController extends Controller
{

    public function createNewLocation(Request $request){

        $jsonRaw = $request->getContent();
        $jsonDecoded = json_decode($jsonRaw,true);
        $postcodeFromJson = $jsonDecoded['postcode'];
        $postcode = strtolower($postcodeFromJson);
        $postcode = str_replace(' ','',$postcode);
        $openingTimes = (array)$jsonDecoded['opening_times'];
        $closingTimes = (array)$jsonDecoded['closing_times'];

        Locations::create([
            'postcode' => $postcode
        ]);

        $locationId = Locations::where('postcode',$postcode)->value('id');

        define('DAYSINWEEK', array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'));

        $daysOpen = array_keys($openingTimes);
        $daysClosed = array_diff(array_map('strtolower',DAYSINWEEK),$daysOpen);

        foreach(DAYSINWEEK as $day){

            $dayInput = "";
            $openingTime = "";
            $closingTime = "";

            $dayLowerCase = strtolower($day);

            if (in_array(strtolower($day),$daysOpen)){
                $dayInput = $day;
                $openingTime = $openingTimes[$dayLowerCase];
                $closingTime = $closingTimes[$dayLowerCase];
            }

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
    }

    public function getByPostcode(Request $request){

        $jsonRaw = $request->getContent();
        $jsonDecoded = json_decode($jsonRaw,true);
        $postcode = $jsonDecoded['postcode'];

        $closestPoint = LocationController::getClosestDropPoint($postcode);

        $closestPostCode = strtolower($closestPoint['result']['postcode']);
        $closestPostCode = str_replace(' ','',$closestPostCode);

        $locationDetails = DB::table('locations')
                               ->join('opening_times','locations.id','=','opening_times.location_id')
                               ->select('opening_times.day','opening_times.opening_time','opening_times.closing_time')
                               ->where('locations.postcode',$closestPostCode)
                               ->get();

        return response()->json([
            'Opening_times'=>$locationDetails,
            'address' => [
                'postcode'=>$closestPoint['result']['postcode'],
                'admin_ward'=>$closestPoint['result']['admin_ward'],
                'county'=>$closestPoint['result']['admin_county'],
                'country'=>$closestPoint['result']['country'],
                'latitude'=>$closestPoint['result']['latitude'],
                'longitude'=>$closestPoint['result']['longitude']
            ]
        ]);
    }

    private function getClosestDropPoint($queryPostcode){
        $storePostcodes = Locations::select('postcode')->get(['postcode']);

        $queryPostCodeInfo = LocationController::getPostcodeAPIInfo($queryPostcode);
        $queryLongitude = $queryPostCodeInfo['result']['longitude'];
        $queryLatitude = $queryPostCodeInfo['result']['latitude'];

        $closestStore = [];
        //Astronomically large number as placeholder, could be inplemented better
        $closestStoreDistance = 10000000000000000;

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

    private function getPostcodeAPIInfo($postcode){
        $response = file_get_contents('http://api.postcodes.io/postcodes/'.$postcode);
        return json_decode($response,true);
    }
}
