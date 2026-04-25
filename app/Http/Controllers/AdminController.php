<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $totalStudents = Student::count();
        $totalTeachers = User::where('role', 'teacher')->count();

        $allHistory = PointHistory::all();

        $sumPositivePoints = $allHistory->where('points', '>', 0)->sum('points');
        $sumNegativePoints = (int) abs($allHistory->where('points', '<', 0)->sum('points'));
        $totalAssignments = $allHistory->count();

        return response()->json([
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'sum_positive_points' => $sumPositivePoints,
            'sum_negative_points' => $sumNegativePoints,
            'total_assignments' => $totalAssignments,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('size', 50);

        $history = PointHistory::with(['student.schoolClass', 'teacher', 'rule'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($history);
    }

    public function historyShow(int $id, Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $record = PointHistory::with(['student.schoolClass', 'teacher', 'rule'])
            ->find($id);

        if (! $record) {
            return response()->json(['message' => 'History record not found'], 404);
        }

        $student = $record->student;

        return response()->json([
            'id' => $record->id,
            'student_name' => $student?->user?->name,
            'student_class' => $student?->schoolClass?->name ?? 'Без класса',
            'teacher_name' => $record->teacher?->name ?? 'Admin',
            'rule_description' => $record->rule?->description,
            'points_changed' => $record->points,
            'comment' => $record->comment,
            'created_at' => $record->created_at,
        ]);
    }

    public function historyByStudent(int $studentId, Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('size', 20);

        $history = PointHistory::with(['student.schoolClass', 'teacher', 'rule'])
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($history);
    }

    public function historyByTeacher(int $teacherId, Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('size', 20);

        $history = PointHistory::with(['student.schoolClass', 'teacher', 'rule'])
            ->where('teacher_id', $teacherId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($history);
    }

    public function historyByRule(int $ruleId, Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('size', 20);

        $history = PointHistory::with(['student.schoolClass', 'teacher', 'rule'])
            ->where('rule_id', $ruleId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($history);
    }

    public function historyDelete(int $id, Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $record = PointHistory::with('student')->find($id);

        if (! $record) {
            return response()->json(['message' => 'History record not found'], 404);
        }

        $student = $record->student;

        if ($student) {
            $student->current_points -= $record->points;
            $student->save();
        }

        $record->delete();

        return response()->json(null, 204);
    }

    public function teacherStats(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $teachers = User::where('role', 'teacher')->get();

        $stats = $teachers->map(function (User $teacher) {
            $history = PointHistory::where('teacher_id', $teacher->id)->get();

            $positiveAssignments = $history->where('points', '>', 0)->count();
            $negativeAssignments = $history->where('points', '<', 0)->count();
            $totalStudentsAffected = $history->pluck('student_id')->unique()->count();

            return [
                'teacher_id' => $teacher->id,
                'name' => $teacher->name,
                'positive_assignments' => $positiveAssignments,
                'negative_assignments' => $negativeAssignments,
                'total_students_affected' => $totalStudentsAffected,
            ];
        });

        return response()->json($stats->values());
    }

    public function ranking(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('size', 50);

        $students = Student::with(['user', 'schoolClass'])
            ->orderByDesc('current_points')
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json($students);
    }
}
