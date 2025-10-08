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
        Schema::create('approved_tenants', function (Blueprint $table) {
            $table->id('approvedID'); // bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->string('fktenantID'); // varchar(255) NOT NULL
            $table->string('source_type'); // varchar(255) NOT NULL
            $table->unsignedBigInteger('source_id'); // bigint(20) UNSIGNED NOT NULL
            $table->unsignedBigInteger('fkroomID'); // bigint(20) UNSIGNED NOT NULL
            $table->string('firstname'); // varchar(255) NOT NULL
            $table->string('lastname'); // varchar(255) NOT NULL
            $table->string('contactNumber'); // varchar(255) NOT NULL
            $table->string('contactEmail'); // varchar(255) NOT NULL
            $table->integer('age'); // int(11) NOT NULL
            $table->string('gender'); // varchar(255) NOT NULL
            $table->string('moveInDate'); // varchar(255) NOT NULL
            $table->string('moveOutDate'); // varchar(255) NOT NULL
            $table->dateTime('extensionDate')->nullable(); // datetime DEFAULT NULL
            $table->string('status'); // varchar(255) NOT NULL
            $table->boolean('has_rated')->default(false); // tinyint(1) NOT NULL DEFAULT 0
            $table->boolean('isDeleted')->default(false); // tinyint(1) NOT NULL DEFAULT 0
            $table->enum('paymentOption', ['online','onsite'])->default('onsite'); // enum NOT NULL DEFAULT 'onsite'
            $table->enum('extension_payment_status', ['pending','done'])->default('pending'); // enum DEFAULT 'pending'
            $table->boolean('notifyRent')->default(false); // tinyint(1) NOT NULL DEFAULT 0
            $table->enum('extension_decision', ['pending','extend','not_extending'])->default('pending'); // enum NOT NULL DEFAULT 'pending'
            $table->string('pictureID')->nullable(); // varchar(255) DEFAULT NULL
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_tenants');
    }
};
