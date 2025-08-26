<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('rulesAndPolicyDorm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fkdormID');
            $table->unsignedBigInteger('fkruleID');
            $table->timestamps();
            $table->foreign('fkdormID')->references('dormID')->on('dorms')->onDelete('cascade');
            $table->foreign('fkruleID')->references('id')->on('rulesAndPolicies')->onDelete('cascade');
            $table->unique(['fkdormID', 'fkruleID']); // prevent duplicates
        });
    }


    public function down()
    {
        Schema::dropIfExists('rulesAndPolicyDorm');
    }
};




?>
