<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate; // 🔥 شيلنا الكومنت من هنا عشان نقدر نستخدم الـ Gate
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 🔥 السماح للـ super-admin بتخطي كل قيود الصلاحيات أوتوماتيك 🔥
        Gate::before(function ($user, $ability) {
            // التأكد أولاً أن الكائن يمتلك دالة hasRole لتجنب الأخطاء مع جارد ولي الأمر (Guardian)
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('super-admin') ? true : null;
            }
            return null;
        });
    }
}