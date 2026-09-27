<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. حل مشكلة طول النصوص في قواعد البيانات
        Schema::defaultStringLength(191);

        // =========================================================
        // 🌟 أولاً: الإعدادات العامة (مكيّشة ومتاحة في كل المشروع)
        // =========================================================
        try {
            // حفظ الإعدادات في الذاكرة المؤقتة لضمان سرعة النظام (0 Queries)
            $globalSettings = Cache::rememberForever('global_settings', function () {
                return Setting::first() ?? new Setting();
            });

            // نشر الإعدادات في كل ملفات الـ Blade
            View::share('globalSettings', $globalSettings);
            
        } catch (\Exception $e) {
            // يتم تجاهل الخطأ وقت تنفيذ المايجريشن لأول مرة
        }
        Paginator::useBootstrapFive(); // 👈 وضيف السطر ده هنا

        // =========================================================
        // 🔔 ثانياً: تنبيهات نواقص المخزون (مخصصة للـ Topbar والـ Sidebar فقط)
        // =========================================================
        // لو اسم ملف القائمة الجانبية عندك مختلف، عدل 'backend.layouts.sidebar' لاسم الملف بتاعك
        View::composer(['backend.layouts.topbar', 'backend.layouts.sidebar', 'backend.layouts.shared.left-sidebar'], function ($view) {
            try {
                $lowStockProducts = Product::where('type', 'product')
                                           ->whereColumn('quantity', '<=', 'alert_quantity')
                                           ->get();
                
                $view->with([
                    'lowStockCount' => $lowStockProducts->count(),
                    'lowStockAlerts' => $lowStockProducts
                ]);
            } catch (\Exception $e) {
                // لو في إيرور (مثلاً الجدول لسه متعملش)، يرجع قيم افتراضية عشان الشاشة متضربش
                $view->with([
                    'lowStockCount' => 0, 
                    'lowStockAlerts' => collect()
                ]);
            }
        });
    }
}