<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('approved_tenants', function (Blueprint $table) {
            $table->id('approvedID');
            $table->unsignedBigInteger('fkroomID');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contactNumber');
            $table->string('contactEmail');
            $table->integer('age');
            $table->string('gender');
            $table->string('move-in-date');
            $table->string('move-out-date');
            $table->string('paymentType')->nullable();
            $table->string('paymentImage')->nullable();
            $table->string('studentpictureId')->nullable();
            $table->timestamps();

            $table->foreign('fkroomID')->references('roomID')->on('rooms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approved_tenants');
    }
};
