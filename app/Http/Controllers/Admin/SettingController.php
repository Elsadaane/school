<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr; 
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    // ==========================================
    // 🔥 1. عرض إعدادات النظام 🔥
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_settings'), 403, 'غير مصرح لك بالدخول لإعدادات النظام العامة.');

        $settings = Setting::firstOrCreate(
            [],
            [
                'company_name_ar' => 'أركان السد العالي للاستثمار ش م م',
                'cr_number'       => '1642397',
                'phone_code'      => '+968',
                'tax_value'       => 5.00
            ]
        );

        return view('backend.settings.index', compact('settings'));
    }

    // ==========================================
    // 🔥 2. تحديث إعدادات النظام والصور 🔥
    // ==========================================
    public function update(Request $request)
    {
        abort_if(!auth()->user()->can('manage_settings'), 403);

        // 🌟 تحديث الـ Validation ليشمل كافة الحقول الموجودة في قاعدة البيانات 🌟
        $validated = $request->validate([
            // بيانات الشركة
            'company_name_ar' => 'required|string|max:255',
            'company_name_en' => 'nullable|string|max:255',
            'cr_number'       => 'nullable|string|max:100',
            'tax_number'      => 'nullable|string|max:100', // 🔥 تم الإضافة
            'tax_value'       => 'required|numeric|min:0|max:100',
            
            // العناوين والتواصل
            'address_ar'      => 'nullable|string|max:500', // 🔥 تم التعديل لتطابق المايجريشن
            'address_en'      => 'nullable|string|max:500', // 🔥 تم الإضافة
            'governorate_ar'  => 'nullable|string|max:255', // 🔥 تم الإضافة
            'governorate_en'  => 'nullable|string|max:255', // 🔥 تم الإضافة
            'wilayat_ar'      => 'nullable|string|max:255', // 🔥 تم الإضافة
            'wilayat_en'      => 'nullable|string|max:255', // 🔥 تم الإضافة
            'building_number' => 'nullable|string|max:255', // 🔥 تم الإضافة
            'po_box'          => 'nullable|string|max:255', // 🔥 تم الإضافة
            'postal_code'     => 'nullable|string|max:255', // 🔥 تم الإضافة
            'email'           => 'nullable|email|max:255',
            'phone_code'      => 'nullable|string|max:10',
            'phone'           => 'nullable|string|max:20',
            
            // الشروط والأحكام (نصوص طويلة تدعم HTML)
            'student_contract_terms_ar' => 'nullable|string', // 🔥 تم الإضافة
            'student_contract_terms_en' => 'nullable|string', // 🔥 تم الإضافة
            'general_terms_ar'          => 'nullable|string', // 🔥 تم الإضافة
            'general_terms_en'          => 'nullable|string', // 🔥 تم الإضافة
            'teacher_contract_terms_ar' => 'nullable|string', // 🔥 تم الإضافة
            'teacher_contract_terms_en' => 'nullable|string', // 🔥 تم الإضافة
            'driver_contract_terms_ar'  => 'nullable|string', // 🔥 تم الإضافة
            'driver_contract_terms_en'  => 'nullable|string', // 🔥 تم الإضافة
            
            // حماية الصور المرفوعة
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'seal'            => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'header_image'    => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'contract_header' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'contract_footer' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $settings = Setting::first();

        // استخراج البيانات النصية فقط لتحديثها في قاعدة البيانات
        $data = Arr::except($validated, ['logo', 'seal', 'header_image', 'contract_header', 'contract_footer']);

        // معالجة رفع الصور
        $images = ['logo', 'seal', 'header_image', 'contract_header', 'contract_footer'];
        foreach ($images as $img) {
            if ($request->hasFile($img)) {
                // مسح الصورة القديمة من السيرفر إن وجدت
                if ($settings->$img) {
                    Storage::disk('public')->delete($settings->$img);
                }
                $data[$img] = $request->file($img)->store('settings', 'public');
            }
        }

        // تحديث قاعدة البيانات
        $settings->update($data);

        // مسح الكاش القديم عشان النظام يسحب التحديثات فوراً ويسمع في كل الصفحات
        Cache::forget('global_settings');

        return back()->with('success', 'تم تحديث إعدادات النظام بنجاح!');
    }
}