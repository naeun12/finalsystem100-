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
       Schema::create('approvepayments', function (Blueprint $table) {
    $table->id('paymentID');
    $table->unsignedBigInteger('fkapprovedID'); // foreign key to approved_tenants
    $table->string('paymentType');
        $table->string('status')->default('pending');
        $table->decimal('amount', 10, 2); 
    $table->string('paymentImage')->nullable();
    $table->timestamps();

    $table->foreign('fkapprovedID')->references('approvedID')->on('approved_tenants')->onDelete('cascade');
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
