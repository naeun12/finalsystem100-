<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\notificationModel; // ✅ CORRECT


class NewNotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $notification;

    /**
     * Create a new event instance.
     */
   public function __construct(notificationModel $notifications)
    {
        $this->notification = $notifications;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
   public function broadcastOn(): array
{
    return [new PrivateChannel('notifications.' . $this->notification->receiverID)];
}

    
     public function broadcastWith(): array
    {
        
        return [
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'created_at' => $this->notification->created_at->toDateTimeString(),
            'receiverID' => $this->notification->receiverID, // 👈 Add this!

        ];
    }
 public function broadcastAs()
{
    \Log::info('📡 broadcastAs() called for NewNotificationEvent, receiverID: ' . $this->notification->receiverID);
    return 'NewNotificationEvent';
}


}
