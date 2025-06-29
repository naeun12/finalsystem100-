
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('room_features_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fkroom_id');
            $table->unsignedBigInteger('fkfeature_id');
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('fkroom_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('fkfeature_id')->references('id')->on('room_features')->onDelete('cascade');


            $table->unique(['fkroom_id', 'fkfeature_id']); // prevent duplicates
        });
    }


    public function down()
    {
        Schema::dropIfExists('room_features_rooms');
    }
};




?>

