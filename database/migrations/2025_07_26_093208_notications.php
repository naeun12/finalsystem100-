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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('senderID');        // ID of who sent the notification
            $table->string('senderType');                  // 'landlord' or 'tenant'
            $table->string('receiverID');      // ID of who will receive
            $table->string('receiverType');                // 'landlord' or 'tenant'
            $table->string('title');                        // Notification title
            $table->text('message');                        // Notification message
            $table->boolean('isRead')->default(false);     // Read status
            $table->timestamp('readAt')->nullable();       // Optional read timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');

    }
};
