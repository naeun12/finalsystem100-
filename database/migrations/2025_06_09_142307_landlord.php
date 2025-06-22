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
            $table->string('landlord_id')->unique()->primary();
            $table->string('firstname', 55);
            $table->string('lastname', 55);
            $table->string('password_hash', 255);
            $table->string('email', 255)->unique();
            $table->string('phonenumber', 11)->unique();
            $table->enum('gender', ['Female', 'Male']);
            $table->string('role')->default('landlord');
            $table->string('profile_pic_url', 255)->nullable();
            $table->string('goverment_id', 255)->nullable();
            $table->string('business_permit', 255)->nullable();
            $table->rememberToken();
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
