<?php

namespace App\Events;

use App\Model\flash_ads;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FlashMessages implements ShouldBroadcast
{
    use SerializesModels;

    public $flash_ads;

    /**
     * Create a new event instance.
     *
     * @param  \App\flash_ads  $flash_ads
     * @return void
     */
    public function __construct(flash_ads $flash_ads)
    {
        $this->flash_ads = $flash_ads;
    }

    public function broadcastOn()
    {
        return new Channel('flash-ads');
    }

    public function broadcastAs()
    {
        return 'flash';
    }

}