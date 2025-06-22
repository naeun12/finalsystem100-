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
         Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->unsignedBigInteger('dormitory_id');
            $table->string('landlord_id', 255);
            $table->string('room_number');
            $table->string('room_type');
            $table->decimal('price', 8, 2);
            $table->string('availability')->default('available');
            $table->string('furnishing_status');
            $table->string('listing_type');
            $table->string('area_sqm');
            $table->string('gender_preference');
            $table->string('room_images');
            $table->integer('capacity')->nullable();
            $table->timestamps();
            // Foreign key constraint
            $table->foreign('dormitory_id')->references('dorm_id')->on('dorms')->onDelete('cascade');
            $table->foreign('landlord_id')->references('landlord_id')->on('landlords')->onDelete('cascade');




        });
    }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('rooms');




    }
};
