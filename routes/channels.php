<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;


/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);
    if (!$conversation) return false;

    // التحقق هل الشخص ده موجود جوه المحادثة ولا لأ؟
    return $conversation->participants()
        ->where('messageable_id', $user->id)
        ->where('messageable_type', get_class($user))
        ->exists();
        
}, ['guards' => ['web', 'teacher', 'guardian']]); // تفعيل البوابات التلاتة





