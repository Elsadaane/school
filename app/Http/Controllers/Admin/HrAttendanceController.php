<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrEmployee;
use App\Models\HrAttendance;
use App\Models\HrShift;
use App\Models\HrLeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HrAttendanceController extends Controller
{
    // ==========================================
    // 1. عرض لوحة الحضور اليومية
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403, 'غير مصرح لك بإدارة الحضور والانصراف.');

        $date = $request->input('date', date('Y-m-d'));
        
        $attendances = HrAttendance::with(['employee.department', 'employee.shift'])
                        ->where('date', $date)
                        ->orderBy('check_in', 'asc')
                        ->get();

        return view('backend.hr.attendance.index', compact('attendances', 'date'));
    }

    // ==========================================
    // 2. معالجة ملف البصمة (Smart Import Logic)
    // ==========================================
    public function import(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403);

        $request->validate([
            'attendance_file' => 'required|file|mimes:csv,txt|max:5120'
        ], [
            'attendance_file.mimes' => 'عفواً، يجب أن يكون الملف بصيغة CSV أو TXT فقط.'
        ]);

        $file = $request->file('attendance_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        
        rewind($handle);
        fgetcsv($handle, 1000, $delimiter);

        $importedCount = 0; 
        $errorCount = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            
            if (!is_array($row) || count($row) < 3) {
                $errorCount++;
                continue;
            }

            $empCode = trim($row[0]);
            $rawDate = trim($row[1]);
            $rawTime = trim($row[2]);

            if (empty($empCode) || empty($rawDate) || empty($rawTime)) {
                $errorCount++;
                continue;
            }

            try {
                $parsedDate = Carbon::parse($rawDate)->format('Y-m-d');
                $parsedTime = Carbon::parse($rawTime)->format('H:i:s');
            } catch (\Exception $e) {
                $errorCount++;
                continue; 
            }

            $employee = HrEmployee::where('employee_code', $empCode)->first();
            if (!$employee || !$employee->shift) {
                $errorCount++;
                continue;
            }

            $shift = $employee->shift;

            try {
                $punchTime = Carbon::parse($parsedTime);
                $shiftStart = Carbon::parse($shift->start_time);

                $attendance = HrAttendance::where('employee_id', $employee->id)
                                          ->where('date', $parsedDate)
                                          ->first();

                if (!$attendance) {
                    $delayMinutes = 0;
                    $status = 'present';

                    if ($punchTime->gt($shiftStart->copy()->addMinutes($shift->grace_period_minutes))) {
                        $delayMinutes = $punchTime->diffInMinutes($shiftStart->copy()->addMinutes($shift->grace_period_minutes));
                        $status = 'late';
                    }

                    HrAttendance::create([
                        'employee_id' => $employee->id,
                        'date' => $parsedDate,
                        'check_in' => $parsedTime,
                        'check_out' => null, 
                        'delay_minutes' => $delayMinutes,
                        'overtime_minutes' => 0,
                        'status' => $status
                    ]);
                    
                    $importedCount++;

                } else {
                    $existingCheckIn = Carbon::parse($attendance->check_in);
                    
                    if ($punchTime->lt($existingCheckIn)) {
                        $delayMinutes = 0;
                        $status = 'present';

                        if ($punchTime->gt($shiftStart->copy()->addMinutes($shift->grace_period_minutes))) {
                            $delayMinutes = $punchTime->diffInMinutes($shiftStart->copy()->addMinutes($shift->grace_period_minutes));
                            $status = 'late';
                        }

                        $attendance->update([
                            'check_in' => $parsedTime,
                            'delay_minutes' => $delayMinutes,
                            'status' => $status
                        ]);
                    }
                    elseif ($punchTime->gt($existingCheckIn)) {
                        if (!$attendance->check_out || $punchTime->gt(Carbon::parse($attendance->check_out))) {
                            $attendance->update(['check_out' => $parsedTime]);
                        }
                    }
                }

            } catch (\Exception $e) {
                $errorCount++;
                continue; 
            }
        }

        fclose($handle);

        if ($importedCount > 0) {
            $message = "تم استيراد وتحليل {$importedCount} سجل بصمة بنجاح!";
            if ($errorCount > 0) {
                $message .= " (تم تجاهل {$errorCount} سجل بسبب أخطاء في البيانات)";
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'تم فحص الملف. إما أنه لا توجد بيانات جديدة، أو يوجد خطأ في تنسيق أكواد الموظفين.');
        }
    }

    // ==========================================
    // 3. التعديل اليدوي لسجل الحضور
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403);

        $request->validate([
            'check_in' => 'nullable|date_format:H:i,H:i:s',
            'check_out' => 'nullable|date_format:H:i,H:i:s',
            'delay_minutes' => 'required|integer|min:0',
            'status' => 'required|in:present,late,absent,on_leave'
        ]);

        $attendance = HrAttendance::findOrFail($id);
        
        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'delay_minutes' => $request->delay_minutes,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'تم تعديل سجل الحضور يدوياً بنجاح!');
    }

    // ==========================================
    // 🔥 4. رصد الغياب التلقائي (مُحسن للأداء) 🔥
    // ==========================================
    public function markAbsentees(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403);

        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);

        // التحقق من أيام العطلة
        if ($carbonDate->isFriday() || $carbonDate->isSaturday()) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن للنظام تسجيل غياب تلقائي في أيام العطلة الأسبوعية (الجمعة والسبت).');
        }

        // جلب الموظفين النشطين
        $activeEmployees = HrEmployee::where('status', 'active')
            ->whereNotNull('hr_shift_id')
            ->get();
        
        // جلب IDs الحضور الموجود
        $attendedIds = HrAttendance::where('date', $date)->pluck('employee_id')->toArray();
        
        // جلب IDs المجازين
        $onLeaveIds = HrLeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->pluck('employee_id')
            ->toArray();

        $newAttendances = [];
        $absentCount = 0;
        $leaveCount = 0;

        foreach ($activeEmployees as $employee) {
            
            $hasAttendance = in_array($employee->id, $attendedIds);
            $isOnLeave = in_array($employee->id, $onLeaveIds);

            if (!$hasAttendance && !$isOnLeave) {
                $newAttendances[] = [
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'check_in' => null,
                    'check_out' => null,
                    'delay_minutes' => 0,
                    'overtime_minutes' => 0,
                    'status' => 'absent',
                    'notes' => 'تسجيل غياب تلقائي من النظام (لم يسجل بصمة)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $absentCount++;
                
            } elseif (!$hasAttendance && $isOnLeave) {
                $newAttendances[] = [
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'check_in' => null,
                    'check_out' => null,
                    'delay_minutes' => 0,
                    'overtime_minutes' => 0,
                    'status' => 'on_leave',
                    'notes' => 'في إجازة رسمية معتمدة',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $leaveCount++;
            }
        }

        // إدراج البيانات دفعة واحدة
        if (count($newAttendances) > 0) {
            HrAttendance::insert($newAttendances);
        }

        if ($absentCount > 0 || $leaveCount > 0) {
            $message = "تم إقفال اليوم بنجاح!";
            if ($absentCount > 0) {
                $message .= " تم إثبات غياب ({$absentCount}) موظف.";
            }
            if ($leaveCount > 0) {
                $message .= " وتم تسجيل ({$leaveCount}) موظف في إجازة.";
            }
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('info', "جميع الكوادر سجلت حضورها أو في إجازات معتمدة لهذا اليوم.");
    }
}