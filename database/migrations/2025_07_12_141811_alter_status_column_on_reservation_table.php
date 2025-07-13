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
        Schema::table('reservation', function (Blueprint $table) {
            $table->string('status')->default('for-confirmation')->change(); // Example: changing default
        });
    }

    public function down()
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->string('status')->default('pending')->change(); // Revert to original default
        });
    }
};
