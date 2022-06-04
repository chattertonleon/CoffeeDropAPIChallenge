<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CapsuleTypes;
use App\Models\CashbackEnquiries;
use App\Http\Resources\CashbackResource;
use App\Http\Resources\CashbackCollection;
use Carbon\carbon;

class CashbackController extends Controller
{

    //method calculates cashback
    public function getCashback(Request $request){

        $jsonRaw = $request->getContent();
        $jsonDecoded = json_decode($jsonRaw,true);

            $ristrettoNumber = $jsonDecoded['Ristretto'];
            $espressoNumber = $jsonDecoded['Espresso'];
            $lungoNumber = $jsonDecoded['Lungo'];

            //calculate total number of capsules
            $totalCapsules = $ristrettoNumber + $espressoNumber + $lungoNumber;

            $totalSum = 0;

            $ristrettoPricePerCapsule = 0;
            $espressoPricePerCapsule = 0;
            $lungoPricePerCapsule = 0;

            //work out pricing dependent on number of capsules
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

            //Used as multiple insert statement below does not automatically calculate created at and updated at times
            $now = Carbon::now('utc')->toDateTimeString();

            CashbackEnquiries::insert([
                'number_ristretto'=>$ristrettoNumber,
                'number_espresso'=>$espressoNumber,
                'number_lungo'=>$lungoNumber,
                'total_price'=>$totalSum,
                'created_at'=>$now,
                'updated_at'=>$now
            ]);

        //used the following as was not able to work out Resource collections
        return response()->json([
            'total_cashback'=>round($totalSum,2),
            'updated_cashback_enquiries' => CashbackEnquiries::get()
        ]);
    }

    //retrives 5 most recent cashbacks
    public function getMostRecentCashbacks(){
        $mostRecentCashbacks = CashbackEnquiries::latest()->take(5)->get();

        $returnJson = [];

        foreach($mostRecentCashbacks as $recentCashback){
            array_push($returnJson, new CashbackResource($recentCashback));
        }

        return response()->json($returnJson);
    }

}
