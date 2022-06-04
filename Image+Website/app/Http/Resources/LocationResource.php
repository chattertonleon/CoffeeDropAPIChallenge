<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'postcode' => $this['postcode'],
            'admin_ward' => $this['admin_ward'],
            'region' => $this['region'],
            'country' => $this['country'],
            'latitude' => $this['latitude'],
            'longitude' => $this['longitude']
        ];
    }
}
