<?php

namespace App\Http\Controllers;

use App\Models\DisciplineRule;
use App\Models\PointHistory;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPointController extends Controller
{
    public function __construct(
        private PointService $pointService,
    ) {}

    public function index(Request $request): View
    {
        // Для режима 1: все ученики, сгруппированные по классам
        $classes = SchoolClass::with(['students.user'])->orderBy('name')->get();

        // Для режима 2: все ученики в flat-списке
        $allStudents = Student::with(['user', 'schoolClass'])->orderBy('id')->get();

        // Все активные правила
        $rules = DisciplineRule::where('is_active', true)->orderByDesc('points')->get();

        // История назначений с фильтрами
        $historyQuery = PointHistory::query()
            ->inActiveSemester()
            ->with(['student.user', 'student.schoolClass', 'teacher', 'rule']);

        if ($request->filled('hf')) {
            $historyQuery->whereDate('created_at', '>=', $request->input('hf'));
        }
        if ($request->filled('ht')) {
            $historyQuery->whereDate('created_at', '<=', $request->input('ht'));
        }
        if ($request->filled('htype')) {
            $historyQuery->whereHas('rule', fn ($q) => $q->where('type', $request->input('htype')));
        }
        if ($request->filled('hclass')) {
            $historyQuery->whereHas('student', fn ($q) => $q->where('class_id', $request->input('hclass')));
        }

        $history = $historyQuery->orderByDesc('created_at')->limit(100)->get();

        $mode = $request->query('mode', '1');

        return view('admin.points.index', compact('classes', 'allStudents', 'rules', 'history', 'mode'));
    }

    public function assign(Request $request): RedirectResponse
    {
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
                actorUser:  auth()->user(),
                actorRole:  'admin',
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
                actorUser:  auth()->user(),
                actorRole:  'admin',
            );
        }

        return redirect()
            ->route('admin.points', ['mode' => $mode])
            ->with('status', 'Баллы успешно назначены.');
    }

    public function cancel(PointHistory $history): RedirectResponse
    {
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