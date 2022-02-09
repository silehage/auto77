<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // "city_id": "39",
    // "province_id": "5",
    // "province": "DI Yogyakarta",
    // "type": "Kabupaten",
    // "city_name": "Bantul",
    // "postal_code": "55700"
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_id', 5);
            $table->string('city_name');
            $table->string('province_id', 4);
            $table->string('province');
            $table->string('type');
            $table->string('postal_code')->nullable();
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
        Schema::dropIfExists('cities');
    }
}
