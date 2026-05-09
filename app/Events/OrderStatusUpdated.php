<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $order;

    public function __construct(array $order)
    {
        $this->order = $order;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('orders.' . $this->order['tenant_id']);
    }

    public function broadcastAs(): string
    {
        return 'order.status';
    }
}
