<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }
    
    // الدالة دي بتجيب الطرف التاني في المحادثة الفردية
    public function getOtherParticipantAttribute()
    {
        // 🔥 الحل: جلب المستخدم الحالي من أي بوابة (Guard) 🔥
        $currentUser = auth('web')->user() ?? auth('teacher')->user() ?? auth('guardian')->user();
        
        if (!$currentUser) {
            return null;
        }
        
        return $this->participants()
            ->where(function($q) use ($currentUser) {
                $q->where('messageable_id', '!=', $currentUser->id)
                  ->orWhere('messageable_type', '!=', get_class($currentUser));
            })->first()?->messageable;
    }
}