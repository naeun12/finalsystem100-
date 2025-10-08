<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('bookingID');
            $table->unsignedBigInteger('fkroomID');
            $table->string('fktenantID');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contactNumber');
            $table->string('contactEmail');
            $table->string('age');
            $table->string('gender');
            $table->date('moveInDate');
            $table->date('moveOutDate');
            $table->string('status')->default('pending')->nullable();
            $table->string('paymentType')->nullable();
            $table->string('paymentImage')->nullable();
            $table->boolean('isDeleted')->default(false);
            $table->string('pictureID')->nullable();
            $table->timestamps();

            // 🔹 Add soft deletes
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('fkroomID')->references('roomID')->on('rooms')->onDelete('cascade');
            $table->foreign('fktenantID')->references('tenantID')->on('tenants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
