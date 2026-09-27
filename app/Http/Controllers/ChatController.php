<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Guardian;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    /**
     * جلب المستخدم الحالي بناءً على البوابة النشطة
     */
    private function getActiveUser()
    {
        if (auth('web')->check()) return auth('web')->user();
        if (auth('teacher')->check()) return auth('teacher')->user();
        if (auth('student')->check()) return auth('student')->user();
        if (auth('guardian')->check()) return auth('guardian')->user();
        abort(403, 'غير مصرح لك بالدخول');
    }

    // ==========================================
    // 1. فتح شاشة الشات
    // ==========================================
    public function index()
    {
        $user = $this->getActiveUser();
        
        $conversations = $user->conversations()
            ->with('participants.messageable') 
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->whereNull('read_at')
                      ->where(function ($q) use ($user) {
                          $q->where('sender_id', '!=', $user->id)
                            ->orWhere('sender_type', '!=', get_class($user));
                      });
            }])
            ->latest('updated_at')
            ->get();
        
        $cacheKey = 'chat_status_' . str_replace('\\', '_', get_class($user)) . '_' . $user->id;
        $currentStatus = Cache::get($cacheKey, 'online');
        
        return view('chat.index', compact('conversations', 'user', 'currentStatus'));
    }

    // ==========================================
    // 2. جلب رسائل محادثة معينة
    // ==========================================
    public function fetchMessages(Request $request, int $conversation_id)
    {
        $user = $this->getActiveUser();
        
        $conversation = $user->conversations()->findOrFail($conversation_id);
        
        $conversation->messages()
            ->whereNull('read_at')
            ->where(function ($q) use ($user) {
                $q->where('sender_id', '!=', $user->id)
                  ->orWhere('sender_type', '!=', get_class($user));
            })
            ->update(['read_at' => now()]);
            
        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->paginate(50);

        return response()->json($messages);
    }

    // ==========================================
    // 3. إرسال رسالة
    // ==========================================
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required_without:attachment|string|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf,docx,zip|max:5120'
        ]);

        $user = $this->getActiveUser();
        
        $conversation = $user->conversations()->findOrFail($request->conversation_id);
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        $message = $user->messages()->create([
            'conversation_id' => $conversation->id,
            'body' => $request->body,
            'attachment' => $attachmentPath,
        ]);

        $conversation->touch();
        
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success', 
            'message' => $message->load('sender')
        ]);
    }

    // ==========================================
    // 4. مسح رسالة
    // ==========================================
    public function deleteMessage(int $message_id)
    {
        $user = $this->getActiveUser();
        $message = $user->messages()->findOrFail($message_id);
        
        $conversation_id = $message->conversation_id;

        if ($message->attachment) {
            Storage::disk('public')->delete($message->attachment);
        }
        
        $message->delete();

        // 🔥 إرسال إشعار للطرف التاني إن الرسالة اتمسحت 🔥
        broadcast(new \App\Events\MessageDeleted($message_id, $conversation_id))->toOthers();

        return response()->json(['status' => 'success', 'msg' => 'تم حذف الرسالة']);
    }

    // ==========================================
    // 5. البحث عن مستخدمين
    // ==========================================
    public function searchUsers(Request $request)
    {
        $term = $request->q;
        if (!$term || strlen($term) < 2) return response()->json([]);

        $results = [];
        $me = $this->getActiveUser();

        $teachers = \App\Models\Teacher::where('is_active', 1)
            ->where(function($q) use ($term) {
                $q->where('name_ar', 'LIKE', "%{$term}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%")
                  ->orWhere('specialization', 'LIKE', "%{$term}%");
            })
            ->take(5)->get();

        foreach($teachers as $t) {
            if (get_class($me) == \App\Models\Teacher::class && $me->id == $t->id) continue;
            $results[] = [
                'id' => $t->id, 
                'type' => \App\Models\Teacher::class, 
                'name' => $t->name_ar, 
                'role' => 'معلم ' . ($t->specialization ? '('.$t->specialization.')' : ''),
                'icon' => 'mdi-school'
            ];
        }

        $admins = \App\Models\User::where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%");
            })
            ->take(5)->get();

        foreach($admins as $a) {
            if (get_class($me) == \App\Models\User::class && $me->id == $a->id) continue;
            $results[] = [
                'id' => $a->id, 
                'type' => \App\Models\User::class, 
                'name' => $a->name, 
                'role' => 'إدارة النظام',
                'icon' => 'mdi-shield-account'
            ];
        }

        $students = \App\Models\Student::where(function($q) use ($term) {
                $q->where('first_name', 'LIKE', "%{$term}%")
                  ->orWhere('second_name', 'LIKE', "%{$term}%")
                  ->orWhere('tribe', 'LIKE', "%{$term}%");
            })
            ->take(5)->get();

        foreach($students as $s) {
            if (get_class($me) == \App\Models\Student::class && $me->id == $s->id) continue;
            $fullName = trim($s->first_name . ' ' . $s->second_name . ' ' . $s->tribe);
            $results[] = [
                'id' => $s->id, 
                'type' => \App\Models\Student::class, 
                'name' => $fullName, 
                'role' => 'طالب',
                'icon' => 'mdi-account-school'
            ];
        }

        $guardians = Guardian::where(function($q) use ($term) {
                $q->where('name_ar', 'LIKE', "%{$term}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%")
                  ->orWhere('national_id', 'LIKE', "%{$term}%");
            })
            ->take(5)->get();

        foreach($guardians as $g) {
            if (get_class($me) == Guardian::class && $me->id == $g->id) continue;
            $results[] = [
                'id' => $g->id, 
                'type' => Guardian::class, 
                'name' => $g->name_ar, 
                'role' => 'ولي أمر',
                'icon' => 'mdi-human-male-child'
            ];
        }

        return response()->json($results);
    }

    // ==========================================
    // 6. بدء محادثة فردية
    // ==========================================
    public function startConversation(Request $request)
    {
        $request->validate([
            'target_id' => 'required',
            'target_type' => 'required'
        ]);

        $me = $this->getActiveUser();
        
        // 🔥 الحل: تنظيف المسار من السلاش المزدوج اللي جاي من الجافاسكريبت 🔥
        $cleanTargetType = str_replace('\\\\', '\\', $request->target_type);

        $existingConv = $me->conversations()->where('type', 'private')
            ->whereHas('participants', function($q) use ($request, $cleanTargetType) {
                $q->where('messageable_id', $request->target_id)
                  ->where('messageable_type', $cleanTargetType);
            })->first();

        if ($existingConv) {
            return response()->json(['status' => 'success', 'conversation_id' => $existingConv->id]);
        }

        $conv = Conversation::create([
            'type' => 'private', 
            'creator_id' => $me->id, 
            'creator_type' => get_class($me)
        ]);
        
        $conv->participants()->create(['messageable_id' => $me->id, 'messageable_type' => get_class($me)]);
        $conv->participants()->create(['messageable_id' => $request->target_id, 'messageable_type' => $cleanTargetType]);

        return response()->json(['status' => 'success', 'conversation_id' => $conv->id]);
    }

    // ==========================================
    // 7. إنشاء محادثة جماعية (الجروب)
    // ==========================================
    public function createGroup(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|min:2|max:100',
            'participants' => 'required|array|min:1',
            'participants.*.id' => 'required',
            'participants.*.type' => 'required'
        ]);

        $me = $this->getActiveUser();

        $conv = \App\Models\Conversation::create([
            'type' => 'group',
            'name' => $request->group_name,
            'creator_id' => $me->id,
            'creator_type' => get_class($me)
        ]);

        $conv->participants()->create([
            'messageable_id' => $me->id, 
            'messageable_type' => get_class($me)
        ]);

        foreach ($request->participants as $participant) {
            // 🔥 الحل: تنظيف المسار من السلاش المزدوج هنا كمان 🔥
            $cleanType = str_replace('\\\\', '\\', $participant['type']);
            
            if ($participant['id'] == $me->id && $cleanType == get_class($me)) {
                continue; 
            }

            $conv->participants()->create([
                'messageable_id' => $participant['id'],
                'messageable_type' => $cleanType
            ]);
        }

        $message = \App\Models\Message::create([
            'conversation_id' => $conv->id,
            'sender_id' => $me->id,
            'sender_type' => get_class($me),
            'body' => "تم إنشاء مجموعة '{$request->group_name}' بنجاح. مرحباً بالجميع!",
            'read_at' => now()
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success', 
            'message' => 'تم إنشاء المجموعة بنجاح',
            'conversation_id' => $conv->id
        ]);
    }

    // ==========================================
    // 🔥 8. جلب بيانات ملف التعارف (Profile Card)
    // ==========================================
    public function getUserProfile(Request $request)
    {
        $id = $request->id;
        
        // 🔥 الحل: تنظيف المسار هنا للاحتياط عشان لو ضغط على صورته يفتح البروفايل صح 🔥
        $type = str_replace('\\\\', '\\', $request->type); 
        
        $data = [
            'name' => 'غير معروف', 
            'role' => 'غير محدد', 
            'details' => 'لا توجد بيانات إضافية', 
            'status' => 'offline'
        ];

        $cacheKey = 'chat_status_' . str_replace('\\', '_', $type) . '_' . $id;
        $data['status'] = Cache::get($cacheKey, 'offline');

        if ($type == 'App\Models\Teacher') {
            $teacher = \App\Models\Teacher::with('branch')->find($id);
            if ($teacher) {
                $data['name'] = $teacher->name_ar;
                $data['role'] = 'هيئة التدريس';
                $data['details'] = 'التخصص: ' . ($teacher->specialization ?? 'غير مسجل') . '<br>' . 
                                   'الفرع: ' . ($teacher->branch->name_ar ?? 'غير محدد') . '<br>' . 
                                   'الهاتف: ' . ($teacher->phone ?? 'لا يوجد');
            }
        } elseif ($type == 'App\Models\Student') {
            $student = \App\Models\Student::with(['grade', 'section', 'branch'])->find($id);
            if ($student) {
                $data['name'] = trim($student->first_name . ' ' . $student->second_name . ' ' . $student->tribe);
                $data['role'] = 'طالب';
                $data['details'] = 'الفرع: ' . ($student->branch->name_ar ?? 'غير محدد') . '<br>' . 
                                   'الصف: ' . ($student->grade->name_ar ?? 'غير مسجل') . '<br>' . 
                                   'الشعبة: ' . ($student->section->name_ar ?? 'غير مسجلة');
            }
        } elseif ($type == 'App\Models\Guardian') {
            $guardian = Guardian::find($id);
            if ($guardian) {
                $data['name'] = $guardian->name_ar;
                $data['role'] = 'ولي أمر';
                $data['details'] = 'الهاتف: ' . ($guardian->phone ?? 'لا يوجد') . '<br>' . 
                                   'الرقم المدني: ' . ($guardian->national_id ?? 'لا يوجد');
            }
        } elseif ($type == 'App\Models\User') {
            $admin = \App\Models\User::find($id);
            if ($admin) {
                $data['name'] = $admin->name;
                $data['role'] = 'إدارة النظام';
                $data['details'] = 'البريد الإلكتروني: ' . $admin->email;
            }
        }

        if ($data['status'] == 'invisible') {
            $data['status'] = 'offline';
        }

        return response()->json($data);
    }

    // ==========================================
    // 🔥 9. تغيير حالة الاتصال (Online / DND / Invisible)
    // ==========================================
    public function updateChatStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,dnd,invisible'
        ]);

        $user = $this->getActiveUser();
        
        $cacheKey = 'chat_status_' . str_replace('\\', '_', get_class($user)) . '_' . $user->id;
        Cache::put($cacheKey, $request->status, now()->addDay());

        return response()->json(['status' => 'success', 'current_status' => $request->status]);
    }
    // ==========================================
    // 10. جلب أعضاء المجموعة
    // ==========================================
    public function getGroupMembers($id)
    {
        $user = $this->getActiveUser();
        // التأكد إن المحادثة جروب وإن المستخدم ده عضو فيها
        $conversation = $user->conversations()->where('type', 'group')->findOrFail($id);
        
        $members = $conversation->participants()->with('messageable')->get()->map(function($p) {
            $m = $p->messageable;
            if(!$m) return null;

            $name = $m->name_ar ?? $m->name ?? trim(($m->first_name ?? '') . ' ' . ($m->second_name ?? '') . ' ' . ($m->tribe ?? ''));
            $role = 'عضو';
            $icon = 'mdi-account';
            $typeClass = get_class($m);
            
            if (str_contains($typeClass, 'Teacher')) { $role = 'معلم'; $icon = 'mdi-school'; }
            elseif (str_contains($typeClass, 'Student')) { $role = 'طالب'; $icon = 'mdi-account-school'; }
            elseif (str_contains($typeClass, 'Guardian')) { $role = 'ولي أمر'; $icon = 'mdi-human-male-child'; }
            elseif (str_contains($typeClass, 'User')) { $role = 'إدارة النظام'; $icon = 'mdi-shield-account'; }

            return [
                'id' => $m->id,
                'type' => str_replace('\\', '\\\\', $typeClass),
                'name' => $name,
                'role' => $role,
                'icon' => $icon,
            ];
        })->filter();

        return response()->json($members);
    }

    // ==========================================
    // 11. إضافة عضو جديد للمجموعة
    // ==========================================
    public function addGroupMember(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'user_type' => 'required'
        ]);

        $user = $this->getActiveUser();
        $conversation = $user->conversations()->where('type', 'group')->findOrFail($id);

        $cleanType = str_replace('\\\\', '\\', $request->user_type);

        // التأكد إن العضو مش موجود أصلاً في الجروب
        $exists = $conversation->participants()
            ->where('messageable_id', $request->user_id)
            ->where('messageable_type', $cleanType)
            ->exists();

        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'هذا الشخص عضو بالفعل في هذه المجموعة'], 400);
        }

        // إضافة العضو
        $conversation->participants()->create([
            'messageable_id' => $request->user_id,
            'messageable_type' => $cleanType
        ]);

        // إرسال رسالة آلية تبلغ الجروب بإنضمام شخص جديد
        $message = $user->messages()->create([
            'conversation_id' => $conversation->id,
            'body' => "قام أحد الأعضاء بإضافة شخص جديد للمجموعة.",
            'read_at' => now()
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json(['status' => 'success', 'message' => 'تم إضافة العضو بنجاح']);
    }
    // ==========================================
    // 12. إزالة عضو من المجموعة
    // ==========================================
    public function removeGroupMember(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'user_type' => 'required'
        ]);

        $user = $this->getActiveUser();
        $conversation = $user->conversations()->where('type', 'group')->findOrFail($id);

        $cleanType = str_replace('\\\\', '\\', $request->user_type);

        // منع الشخص من إزالة نفسه من هذا الزر (تجنباً للأخطاء)
        if ($request->user_id == $user->id && $cleanType == get_class($user)) {
            return response()->json(['status' => 'error', 'message' => 'لا يمكنك إزالة نفسك بهذه الطريقة.'], 400);
        }

        $conversation->participants()
            ->where('messageable_id', $request->user_id)
            ->where('messageable_type', $cleanType)
            ->delete();

        // إرسال رسالة آلية تبلغ الجروب
        $message = $user->messages()->create([
            'conversation_id' => $conversation->id,
            'body' => "قام أحد الأعضاء بإزالة شخص من المجموعة.",
            'read_at' => now()
        ]);
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json(['status' => 'success', 'message' => 'تم إزالة العضو بنجاح']);
    }

    // ==========================================
    // 13. تغيير اسم المجموعة
    // ==========================================
    public function updateGroupName(Request $request, $id)
    {
        $request->validate([
            'group_name' => 'required|string|min:2|max:100'
        ]);

        $user = $this->getActiveUser();
        $conversation = $user->conversations()->where('type', 'group')->findOrFail($id);

        $oldName = $conversation->name;
        $conversation->update(['name' => $request->group_name]);

        // إرسال رسالة آلية تبلغ الجروب بتغيير الاسم
        $message = $user->messages()->create([
            'conversation_id' => $conversation->id,
            'body' => "تم تغيير اسم المجموعة إلى '{$request->group_name}'.",
            'read_at' => now()
        ]);
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json(['status' => 'success', 'message' => 'تم تغيير الاسم بنجاح']);
    }
    public function updateMessage(Request $request, int $message_id)
    {
        $request->validate([
            'body' => 'required|string|max:2000'
        ]);

        $user = $this->getActiveUser();
        $message = $user->messages()->findOrFail($message_id);

        $message->update([
            'body' => $request->body,
            'is_edited' => true // يفضل تضيف الحقل ده في الداتا بيز كـ Boolean
        ]);

        // 🔥 إرسال إشعار للطرف التاني بالتعديل 🔥
        broadcast(new \App\Events\MessageEdited($message))->toOthers();

        return response()->json([
            'status' => 'success', 
            'message' => $message->load('sender')
        ]);
    }
}