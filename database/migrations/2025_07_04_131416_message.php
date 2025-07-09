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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversationID')->nullable(); // optional thread ID
            $table->string('senderID');
            $table->enum('senderRole', ['tenant', 'landlord', 'admin']);
            $table->string('receiverID');
            $table->enum('receiverRole', ['tenant', 'landlord', 'admin']);
            $table->text('message');
            $table->unsignedBigInteger('replyToID')->nullable(); // reply to message
            $table->boolean('isRead')->default(false);
            $table->timestamp('sentAt')->useCurrent();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');

    }
};
