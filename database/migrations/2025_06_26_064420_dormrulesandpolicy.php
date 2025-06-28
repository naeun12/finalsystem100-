<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('rules_and_policy_dorm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fkdorm_id');
            $table->unsignedBigInteger('fkrules_id');
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('fkdorm_id')->references('dorm_id')->on('dorms')->onDelete('cascade');
            $table->foreign('fkrules_id')->references('id')->on('rules_and_policies')->onDelete('cascade');


            $table->unique(['fkdorm_id', 'fkrules_id']); // prevent duplicates
        });
    }


    public function down()
    {
        Schema::dropIfExists('rules_and_policy_dorm');
    }
};




?>
