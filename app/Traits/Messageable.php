<?php

namespace App\Traits;

use App\Models\Conversation;
use App\Models\Message;

trait Messageable
{
    // جلب كل المحادثات اللي الشخص ده مشارك فيها
    public function conversations()
    {
        return $this->morphToMany(Conversation::class, 'messageable', 'conversation_participants');
    }

    // جلب كل الرسائل اللي الشخص ده بعتها
    public function messages()
    {
        return $this->morphMany(Message::class, 'sender');
    }
}