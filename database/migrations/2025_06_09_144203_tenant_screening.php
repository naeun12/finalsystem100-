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
        Schema::create('tenant_screening', function (Blueprint $table) {
            $table->id('tenantscreening_id');
            $table->unsignedBigInteger('fkdormitory_id');
            $table->unsignedBigInteger('fkroom_id');
            $table->string('fktenant_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('student_id');
            $table->string('course');
            $table->string('contact_number');
            $table->string('contact_email');
            $table->string('age');
            $table->string('gender');
            $table->string('status')->default('pending')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_image')->nullable();
            $table->string('studentpicture_id')->nullable();
            $table->timestamps();
       
            $table->foreign('fkdormitory_id')->references('dorm_id')->on('dorms')->onDelete('cascade');
            $table->foreign('fkroom_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('fktenant_id')->references('tenant_id')->on('tenants')->onDelete('cascade');
        });
   
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_screening');


    }
};
