<?php

namespace App\Events;

use App\Models\Slide;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebinarSlide implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $html;
    public string $script;
    public bool $hasMp3;
    /**
     * Create a new event instance.
     */
    public function __construct(Slide $slide)
    {
        $this->html = $slide->html;
        $this->script = $slide->script;
        $this->hasMp3 = !!$slide->mp3;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('webinar.update'),
        ];
    }
}
