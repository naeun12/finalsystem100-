<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->id('reservationID');
            $table->unsignedBigInteger('fkdormitoryID');
            $table->unsignedBigInteger('fkroomID');
            $table->string('fktenantID');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contactNumber');
            $table->string('contactEmail');
            $table->string('age');
            $table->string('gender');
            $table->string('status')->default('pending')->change(); // Revert to original default
            $table->string('status')->default('pending');
            $table->boolean('isDeleted')->default(false);
            $table->string('pictureID')->nullable();
            $table->date('moveInDate')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Foreign key constraints
            $table->foreign('fkdormitoryID')->references('dormID')->on('dorms')->onDelete('cascade');
            $table->foreign('fkroomID')->references('roomID')->on('rooms')->onDelete('cascade');
            $table->foreign('fktenantID')->references('tenantID')->on('tenants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation');
    }
};
