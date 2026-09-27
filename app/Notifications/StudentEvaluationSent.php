<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class StudentEvaluationSent extends Notification
{
    use Queueable;

    public $evaluation;
    public $teacherName;
    public $subjectName;

    public function __construct($evaluation, $teacherName, $subjectName)
    {
        $this->evaluation = $evaluation;
        $this->teacherName = $teacherName;
        $this->subjectName = $subjectName;
    }

    public function via($notifiable)
    {
        // هنرسل الإشعار في الداتا بيز عشان يظهر في جرس الإشعارات عند الطالب
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'تقييم دراسي جديد 📊',
            'body' => "قام أ. {$this->teacherName} بإصدار تقييمك الجديد في مادة {$this->subjectName}. التقييم العام: {$this->evaluation->overall_level}",
            'url' => route('student.evaluations.show', $this->evaluation->id), // راوت هنبقى نعمله لبوابة الطالب
            'icon' => 'bx-star'
        ];
    }
}