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
            Schema::create('landlords', function (Blueprint $table) {
            $table->string('landlordID')->unique()->primary();
            $table->string('firstname', 55);
            $table->string('lastname', 55);
            $table->string('password', 255);
            $table->string('email', 255)->unique();
            $table->string('phoneNumber', 11)->unique();
            $table->enum('gender', ['Female', 'Male']);
            $table->string('role')->default('landlord');
            $table->string('profilePicUrl', 255)->nullable();
            $table->string('govermentID', 255)->nullable();
            $table->string('businessPermit', 255)->nullable();
            $table->boolean('isVerified')->default(false);
            $table->boolean('is_deactivated')->default(false)->comment('0 = active, 1 = deactivated');
            $table->timestamps();
        });
    }


   


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
                Schema::dropIfExists('landlords');


    }
};
