<?php

namespace App\Events;

use App\Models\Plaza;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PlazaUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $plaza;

    public function __construct(Plaza $plaza)
    {
        $this->plaza = $plaza;
    }

    public function broadcastOn()
    {
        return new Channel('parking.' . $this->plaza->parking_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->plaza->id,
            'status' => $this->plaza->status,
        ];
    }
}

