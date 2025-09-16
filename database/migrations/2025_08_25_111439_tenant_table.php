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
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('tenantID')->unique()->primary();
            $table->string('firstname', 55);
            $table->string('lastname', 55);
            $table->string('password', 255);
            $table->string('email', 255)->unique();
            $table->string('phoneNumber', 11)->unique();
            $table->enum('gender', ['Female', 'Male']);
            $table->string('city', 255);
            $table->string('province', 255);
            $table->string('region', 255);
            $table->string('currentAddress', 255);
            $table->string('postalCode', 4);
            $table->string('role')->default('tenant');
             $table->boolean('is_deactivated')->default(false)->comment('0 = active, 1 = deactivated');
            $table->string('profilePicUrl', 255)->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
