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
         Schema::create('landlordVerifiedPayment', function (Blueprint $table) {
            $table->id('paymentID');
            $table->string('fklandlordID');
            $table->string('email');
            $table->decimal('amount', 10, 2);
            $table->string('paymongo_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->string('paymentMethod')->default('gcash');
            $table->string('referenceNumber')->nullable();
            $table->json('responsePayload')->nullable();
            $table->timestamps();

            // If you have landlords table
            $table->foreign('fklandlordID')->references('landlordID')->on('landlords')->onDelete('cascade');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlordVerifiedPayment');
    }
};
