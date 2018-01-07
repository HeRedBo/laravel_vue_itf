<?php

namespace App\Events;

use App\Models\Admin\Logger;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 后台管理员日志时间处理
 */
class AdminLogger
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($venue_id, $catalog, $intro)
    {
        
        $this->model    = new Logger();
        $this->catalog  = $catalog;
        $this->intro    = $intro;
        $this->venue_id = $venue_id;
    }



    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
