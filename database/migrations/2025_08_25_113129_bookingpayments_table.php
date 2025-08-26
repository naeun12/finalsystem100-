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
        Schema::create('bookingpayments', function (Blueprint $table) {
    $table->id('paymentID');
    $table->unsignedBigInteger('fkbookingID');
    $table->string('paymentType');
    $table->decimal('amount', 10, 2); 
    $table->string('paymentImage')->nullable();
    $table->timestamps();

    $table->foreign('fkbookingID')->references('bookingID')->on('bookings')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookingpayments');
    }
};
