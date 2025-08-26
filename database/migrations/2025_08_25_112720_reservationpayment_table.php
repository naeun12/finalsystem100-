<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservationpayments', function (Blueprint $table) {
            $table->id('paymentID');
            $table->unsignedBigInteger('reservationID');
            $table->string('paymentType'); // GCash, BDO, etc.
                    $table->decimal('amount', 10, 2); 

            $table->string('paymentImage'); // file path
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->timestamps();

            $table->foreign('reservationID')->references('reservationID')->on('reservation')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservationpayments');
    }
};
