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
        Schema::create('rules_and_policies', function (Blueprint $table) {
            $table->id();
            $table->string('rules_name');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('rules_and_policies');
    }
};
