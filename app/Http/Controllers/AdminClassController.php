<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Services\HomeroomTeacherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminClassController extends Controller
{
    public function __construct(
        private HomeroomTeacherService $homeroomService,
    ) {}

    public function index(): View
    {
        $classes = SchoolClass::with(['homeroomTeacher.user'])
            ->withCount('students')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $teachers = Teacher::with(['user', 'homeroomClass'])->orderBy('id')->get();

        return view('admin.classes.create', compact('teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:school_classes,name'],
            'homeroom_teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $class = SchoolClass::create(['name' => $data['name']]);

            if ($request->filled('homeroom_teacher_id')) {
                $this->homeroomService->assignClassHomeroom($class, (int) $data['homeroom_teacher_id']);
            }
        });

        return redirect()->route('admin.classes.index')->with('status', "Класс «{$data['name']}» создан.");
    }

    public function edit(SchoolClass $class): View
    {
        $class->load('homeroomTeacher.user');
        $teachers = Teacher::with(['user', 'homeroomClass'])->orderBy('id')->get();

        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', "unique:school_classes,name,{$class->id}"],
            'homeroom_teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
        ]);

        DB::transaction(function () use ($data, $class, $request): void {
            $class->update(['name' => $data['name']]);

            $teacherId = $request->filled('homeroom_teacher_id')
                ? (int) $data['homeroom_teacher_id']
                : null;

            $this->homeroomService->assignClassHomeroom($class, $teacherId);
        });

        return redirect()->route('admin.classes.index')->with('status', "Класс обновлён → «{$data['name']}».");
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.classes.index')->with('status', 'Класс удалён.');
    }
}
