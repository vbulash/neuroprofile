<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToastEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $type;
    public string $title;
    public string $message;

    public function __construct(string $type, string $title, string $message)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return ['neuroprofile-channel-' . session()->getId()];
    }

    public function broadcastAs()
    {
        return 'toast-event';
    }
}
