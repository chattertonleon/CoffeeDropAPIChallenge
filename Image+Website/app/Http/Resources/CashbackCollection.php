<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CashbackCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

     //unable to work out how to make collection work in time given
    public function toArray($request)
    {
        return [
            'cashback_collection' => new CashbackResource($this)
        ];
    }
}
