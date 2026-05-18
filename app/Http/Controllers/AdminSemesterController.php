<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Semester;
use App\Services\SemesterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSemesterController extends Controller
{
    public function __construct(
        private SemesterService $semesterService,
    ) {}

    public function index(): View
    {
        $activeSemester = $this->semesterService->active();
        $archivedSemesters = Semester::query()->archived()->with('closedBy')->get();

        $activeStats = [
            'students' => \App\Models\Student::count(),
            'records'  => PointHistory::query()->forSemester($activeSemester->id)->count(),
            'merits'   => (int) PointHistory::query()->forSemester($activeSemester->id)->where('points', '>', 0)->sum('points'),
            'demerits' => (int) abs(PointHistory::query()->forSemester($activeSemester->id)->where('points', '<', 0)->sum('points')),
        ];

        return view('admin.semesters.index', compact('activeSemester', 'archivedSemesters', 'activeStats'));
    }

    public function show(Semester $semester): View
    {
        if ($semester->isActive()) {
            return redirect()->route('admin.semesters.index');
        }

        $semester->load('closedBy');

        $snapshots = $semester->studentSnapshots()
            ->orderBy('global_rank')
            ->limit(50)
            ->get();

        $latestHistory = PointHistory::query()
            ->forSemester($semester->id)
            ->with(['student.user', 'student.schoolClass', 'teacher', 'rule'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('admin.semesters.show', compact('semester', 'snapshots', 'latestHistory'));
    }

    public function close(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'closed_name' => ['required', 'string', 'max:120'],
            'new_name'    => ['required', 'string', 'max:120'],
        ]);

        $active = $this->semesterService->active();

        $result = $this->semesterService->closeAndStartNew(
            $data['closed_name'],
            $data['new_name'],
            $request->user(),
        );

        return redirect()
            ->route('admin.semesters.index')
            ->with('status', sprintf(
                'Семестр «%s» архивирован. Открыт новый семестр «%s». Баллы всех учеников сброшены до %d.',
                $result['closed']->name,
                $result['new']->name,
                $result['starting_points'],
            ));
    }
}
