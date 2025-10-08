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
        Schema::create('dorms', function (Blueprint $table) {
        $table->id('dormID');
        $table->string('dormName');
        $table->string('fklandlordID', 255);
        $table->text('address');
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);
        $table->text('description')->nullable();
        $table->integer('totalRooms');
        $table->string('contactEmail');
        $table->string('contactPhone');
        $table->string('gcashNumber');
        $table->unsignedBigInteger('views')->default(0);
        $table->string('availability',255)->default('Not Available');
        $table->string('occupancyType',255);
        $table->string('buildingType',255);
        $table->timestamps();
        $table->foreign('fklandlordID')->references('landlordID')->on('landlords')->onDelete('cascade');


        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dorms');


    }
};
