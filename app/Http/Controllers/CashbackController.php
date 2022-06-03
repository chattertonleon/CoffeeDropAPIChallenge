<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CapsuleTypes;
use App\Models\CashbackEnquiries;
use Carbon\carbon;

class CashbackController extends Controller
{
    public function getCashback(Request $request){

        $jsonRaw = $request->getContent();
        $jsonDecoded = json_decode($jsonRaw,true);

        foreach ($jsonDecoded as $json){
            $ristrettoNumber = $json['Ristretto'];
            $espressoNumber = $json['Espresso'];
            $lungoNumber = $json['Lungo'];

            $totalCapsules = $ristrettoNumber + $espressoNumber + $lungoNumber;

            $totalSum = 0;

            $ristrettoPricePerCapsule = 0;
            $espressoPricePerCapsule = 0;
            $lungoPricePerCapsule = 0;

            if ($totalCapsules < 51){

                $ristrettoPricePerCapsule = CapsuleTypes::where('capsule_name','Ristresso')->value('price_first_fifty');
                $espressoPricePerCapsule = CapsuleTypes::where('capsule_name','Espresso')->value('price_first_fifty');
                $lungoPricePerCapsule = CapsuleTypes::where('capsule_name','Lungo')->value('price_first_fifty');

            } else if ($totalCapsules < 500){

                $ristrettoPricePerCapsule = CapsuleTypes::where('capsule_name','Ristresso')->value('price_fifty_to_five_hundred');
                $espressoPricePerCapsule = CapsuleTypes::where('capsule_name','Espresso')->value('price_fifty_to_five_hundred');
                $lungoPricePerCapsule = CapsuleTypes::where('capsule_name','Lungo')->value('price_fifty_to_five_hundred');

            } else {

                $ristrettoPricePerCapsule = CapsuleTypes::where('capsule_name','Ristresso')->value('price_over_five_hundred_and_one');
                $espressoPricePerCapsule = CapsuleTypes::where('capsule_name','Espresso')->value('price_over_five_hundred_and_one');
                $lungoPricePerCapsule = CapsuleTypes::where('capsule_name','Lungo')->value('price_over_five_hundred_and_one');
            }

            $totalSum = ($ristrettoPricePerCapsule * $ristrettoNumber) + ($espressoPricePerCapsule * $espressoNumber) + ($lungoPricePerCapsule * $lungoNumber);

            $now = Carbon::now('utc')->toDateTimeString();

            CashbackEnquiries::insert([
                'number_ristretto'=>$ristrettoNumber,
                'number_espresso'=>$espressoNumber,
                'number_lungo'=>$lungoNumber,
                'total_price'=>$totalSum,
                'created_at'=>$now,
                'updated_at'=>$now
            ]);
        }

        return response()->json([
            'total_cashback'=>$totalSum
        ]);
    }

    public function getMostRecentCashbacks(){
        $mostRecentCashbacks = CashbackEnquiries::latest()->take(5)->get();

        $returnJson = [];

        foreach($mostRecentCashbacks as $recentCashback){
            array_push($returnJson,[
                                   'number_ristretto' => $recentCashback['number_ristretto'],
                                   'number_espresso' => $recentCashback['number_espresso'],
                                   'number_lungo' => $recentCashback['number_lungo'],
                                   'total_price' => $recentCashback['total_price']
                                   ]);
        }
        return response()->json($returnJson);
    }

}
