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
             $table->string('fktenantID');
            // Add foreign key constraint
            $table->foreign('fktenantID')->references('tenantID')->on('tenants')->onDelete('cascade');
             $table->string('source_type'); // 'booking' or 'reservation'
            $table->unsignedBigInteger('source_id'); 
            
            $table->unsignedBigInteger('fkroomID');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contactNumber');
            $table->string('contactEmail');
            $table->integer('age');
            $table->string('gender');
            $table->string('moveInDate');
            $table->string('moveOutDate');
            $table->string('status')->defualt('active');
            $table->boolean('notifyRent')->default(false);

    $table->enum('extension_decision', ['pending', 'extend', 'not_extending'])
          ->default('pending');
            $table->string('pictureID')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fkroomID')->references('roomID')->on('rooms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approved_tenants');
    }
};
