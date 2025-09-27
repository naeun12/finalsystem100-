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
         Schema::create('tenantscreenings', function (Blueprint $table) {
            $table->id('screeningID');
            $table->unsignedBigInteger('fkBookingID'); // Link to bookings
            $table->integer('age');
            $table->string('gender');
            $table->string('contactNumber');
            $table->string('contactEmail');
            $table->string('pictureID')->nullable();
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();

          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
