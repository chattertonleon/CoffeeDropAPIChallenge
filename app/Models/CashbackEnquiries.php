<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashbackEnquiries extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_ristretto','number_espresso','number_lungo','total_price'
    ];
}
