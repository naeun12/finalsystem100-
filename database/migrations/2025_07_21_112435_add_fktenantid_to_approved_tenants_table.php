<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approved_tenants', function (Blueprint $table) {
            $table->string('fktenantID')->nullable()->after('approvedID');

            // Add foreign key constraint
            $table->foreign('fktenantID')->references('tenantID')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('approved_tenants', function (Blueprint $table) {
            $table->dropForeign(['fktenantID']);
            $table->dropColumn('fktenantID');
        });
    }
};
