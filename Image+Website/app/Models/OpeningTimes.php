<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//model for opening times of a drop off location, one location can have many opening times
class OpeningTimes extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id','day','opening_time','closing_time'
    ];
}
