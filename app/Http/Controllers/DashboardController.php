<?php

namespace App\Http\Controllers;

use App\Models\DisciplineRule;
use App\Models\PointHistory;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();

        $allHistory = PointHistory::all();
        $sumPositivePoints = $allHistory->where('points', '>', 0)->sum('points');
        $sumNegativePoints = (int) abs($allHistory->where('points', '<', 0)->sum('points'));
        $totalAssignments = $allHistory->count();

        $latestHistory = PointHistory::with(['student.user', 'student.schoolClass', 'teacher', 'rule'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'sumPositivePoints',
            'sumNegativePoints',
            'totalAssignments',
            'latestHistory',
        ));
    }

    public function teacher(Request $request): View
    {
        $user = $request->user() ?? auth()->user();

        // Режим вкладки: '1' = правило→ученики, '2' = ученик→правила
        $mode = $request->query('mode', '1');

        // Для режима 1: классы с учениками
        $classes = SchoolClass::with(['students.user'])->orderBy('name')->get();

        // Для режима 2: все ученики flat-список
        $allStudents = Student::with(['user', 'schoolClass'])->orderBy('id')->get();

        $rules = DisciplineRule::where('is_active', true)->orderByDesc('points')->get();

        // История назначений этого учителя
        $historyQuery = PointHistory::with(['student.user', 'student.schoolClass', 'rule'])
            ->where('teacher_id', $user?->id);

        if ($request->filled('hf')) {
            $historyQuery->whereDate('created_at', '>=', $request->input('hf'));
        }
        if ($request->filled('ht')) {
            $historyQuery->whereDate('created_at', '<=', $request->input('ht'));
        }
        if ($request->filled('htype')) {
            $historyQuery->whereHas('rule', fn ($q) => $q->where('type', $request->input('htype')));
        }

        $history = $historyQuery->orderByDesc('created_at')->limit(50)->get();

        $teacherProfile = Teacher::with('homeroomClass')
            ->where('user_id', $user?->id)
            ->first();

        return view('teacher.dashboard', compact(
            'classes',
            'allStudents',
            'rules',
            'history',
            'mode',
            'teacherProfile',
        ));
    }

    public function student(Request $request): View
    {
        $user = $request->user() ?? auth()->user();

        $student = Student::with('schoolClass')
            ->where('user_id', $user?->id)
            ->firstOrFail();

        // глобальный ранг по школе
        $studentsWithMorePoints = Student::where('current_points', '>', $student->current_points)->count();
        $globalRank = $studentsWithMorePoints + 1;

        // ранг в классе
        $classRank = null;
        if ($student->class_id) {
            $classRank = Student::where('class_id', $student->class_id)
                    ->where('current_points', '>', $student->current_points)
                    ->count() + 1;
        }

        // история конкретного ученика
        $history = PointHistory::with(['teacher', 'rule'])
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // топ‑10 по школе
        $topSchool = Student::with(['user', 'schoolClass'])
            ->orderByDesc('current_points')
            ->orderBy('id')
            ->limit(10)
            ->get();

        // топ‑10 по классу
        $topClass = collect();
        if ($student->class_id) {
            $topClass = Student::with(['user', 'schoolClass'])
                ->where('class_id', $student->class_id)
                ->orderByDesc('current_points')
                ->orderBy('id')
                ->limit(10)
                ->get();
        }

        return view('student.dashboard', [
            'student' => $student,
            'globalRank' => $globalRank,
            'classRank' => $classRank,
            'history' => $history,
            'topSchool' => $topSchool,
            'topClass' => $topClass,
        ]);
    }
}
