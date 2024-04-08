<?php

namespace App\Events;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Calling implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $title, $to, $content, $description, $type,$read_at;

    /**
     * Create a new event instance.
     */
    public function __construct($title, $type, $description, $content, $to)
    {
        $this->title = $title;
        $this->type =  $type;
        $this->content = $content;
        $this->description = $description;
        $this->to = $to;
        $this->read_at = Carbon::now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [
            new Channel('initiate-call'),
        ];
    }



    public function broadcastAs()
    {

        return 'call-to-' . $this->to;
    }
    public function broadcastWith()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type, 'content' => $this->content, 'to' => $this->to
        ];
    }
    public function handle()
    {
        // Save the notification to the database
        // Notification::create([
        //     'title' => $this->title,
        //     'description' => $this->description,
        //     'type' => $this->type, 'content' => json_encode($this->content), 'to' => $this->to
        // ]);
    }
}
