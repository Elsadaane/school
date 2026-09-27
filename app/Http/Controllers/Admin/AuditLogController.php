<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditLogController extends Controller
{
    // ==========================================
    // 🔥 عرض سجل الرقابة والأمان 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('view_audit_logs'), 403, 'عفواً، غير مصرح لك بمشاهدة سجل الرقابة والأمان.');

        $query = Activity::with(['causer', 'subject'])->latest();

        // 🔥 الفلاتر الأساسية 🔥
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }
        
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        
        // 🔥 معالجة التواريخ 🔥
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay());
        }
        
        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        // 🔥 البحث الذكي 🔥
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhere('properties', 'LIKE', "%{$search}%");
            });
        }

        $logs = $query->paginate(30)->appends($request->all());
        $users = User::select('id', 'name')->orderBy('name')->get();
        $logNames = Activity::select('log_name')->distinct()->pluck('log_name');

        // 🔥 إحصائيات 🔥
        $totalLogs = Activity::count();
        $todayLogs = Activity::whereDate('created_at', today())->count();
        $thisWeekLogs = Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $criticalEvents = Activity::where('event', '!=', 'updated')->count();

        return view('backend.audit_logs.index', compact(
            'logs', 'users', 'logNames',
            'totalLogs', 'todayLogs', 'thisWeekLogs', 'criticalEvents'
        ));
    }

    // ==========================================
    // 🔥 عرض تفاصيل السجل 🔥
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('view_audit_logs'), 403);

        $log = Activity::with(['causer', 'subject'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'event' => $log->event,
                'causer_name' => $log->causer->name ?? 'النظام',
                'causer_email' => $log->causer->email ?? '---',
                'subject_type' => class_basename($log->subject_type),
                'subject_id' => $log->subject_id,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'ip_address' => $log->properties['ip'] ?? '---',
                'user_agent' => $log->properties['user_agent'] ?? '---',
            ]
        ]);
    }

    // ==========================================
    // 🔥 تصدير السجلات 🔥
    // ==========================================
    public function export(Request $request)
    {
        abort_if(!auth()->user()->can('export_audit_logs'), 403);

        $query = Activity::with(['causer'])->latest();

        // تطبيق نفس الفلاتر
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }
        
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay());
        }
        
        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        $logs = $query->get();

        // إنشاء CSV
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // رأس الجدول
            fputcsv($file, ['ID', 'التاريخ', 'المستخدم', 'الحدث', 'الوصف', 'النوع', 'التفاصيل']);
            
            // البيانات
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->causer->name ?? 'النظام',
                    $log->event,
                    $log->description,
                    $log->log_name,
                    json_encode($log->properties, JSON_UNESCAPED_UNICODE),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}