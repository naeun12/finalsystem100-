<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dorm_images', function (Blueprint $table) {
            $table->id('images_id'); // primary key
            $table->unsignedBigInteger('dormitory_id');
            $table->string('main_image')->nullable();
            $table->string('secondary_image')->nullable();
            $table->string('third_image')->nullable();
            $table->timestamps();


            $table->foreign('dormitory_id')->references('dorm_id')->on('dorms')->onDelete('cascade');


        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dorm_images');
    }
};
