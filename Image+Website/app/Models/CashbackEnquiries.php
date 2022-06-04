<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//model of all enquiries for cashback ammounts
class CashbackEnquiries extends Model
{
    use HasFactory;

    //created_at and updated_at made fillable, this is as mass insert used for multiple record creation over eloquent create command
    //aware this may be bad practice
    protected $fillable = [
        'number_ristretto','number_espresso','number_lungo','total_price','created_at','updated_at'
    ];
}
