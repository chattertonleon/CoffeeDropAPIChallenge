<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//model of capsule types and there cost per capsule
class CapsuleTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'capsule_name','price_first_fifty','price_fifty_to_five_hundred','price_over_five_hundred_and_one','created_at','updated_at'
    ];
}
