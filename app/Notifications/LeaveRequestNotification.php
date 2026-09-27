<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    public $leaveRequest;
    public $studentName;

    public function __construct($leaveRequest, $studentName)
    {
        $this->leaveRequest = $leaveRequest;
        $this->studentName = $studentName;
    }

    public function via($notifiable)
    {
        return ['database']; // هنحفظ الإشعار في الداتا بيز عشان يظهر في القائمة العلوية
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'طلب استئذان جديد',
            'message' => 'تم تقديم طلب استئذان للطالب: ' . $this->studentName,
            'url' => route('admin.leave_requests.index'), // الرابط اللي المدير هيروحله لما يدوس
            'icon' => 'mdi-hospital-box-outline',
            'leave_id' => $this->leaveRequest->id
        ];
    }
}