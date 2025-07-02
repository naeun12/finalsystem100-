
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->id('reservationID');
            $table->unsignedBigInteger('fkdormitoryID');
            $table->unsignedBigInteger('fkroomID');
            $table->string('fktenantID');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contact_number');
            $table->string('contact_email');
            $table->string('age');
            $table->string('gender');
            $table->string('status')->default('pending')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_image')->nullable();
            $table->string('studentpicture_id')->nullable();
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('fkdormitoryID')->references('dorm_id')->on('dorms')->onDelete('cascade');
            $table->foreign('fkroomID')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('fktenantID')->references('tenant_id')->on('tenants')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('reservation');
    }
};




?>

