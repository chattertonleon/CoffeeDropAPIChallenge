<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CashbackEnquiries;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('cashback_enquiries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number_ristretto');
            $table->integer('number_espresso');
            $table->integer('number_lungo');
            $table->decimal('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('cashback_enquiries');
    }
};
