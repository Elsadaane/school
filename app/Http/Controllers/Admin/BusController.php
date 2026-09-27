<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Branch; 
use App\Models\AcademicYear;
use App\Models\Branche;
use App\Models\HrCustody;
use App\Models\HrEmployee;
use App\Models\TransportRoute; // ✅ تم إضافة موديل خطوط السير
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusController extends Controller
{
    // ==========================================
    // 1. عرض قائمة الحافلات (مع الفلاتر الذكية 🔥)
    // ==========================================
    public function index(Request $request)
    {
        $branches = Branche::all(); 
        $academicYears = AcademicYear::latest()->get();

        // 🔄 تحديث العلاقات المحملة وجلب عدد العقود (الصباحي + المسائي)
        $query = Bus::with(['academicYear', 'branch', 'transportRoute', 'driver', 'supervisor'])
                    ->withCount(['morningContracts', 'afternoonContracts'])
                    ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bus_number', 'like', "%{$search}%")
                  // 🔥 البحث في خط السير بدلاً من عمود route المحذوف
                  ->orWhereHas('transportRoute', function($routeQuery) use ($search) {
                      $routeQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('driver', function($driverQuery) use ($search) {
                      $driverQuery->where('full_name_ar', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $buses = $query->paginate(15)->withQueryString();
        
        // 🔄 حساب المقاعد المتاحة ديناميكياً للـ View (فصل الصباحي عن المسائي) 🔥
        $buses->getCollection()->transform(function ($bus) {
            $bus->available_morning_seats = max(($bus->capacity ?? 0) - $bus->morning_contracts_count, 0);
            $bus->available_afternoon_seats = max(($bus->capacity ?? 0) - $bus->afternoon_contracts_count, 0);
            return $bus;
        });

        return view('backend.buses.index', compact('buses', 'branches', 'academicYears'));
    }

    // ==========================================
    // 2. فتح صفحة إضافة حافلة
    // ==========================================
    public function create()
    {
        $academicYears = AcademicYear::latest()->get();
        $branches = Branche::all(); 
        
        // جلب السائقين
        $drivers = HrEmployee::where(function($query) {
                $query->where('job_title', 'like', '%سائق%')
                      ->orWhere('job_title', 'like', '%سواق%')
                      ->orWhere('job_title', 'like', '%driver%');
            })
            ->where('status', 'active')
            ->get();

        // 🆕 جلب المشرفين
        $supervisors = HrEmployee::where(function($query) {
                $query->where('job_title', 'like', '%مشرف%')
                      ->orWhere('job_title', 'like', '%مراقب%')
                      ->orWhere('job_title', 'like', '%supervisor%');
            })
            ->where('status', 'active')
            ->get();
        
        // 🆕 جلب خطوط السير
        $transportRoutes = TransportRoute::all();

        return view('backend.buses.create', compact('academicYears', 'branches', 'drivers', 'supervisors', 'transportRoutes'));
    }

    // ==========================================
    // 3. حفظ بيانات الحافلة الجديدة
    // ==========================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'transport_route_id' => 'nullable|exists:transport_routes,id', // 🔄 تم التحديث
            'driver_id' => 'nullable|exists:hr_employees,id',
            'supervisor_id' => 'nullable|exists:hr_employees,id', // 🆕 إضافة المشرف
            'bus_number' => 'required|string|max:255|unique:buses,bus_number',
            
            'ownership_type' => 'required|in:owned,rented',
            'monthly_rent' => 'nullable|numeric|min:0',
            
            'bus_type' => 'nullable|string|max:255',
            'bus_model' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'insurance_expiry_date' => 'nullable|date', // 🆕 حقول تواريخ جديدة
            'registration_expiry_date' => 'nullable|date',
            
            'driver_name' => 'required_without:driver_id|string|max:255|nullable',
            'driver_national_id' => 'nullable|string|max:255',
            'driver_phone' => 'required_without:driver_id|string|max:255|nullable',
            'notes' => 'nullable|string',
            'contract_terms_ar' => 'nullable|string',
            'contract_terms_en' => 'nullable|string',
            'signature_data' => 'nullable|string',
        ]);

        // تصفير الإيجار لو الحافلة مملوكة للمدرسة
        if ($data['ownership_type'] === 'owned') {
            $data['monthly_rent'] = 0;
        }

        try {
            DB::beginTransaction();
            $bus = Bus::create($data);

            // تسجيل عهدة السائق
            if ($bus->driver_id) {
                \App\Models\HrCustody::create([
                    'employee_id' => $bus->driver_id,
                    'item_name'   => 'حافلة مدرسية (' . ($bus->ownership_type == 'owned' ? 'مملوكة' : 'مستأجرة') . ')',
                    'item_code'   => $bus->bus_number,
                    'receive_date'=> now(),
                    'status'      => 'in_use',
                    'notes'       => 'تم إسناد الحافلة تلقائياً من نظام الحركة.',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.buses.index')->with('success', 'تمت إضافة الحافلة بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // 4. عرض تفاصيل الحافلة
    // ==========================================
    public function show($id)
    {
        // 🔄 تحديث العلاقات المحملة
        $bus = Bus::with(['academicYear', 'branch', 'transportRoute', 'driver', 'supervisor', 'morningContracts.student', 'afternoonContracts.student'])->findOrFail($id);
        return view('backend.buses.show', compact('bus'));
    }

    // ==========================================
    // 5. فتح صفحة التعديل
    // ==========================================
    public function edit($id)
    {
        $bus = Bus::findOrFail($id);
        $academicYears = AcademicYear::latest()->get();
        $branches = Branche::all(); 
        
        $drivers = HrEmployee::where(function($query) {
                $query->where('job_title', 'like', '%سائق%')
                      ->orWhere('job_title', 'like', '%driver%');
            })->where('status', 'active')->get();

        $supervisors = HrEmployee::where(function($query) {
                $query->where('job_title', 'like', '%مشرف%')
                      ->orWhere('job_title', 'like', '%supervisor%');
            })->where('status', 'active')->get();
        
        $transportRoutes = TransportRoute::all();
        
        return view('backend.buses.edit', compact('bus', 'academicYears', 'branches', 'drivers', 'supervisors', 'transportRoutes'));
    }

    // ==========================================
    // 6. حفظ التعديلات
    // ==========================================
    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'transport_route_id' => 'nullable|exists:transport_routes,id', // 🔄 تم التحديث
            'driver_id' => 'nullable|exists:hr_employees,id',
            'supervisor_id' => 'nullable|exists:hr_employees,id', // 🆕 إضافة المشرف
            'bus_number' => 'required|string|max:255|unique:buses,bus_number,' . $id,
            
            'ownership_type' => 'required|in:owned,rented',
            'monthly_rent' => 'nullable|numeric|min:0',

            'bus_type' => 'nullable|string|max:255',
            'bus_model' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'insurance_expiry_date' => 'nullable|date',
            'registration_expiry_date' => 'nullable|date',

            'driver_name' => 'required_without:driver_id|string|max:255|nullable',
            'driver_national_id' => 'nullable|string|max:255',
            'driver_phone' => 'required_without:driver_id|string|max:255|nullable',
            'notes' => 'nullable|string',
            'contract_terms_ar' => 'nullable|string',
            'contract_terms_en' => 'nullable|string',
            'signature_data' => 'nullable|string', 
        ]);

        if (empty($data['signature_data'])) {
            unset($data['signature_data']);
        }

        if ($data['ownership_type'] === 'owned') {
            $data['monthly_rent'] = 0;
        }

        $oldDriverId = $bus->driver_id;
        
        $bus->update($data);

        // التلاعب في العهدة لو تم تغيير السائق
        if ($oldDriverId != $bus->driver_id) {
            if ($oldDriverId) {
                \App\Models\HrCustody::where('employee_id', $oldDriverId)
                         ->where('item_code', $bus->bus_number)
                         ->where('status', 'in_use')
                         ->update(['status' => 'returned', 'return_date' => now()]);
            }
            if ($bus->driver_id) {
                \App\Models\HrCustody::create([
                    'employee_id' => $bus->driver_id,
                    'item_name'   => 'حافلة مدرسية (' . ($bus->ownership_type == 'owned' ? 'مملوكة' : 'مستأجرة') . ')',
                    'item_code'   => $bus->bus_number,
                    'receive_date'=> now(),
                    'status'      => 'in_use',
                    'notes'       => 'إسناد جديد بسبب تعديل بيانات الحافلة.',
                ]);
            }
        }

        return redirect()->route('admin.buses.index')->with('success', 'تم تحديث بيانات الحافلة بنجاح.');
    }

    // ==========================================
    // 7. طباعة العقد
    // ==========================================
    public function printContract($id)
    {
        $bus = Bus::with(['academicYear', 'branch', 'transportRoute'])->findOrFail($id);
        return view('backend.buses.print', compact('bus'));
    }

    // ==========================================
    // 8. الحذف الآمن (Soft Delete) ✅
    // ==========================================
    public function destroy($id)
    {
        $bus = Bus::findOrFail($id);

        // 🔄 التحقق هل الباص مسجل في عقود الطلاب (ذهاب أو إياب)؟
        $hasContracts = DB::table('student_contracts')
            ->where(function($q) use ($bus) {
                $q->where('morning_bus_id', $bus->id)
                  ->orWhere('afternoon_bus_id', $bus->id);
            })
            ->exists();

        if ($hasContracts) {
            return redirect()->back()->withErrors(['error' => '❌ لا يمكن حذف هذه الحافلة لأنها مرتبطة بعقود طلاب (ذهاب أو إياب)! يرجى إزالة الحافلة من العقود أولاً.']);
        }

        $bus->delete();

        return redirect()->back()->with('success', 'تم أرشفة الحافلة بنجاح (يمكن استعادتها لاحقاً).');
    }

    // ==========================================
    // 🆕 9. استعادة حافلة محذوفة (Restore)
    // ==========================================
    public function restore($id)
    {
        $bus = Bus::withTrashed()->findOrFail($id);
        $bus->restore();

        return redirect()->back()->with('success', 'تمت استعادة الحافلة بنجاح.');
    }

    // ==========================================
    // 🆕 10. عرض الحافلات المؤرشفة
    // ==========================================
    public function archived()
    {
        $buses = Bus::onlyTrashed()->with(['academicYear', 'branch', 'transportRoute'])->latest('deleted_at')->paginate(15);
        return view('backend.buses.archived', compact('buses'));
    }
}