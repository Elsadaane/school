<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InstallmentDueNotification extends Notification
{
    use Queueable;

    public $installment;

    public function __construct($installment)
    {
        $this->installment = $installment;
    }

    public function via($notifiable)
    {
        return ['database']; // ممكن تضيف 'mail' لو عايز تبعت إيميل كمان
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'تذكير بقسط مستحق',
            'message' => 'يوجد قسط مستحق بقيمة ' . ($this->installment->amount - $this->installment->paid_amount) . ' OMR للطالب ' . $this->installment->contract->student->first_name,
            'installment_id' => $this->installment->id,
            'due_date' => $this->installment->due_date,
            'url' => route('admin.student-contracts.show', $this->installment->student_contract_id)
        ];
    }
}