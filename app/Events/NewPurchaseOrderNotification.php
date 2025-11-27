<?php

namespace App\Events;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPurchaseOrderNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Purchase $purchase;
    public Supplier $supplier;

    /**
     * Create a new event instance.
     */
    public function __construct(Purchase $purchase, Supplier $supplier)
    {
        $this->purchase = $purchase;
        $this->supplier = $supplier;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('supplier.' . $this->supplier->id),
        ];
    }
}
