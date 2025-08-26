<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dorm_reviews', function (Blueprint $table) {
            $table->id('reviewID');

            // Foreign keys
            $table->unsignedBigInteger('fkdormID');
            $table->unsignedBigInteger('fkapprovedID'); // tenantID is string type

            // Rating & Review
            $table->unsignedTinyInteger('rating') // 1 to 5 stars
                  ->comment('Tenant rating (1-5 stars)');
            $table->text('review')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('fkdormID')
                  ->references('dormID')
                  ->on('dorms')
                  ->onDelete('cascade');

            $table->foreign('fkapprovedID')
                  ->references('approvedID')
                  ->on('approved_tenants')
                  ->onDelete('cascade');

            // Prevent duplicate reviews by the same tenant for the same dorm
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dorm_reviews');
    }
};
