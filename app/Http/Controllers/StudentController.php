<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        /** @var Student|null $student */
        $student = Student::with('schoolClass')
            ->where('user_id', $user->id)
            ->firstOrFail();

        $studentsWithMorePoints = Student::where('current_points', '>', $student->current_points)->count();
        $rank = $studentsWithMorePoints + 1;

        return response()->json([
            'id' => $student->id,
            'user_id' => $student->user_id,
            'name' => $user->name,
            'current_points' => $student->current_points,
            'school_class' => $student->schoolClass ? [
                'id' => $student->schoolClass->id,
                'name' => $student->schoolClass->name,
            ] : null,
            'rank' => $rank,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $perPage = (int) $request->query('size', 20);

        $history = PointHistory::with(['teacher', 'rule'])
            ->where('student_id', $student->id)
            ->inActiveSemester()
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $items = $history->getCollection()->map(function (PointHistory $record) {
            return [
                'id' => $record->id,
                'teacher_name' => $record->teacher?->name ?? 'Admin',
                'rule_description' => $record->rule?->description,
                'points_changed' => $record->points,
                'comment' => $record->comment,
                'created_at' => $record->created_at,
            ];
        });

        $history->setCollection($items);

        return response()->json($history);
    }
}
