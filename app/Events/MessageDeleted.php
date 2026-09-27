<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message_id;
    public $conversation_id;

    /**
     * Create a new event instance.
     */
    public function __construct($message_id, $conversation_id)
    {
        $this->message_id = $message_id;
        $this->conversation_id = $conversation_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // الإذاعة على نفس القناة الخاصة بالمحادثة
        return new PrivateChannel('conversation.' . $this->conversation_id);
    }

    /**
     * الداتا اللي هتروح للـ JavaScript
     */
    public function broadcastWith()
    {
        return [
            'message_id' => $this->message_id,
            'conversation_id' => $this->conversation_id,
        ];
    }
}