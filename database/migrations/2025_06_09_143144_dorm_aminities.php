<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('amenity_dorm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dorm_id');
            $table->unsignedBigInteger('amenity_id');
           
            $table->timestamps();


            // Foreign key constraints
            $table->foreign('dorm_id')->references('dorm_id')->on('dorms')->onDelete('cascade');
            $table->foreign('amenity_id')->references('id')->on('amenities')->onDelete('cascade');


            $table->unique(['dorm_id', 'amenity_id']); // prevent duplicates
        });
    }


    public function down()
    {
        Schema::dropIfExists('amenity_dorm');
    }
};




?>
