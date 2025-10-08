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
        Schema::create('dormimages', function (Blueprint $table) {
            $table->id('imagesID'); // primary key
            $table->unsignedBigInteger('fkdormID');
            $table->string('mainImage')->nullable();
            $table->string('secondaryImage')->nullable();
            $table->string('thirdImage')->nullable();
            $table->timestamps();
            $table->foreign('fkdormID')->references('dormID')->on('dorms')->onDelete('cascade');


        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dormImages');
    }
};
