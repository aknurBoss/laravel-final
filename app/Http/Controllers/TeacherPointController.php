<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Student;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherPointController extends Controller
{
    public function __construct(
        private PointService $pointService,
    ) {}

    public function assignments(Request $request): View
    {
        $user = $request->user() ?? auth()->user();

        $historyQuery = PointHistory::query()
            ->inActiveSemester()
            ->with(['student.user', 'student.schoolClass', 'rule'])
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

        $history = $historyQuery->orderByDesc('created_at')->limit(100)->get();

        return view('teacher.assignments.index', compact('history'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $mode = $request->input('mode', '1');

        if ($mode === '1') {
            // Режим 1: одно правило → много учеников (из разных классов)
            $data = $request->validate([
                'rule_id'       => ['required', 'integer', 'exists:discipline_rules,id'],
                'student_ids'   => ['required', 'array', 'min:1'],
                'student_ids.*' => ['integer', 'exists:students,id'],
                'comment'       => ['nullable', 'string', 'max:500'],
            ]);

            $this->pointService->assignPoints(
                studentIds: $data['student_ids'],
                ruleIds:    [$data['rule_id']],
                comment:    (string) ($data['comment'] ?? ''),
                actorUser:  $user,
                actorRole:  'teacher',
            );
        } else {
            // Режим 2: один ученик → много правил
            $data = $request->validate([
                'student_id'    => ['required', 'integer', 'exists:students,id'],
                'rule_ids'      => ['required', 'array', 'min:1'],
                'rule_ids.*'    => ['integer', 'exists:discipline_rules,id'],
                'comment'       => ['nullable', 'string', 'max:500'],
            ]);

            $this->pointService->assignPoints(
                studentIds: [$data['student_id']],
                ruleIds:    $data['rule_ids'],
                comment:    (string) ($data['comment'] ?? ''),
                actorUser:  $user,
                actorRole:  'teacher',
            );
        }

        return redirect()
            ->route('teacher.assignments')
            ->with('status', 'Баллы успешно назначены.');
    }

    public function cancel(Request $request, PointHistory $history): RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Учитель может отменять только свои назначения
        if ($history->teacher_id !== $user->id) {
            abort(403);
        }

        if ($history->semester_id !== app(\App\Services\SemesterService::class)->activeSemesterId()) {
            abort(403, 'Нельзя отменить запись из архивного семестра.');
        }

        DB::transaction(function () use ($history) {
            $history->load('student');
            if ($history->student) {
                $history->student->current_points -= $history->points;
                $history->student->save();
            }
            $history->delete();
        });

        return back()->with('status', 'Назначение отменено, баллы откатаны.');
    }
}