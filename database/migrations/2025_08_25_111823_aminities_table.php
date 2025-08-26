<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id(); // amenity_id
            $table->string('aminityName');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('amenities');
    }
};
