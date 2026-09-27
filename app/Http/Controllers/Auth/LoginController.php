<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. عرض صفحة تسجيل الدخول بناءً على النوع
    public function showLoginForm($type)
    {
        // 🔥 ضفنا كلمة parent للمصفوفة عشان الإيرور 404 يختفي 🔥
        $allowedTypes = ['web', 'teacher', 'student', 'guardian', 'parent'];
        if (!in_array($type, $allowedTypes)) {
            abort(404);
        }

        // الكوبري: لو الرابط parent هنتعامل مع جارد guardian
        $guardName = $type === 'parent' ? 'guardian' : $type;

        // التحقق لو المستخدم مسجل دخول بالفعل
        if (Auth::guard($guardName)->check()) {
            return $this->redirectBasedOnGuard($type);
        }

        return view('auth.login', compact('type'));
    }

    // 2. معالجة بيانات تسجيل الدخول
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //         // 🔥 ضفنا parent هنا كمان عشان التوثيق (Validation) ميضربش 🔥
    //         'type' => 'required|in:web,teacher,student,guardian,parent' 
    //     ]);

    //     $type = $request->type;
    //     $guardName = $type === 'parent' ? 'guardian' : $type;

    //     // محاولة تسجيل الدخول
    //     if (Auth::guard($guardName)->attempt(['email' => $request->email, 'password' => $request->password], $request->filled('remember'))) {
    //         return $this->redirectBasedOnGuard($type);
    //     }

    //     return back()->withErrors([
    //         'email' => trans('auth.failed') 
    //     ])->withInput($request->only('email', 'remember'));
    // }

            public function login(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
                'type' => 'required|in:web,teacher,student,guardian,parent' 
            ]);

            $type = $request->type;
            $guardName = $type === 'parent' ? 'guardian' : $type;
            $credentials = ['email' => $request->email, 'password' => $request->password];

            // 🔥 التحقق من status
            $credentials['status'] = 'active'; // ✅ السماح فقط للمستخدمين النشطين

            if (Auth::guard($guardName)->attempt($credentials, $request->filled('remember'))) {
                return $this->redirectBasedOnGuard($type);
            }

            return back()->withErrors([
                'email' => 'بيانات الدخول غير صحيحة أو الحساب معطل' 
            ])->withInput($request->only('email'));
        }

    // 3. تسجيل الخروج
    public function logout(Request $request, $type)
    {
        $guardName = $type === 'parent' ? 'guardian' : $type;
        
        Auth::guard($guardName)->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.show', $type);
    }

    // دالة مساعدة لتوجيه كل مستخدم للداشبورد بتاعته
    private function redirectBasedOnGuard($type)
    {
        switch ($type) {
            case 'web':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            case 'guardian':
            case 'parent': // 🔥 توجيه سليم لداشبورد ولي الأمر 🔥
                return redirect()->route('parent.dashboard');
            default:
                return redirect('/');
        }
    }
}