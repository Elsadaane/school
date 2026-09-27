<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AcademicTerm;
use App\Models\StudentMark;
use App\Models\ExamCategory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function printCertificate($student_id, $term_id)
    {
        $student = Student::with(['grade', 'section', 'branch'])->findOrFail($student_id);
        $term = AcademicTerm::findOrFail($term_id);

        // 1. تجميع درجات الطالب في هذا الفصل الدراسي (مع جلب المادة لتجنب الاستعلام داخل اللوب)
        $marks = StudentMark::with('subject')
            ->where('student_id', $student->id)
            ->whereHas('category', function($q) use ($term_id) {
                $q->where('academic_term_id', $term_id);
            })
            ->get()
            ->groupBy('subject_id');

        // 🔥 التحسين: جلب النهايات العظمى لكل المواد الخاصة بهذا الصف والترم مرة واحدة 🔥
        $examCategories = ExamCategory::where('academic_term_id', $term_id)
            ->where('grade_id', $student->grade_id)
            ->get()
            ->groupBy('subject_id');

        $reportCard = [];
        $totalObtained = 0;
        $totalMax = 0;
        $highestSubject = ['name' => '', 'percent' => 0];
        $lowestSubject = ['name' => '', 'percent' => 100];

        // 2. تحليل درجات كل مادة
        foreach ($marks as $subject_id => $subjectMarks) {
            // سحب المادة من الـ Eager Loading بدون استعلام جديد
            $subject = $subjectMarks->first()->subject; 
            $subjectTotalObtained = $subjectMarks->sum('mark_obtained');
            
            // حساب النهاية العظمى للمادة من المجموعة المحملة مسبقاً
            $subjectMax = isset($examCategories[$subject_id]) ? $examCategories[$subject_id]->sum('max_mark') : 0;

            if ($subjectMax > 0) {
                $percent = ($subjectTotalObtained / $subjectMax) * 100;
                
                $reportCard[] = [
                    'subject' => $subject->name_ar,
                    'obtained' => $subjectTotalObtained,
                    'max' => $subjectMax,
                    'percent' => round($percent, 1),
                    'grade_letter' => $this->getGradeLetter($percent) // إضافة التقدير اللفظي
                ];

                $totalObtained += $subjectTotalObtained;
                $totalMax += $subjectMax;

                // رصد أقوى وأضعف مادة للذكاء الاصطناعي
                if ($percent > $highestSubject['percent']) {
                    $highestSubject = ['name' => $subject->name_ar, 'percent' => $percent];
                }
                if ($percent < $lowestSubject['percent']) {
                    $lowestSubject = ['name' => $subject->name_ar, 'percent' => $percent];
                }
            }
        }

        // 3. المحلل الأكاديمي الذكي (Smart Advisor) 🤖
        $smartAdvice = $this->generateSmartAdvice($totalObtained, $totalMax, $highestSubject, $lowestSubject);

        // 4. توليد كود الـ QR للتحقق (حجم مناسب للطباعة)
        $verifyUrl = url('/verify-certificate/' . $student->id . '/' . $term->id);
        $qrCode = QrCode::size(90)->margin(1)->generate($verifyUrl);

        return view('backend.certificates.print', compact(
            'student', 'term', 'reportCard', 'totalObtained', 'totalMax', 'smartAdvice', 'qrCode'
        ));
    }

    // دالة مساعدة لتحديد التقدير اللفظي
    private function getGradeLetter($percent)
    {
        if ($percent >= 90) return 'ممتاز';
        if ($percent >= 80) return 'جيد جداً';
        if ($percent >= 70) return 'جيد';
        if ($percent >= 60) return 'مقبول';
        return 'ضعيف';
    }

    // 🔥 اللوجيك الخاص بتحليل مستوى الطالب 🔥
    private function generateSmartAdvice($obtained, $max, $highest, $lowest)
    {
        if ($max == 0) return "لا توجد درجات مرصودة كافية للتحليل.";

        $totalPercent = ($obtained / $max) * 100;
        $advice = "يظهر الطالب مستوى ";

        if ($totalPercent >= 90) {
            $advice .= "ممتازاً واستيعاباً عالياً للمقررات. ";
        } elseif ($totalPercent >= 75) {
            $advice .= "جيداً جداً، مع إمكانية تحقيق المزيد من التفوق. ";
        } elseif ($totalPercent >= 60) {
            $advice .= "متوسطاً، ويحتاج إلى متابعة مستمرة لتحسين الأداء. ";
        } else {
            $advice .= "ضعيفاً في بعض المهارات الأساسية، ونوصي بوضع خطة علاجية فورية. ";
        }

        if ($highest['name'] != '') {
            $advice .= "يتميز الطالب بشكل خاص في مادة ({$highest['name']})، ";
        }
        if ($lowest['name'] != '' && $lowest['percent'] < 70) {
            $advice .= "بينما نوصي بزيادة الجهد والتركيز المنزلي في مادة ({$lowest['name']}) للوصول إلى التميز الشامل.";
        }

        return $advice;
    }
}