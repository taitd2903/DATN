<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;
    public $receiverId;
    public $userName;

    public function __construct($message, $userId, $receiverId, $userName)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->userName = $userName;
    }

    public function broadcastOn()
    {
        return new Channel('chat.user.' . $this->receiverId);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'userId' => $this->userId,
            'receiverId' => $this->receiverId,
            'userName' => $this->userName
        ];
    }
}
