<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentInstallment;
use App\Models\User; // أو Admin حسب الموديل اللي بيستقبل الإشعار
use App\Notifications\InstallmentDueNotification;
use Carbon\Carbon;

class CheckDueInstallments extends Command
{
    protected $signature = 'installments:check-due';
    protected $description = 'التحقق من الأقساط التي اقترب موعد استحقاقها وإرسال إشعارات';

    public function handle()
    {
        // هنجيب الأقساط اللي لسه متدفعتش كاملة وميعادها كمان 3 أيام
        $targetDate = Carbon::today()->addDays(3)->toDateString();

        $dueInstallments = StudentInstallment::where('is_paid', false)
                            ->whereDate('due_date', $targetDate)
                            ->get();

        // هنجيب كل المحاسبين أو المديرين عشان نبعتلهم
        $admins = User::all(); // أو User::where('role', 'admin')->get();

        foreach ($dueInstallments as $installment) {
            foreach ($admins as $admin) {
                $admin->notify(new InstallmentDueNotification($installment));
            }
        }

        $this->info('تم فحص الأقساط وإرسال الإشعارات بنجاح.');
    }
}