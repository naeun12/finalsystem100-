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
            $table->id('otp_id');    
            $table->string('email')->nullable(false);
            $table->string('otpCode')->nullable(false);
            $table->string('role')->default('user');
            $table->timestamp('otpExpires_at')->nullable();
                   
            $table->timestamps();
            $table->softDeletes();
            $table->index('otpExpires_at');

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
