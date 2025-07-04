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
            $table->unsignedBigInteger('conversation_id')->nullable(); // optional thread ID
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_role', ['tenant', 'landlord', 'admin']);
            $table->unsignedBigInteger('receiver_id');
            $table->enum('receiver_role', ['tenant', 'landlord', 'admin']);
            $table->text('message');
            $table->unsignedBigInteger('reply_to_id')->nullable(); // reply to message
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->useCurrent();
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
