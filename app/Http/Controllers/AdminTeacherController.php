<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;
use App\Services\HomeroomTeacherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTeacherController extends Controller
{
    public function __construct(
        private HomeroomTeacherService $homeroomService,
    ) {}

    public function index(Request $request): View
    {
        $query = Teacher::with(['user', 'homeroomClass']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->whereHas('user', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $teachers = $query->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        $classes = $this->classesAvailableForHomeroom();

        return view('admin.teachers.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'string', 'min:6'],
            'is_homeroom_teacher' => ['sometimes', 'boolean'],
            'homeroom_class_id' => [
                'nullable',
                'integer',
                Rule::exists('school_classes', 'id'),
                Rule::requiredIf($request->boolean('is_homeroom_teacher')),
            ],
        ]);

        $isHomeroom = $request->boolean('is_homeroom_teacher');

        DB::transaction(function () use ($data, $isHomeroom, $request): void {
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'role'      => UserRole::TEACHER,
                'is_active' => true,
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'is_homeroom_teacher' => false,
            ]);

            $this->homeroomService->sync(
                $teacher,
                $isHomeroom,
                $isHomeroom ? (int) $request->input('homeroom_class_id') : null,
            );
        });

        return redirect()->route('admin.teachers.index')
            ->with('status', "Учитель «{$data['name']}» создан.");
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load(['user', 'homeroomClass']);
        $classes = $this->classesAvailableForHomeroom($teacher->id);

        return view('admin.teachers.edit', compact('teacher', 'classes'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', "unique:users,email,{$teacher->user_id}"],
            'password'          => ['nullable', 'string', 'min:6'],
            'is_homeroom_teacher' => ['sometimes', 'boolean'],
            'homeroom_class_id' => [
                'nullable',
                'integer',
                Rule::exists('school_classes', 'id'),
                Rule::requiredIf($request->boolean('is_homeroom_teacher')),
            ],
        ]);

        $isHomeroom = $request->boolean('is_homeroom_teacher');

        DB::transaction(function () use ($data, $teacher, $isHomeroom, $request): void {
            $userUpdate = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $userUpdate['password'] = Hash::make($data['password']);
            }

            $teacher->user->update($userUpdate);

            $this->homeroomService->sync(
                $teacher,
                $isHomeroom,
                $isHomeroom ? (int) $request->input('homeroom_class_id') : null,
            );
        });

        return redirect()->route('admin.teachers.index')->with('status', 'Данные учителя обновлены.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($teacher): void {
            $this->homeroomService->sync($teacher, false, null);

            $user = $teacher->user;
            $teacher->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.teachers.index')->with('status', 'Учитель удалён.');
    }

    /** @return \Illuminate\Support\Collection<int, SchoolClass> */
    private function classesAvailableForHomeroom(?int $exceptTeacherId = null)
    {
        return SchoolClass::query()
            ->orderBy('name')
            ->with('homeroomTeacher.user')
            ->get()
            ->filter(function (SchoolClass $class) use ($exceptTeacherId) {
                if ($class->homeroom_teacher_id === null) {
                    return true;
                }

                return $exceptTeacherId !== null && $class->homeroom_teacher_id === $exceptTeacherId;
            });
    }
}
