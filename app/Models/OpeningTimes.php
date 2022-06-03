<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningTimes extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id','day','opening_time','closing_time'
    ];
}
