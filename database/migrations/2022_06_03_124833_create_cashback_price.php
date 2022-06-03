<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CapsuleTypes;
use Carbon\carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capsule_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('capsule_name')->nullable();
            $table->decimal('price_first_fifty',3,2)->nullable();
            $table->decimal('price_fifty_to_five_hundred',3,2)->nullable();
            $table->decimal('price_over_five_hundred_and_one',3,2)->nullable();
            $table->timestamps();
        });

        //having to use Carbon for created at time as bulk insert doesn't seem possible through eloquent create
        $now = Carbon::now('utc')->toDateTimeString();

        CapsuleTypes::insert([
            ['capsule_name' => 'Ristretto',
            'price_first_fifty' => 0.02,
            'price_fifty_to_five_hundred' => 0.03,
            'price_over_five_hundred_and_one' => 0.05,
            'created_at'=>$now]
        ]);

        CapsuleTypes::insert([
            ['capsule_name' => 'Espresso',
            'price_first_fifty' => 0.04,
            'price_fifty_to_five_hundred' => 0.06,
            'price_over_five_hundred_and_one' => 0.10,
            'created_at'=>$now]
        ]);

        CapsuleTypes::insert([
            ['capsule_name' => 'Lungo',
            'price_first_fifty' => 0.06,
            'price_fifty_to_five_hundred' => 0.09,
            'price_over_five_hundred_and_one' => 0.15,
            'created_at'=>$now]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('capsule_types');
    }
};
