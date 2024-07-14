<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNewSubscriptionStatus
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $subscription_id, $status;
    /**
     * Create a new event instance.
     */
    public function __construct($subscription_id, $status)
    {
        $this->subscription_id = $subscription_id;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('send-new-subscription-status'),
        ];
    }
    public function broadcastAs()
    {

        return 'send-new-subscription-status-'.$this->subscription_id;
    }
}
