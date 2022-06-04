<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CashbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'number_ristretto' => $this->number_ristretto,
            'number_espresso' => $this->number_espresso,
            'number_lungo' => $this->number_lungo,
            'total_price' => $this->total_price
        ];
    }
}
