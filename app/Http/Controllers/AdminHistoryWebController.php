<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

class AdminHistoryWebController extends Controller
{
    public function student(Student $student): View
    {
        $history = PointHistory::query()
            ->inActiveSemester()
            ->with(['teacher', 'rule', 'student.schoolClass'])
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('admin.history.student_view', compact('student', 'history'));
    }

    public function teacher(User $user): View
    {
        $history = PointHistory::query()
            ->inActiveSemester()
            ->with(['student.user', 'student.schoolClass', 'rule'])
            ->where('teacher_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('admin.history.teacher_view', compact('user', 'history'));
    }
}
