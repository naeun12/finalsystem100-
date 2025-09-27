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
            $table->id('roomID');
            $table->unsignedBigInteger('fkdormID');
            $table->string('fklandlordID', 255);
            $table->string('roomNumber');
            $table->string('roomType');
            $table->decimal('price', 8, 2);
            $table->string('availability')->default('available');
            $table->string('furnishing_status');
            $table->string('listingType');
            $table->string('areaSqm');
            $table->string('genderPreference');
            $table->string('roomImages');
              $table->boolean('isReservable')
                  ->default(false)
                  ->comment('1 = can be reserved even if occupied, 0 = cannot');
            $table->timestamps();
            $table->foreign('fkdormID')->references('dormID')->on('dorms')->onDelete('cascade');
            $table->foreign('fklandlordID')->references('landlordID')->on('landlords')->onDelete('cascade');
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
