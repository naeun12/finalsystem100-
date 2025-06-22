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
            $table->string('tenant_id')->unique()->primary();
            $table->string('firstname', 55);
            $table->string('lastname', 55);
            $table->string('password_hash', 255);
            $table->string('email', 255)->unique();
            $table->string('phonenumber', 11)->unique();
            $table->enum('gender', ['Female', 'Male']);
            $table->string('city', 255);
            $table->string('province', 255);
            $table->string('region', 255);
            $table->string('currentaddress', 255);
            $table->string('postalcode', 4);
             $table->string('role')->default('tenant');
            $table->string('profile_pic_url', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
