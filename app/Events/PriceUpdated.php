<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cartItem;

    public function __construct($cartItem)
    {
        $this->cartItem = $cartItem;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('cart.' . $this->cartItem->user_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->cartItem->id,
            'price' => $this->cartItem->price,
            'subtotal' => $this->cartItem->quantity * $this->cartItem->price,
        ];
    }
}