<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubdistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // "subdistrict_id":"537",
    // "province_id":"5",
    // "province":"DI Yogyakarta",
    // "city_id":"39",
    // "city":"Bantul",
    // "type":"Kabupaten",
    // "subdistrict_name":"Bambang Lipuro"

    // "subdistrict_id": "537",
    // "province_id": "5",
    // "province": "DI Yogyakarta",
    // "city_id": "39",
    // "city": "Bantul",
    // "type": "Kabupaten",
    // "subdistrict_name": "Bambang Lipuro"
    public function up()
    {
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->string('subdistrict_id', 8);
            $table->string('subdistrict_name');
            $table->string('city_id', 5);
            $table->string('city');
            $table->string('type');
            $table->string('province_id', 4);
            $table->string('province');
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
        Schema::dropIfExists('subdistricts');
    }
}
