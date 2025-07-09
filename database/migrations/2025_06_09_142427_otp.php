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
            Schema::create('otp', function (Blueprint $table) {
            $table->id('otpID');    
            $table->string('email')->nullable(false);
            $table->string('otpCode')->nullable(false);
            $table->string('role')->default('user');
            $table->timestamp('otpExpiresAt')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('otpExpiresAt');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp');
    }
};
