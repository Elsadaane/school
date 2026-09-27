<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TeacherScheduleUpdated extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // ممكن تزود 'mail' لو شغالين ايميلات لاحقاً
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'تحديث في جدول الحصص 📅',
            'body' => $this->message,
            'url' => route('teacher.schedule.index'), // الراوت الخاص بصفحة المعلم
            'icon' => 'bx-calendar-event'
        ];
    }
}