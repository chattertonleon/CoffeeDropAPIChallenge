<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//model of a drop off location, one drop off location can have many opening times
class Locations extends Model
{
    use HasFactory;

    protected $fillable = [
        'postcode'
    ];
}
