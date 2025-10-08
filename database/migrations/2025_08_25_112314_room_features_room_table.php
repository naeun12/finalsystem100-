<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_features_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fkroomID');
            $table->unsignedBigInteger('fkfeatureID');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('fkroomID')->references('roomID')->on('rooms')->onDelete('cascade');
            $table->foreign('fkfeatureID')->references('id')->on('roomfeatures')->onDelete('cascade');

            $table->unique(['fkroomID', 'fkfeatureID']); // prevent duplicates
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_features_rooms');
    }
};
