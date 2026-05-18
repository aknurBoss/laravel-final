<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeacherHomeroomController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $this->resolveHomeroomTeacher($request);

        $homeroomClass = $teacher->homeroomClass()
            ->with(['students' => fn ($q) => $q->with('user')->orderBy('current_points', 'desc')])
            ->first();

        if (! $homeroomClass) {
            throw new NotFoundHttpException('Класс не назначен. Обратитесь к администратору.');
        }

        return view('teacher.homeroom.index', compact('teacher', 'homeroomClass'));
    }

    public function studentHistory(Request $request, Student $student): View
    {
        $teacher = $this->resolveHomeroomTeacher($request);

        if (! $teacher->managesStudent($student)) {
            throw new AccessDeniedHttpException('Нет доступа к этому ученику.');
        }

        $student->load(['user', 'schoolClass']);

        $history = PointHistory::query()
            ->inActiveSemester()
            ->with(['teacher', 'rule'])
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('teacher.homeroom.student_history', compact('teacher', 'student', 'history'));
    }

    private function resolveHomeroomTeacher(Request $request): Teacher
    {
        $user = $request->user();

        $teacher = Teacher::query()
            ->with('homeroomClass')
            ->where('user_id', $user?->id)
            ->first();

        if (! $teacher || ! $teacher->is_homeroom_teacher) {
            throw new AccessDeniedHttpException('Доступ только для классного руководителя.');
        }

        return $teacher;
    }
}
