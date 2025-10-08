<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('amenitydorm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fkdormID');
            $table->unsignedBigInteger('fkaminityID');           
            $table->timestamps();
            $table->foreign('fkdormID')->references('dormID')->on('dorms')->onDelete('cascade');
            $table->foreign('fkaminityID')->references('id')->on('amenities')->onDelete('cascade');
            $table->unique(['fkdormID', 'fkaminityID']);
        });
    }


    public function down()
    {
        Schema::dropIfExists('amenitydorm');
    }
};




?>
