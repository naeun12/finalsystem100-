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
        Schema::table('dorms', function (Blueprint $table) {
            // Add new column for tracking views
            $table->unsignedBigInteger('views')->default(0)->after('gcashNumber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dorms', function (Blueprint $table) {
            $table->dropColumn('views');
        });
    }
};
