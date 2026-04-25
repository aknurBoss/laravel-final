<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::with(['user', 'schoolClass']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->whereHas('user', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $students = $query->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:6'],
            'class_id'       => ['required', 'integer', 'exists:school_classes,id'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => UserRole::STUDENT,
            'is_active' => true,
        ]);

        Student::create([
            'user_id'        => $user->id,
            'class_id'       => $data['class_id'],
            'current_points' => 100,
        ]);

        return redirect()->route('admin.students.index')
            ->with('status', "Ученик «{$user->name}» создан и добавлен в класс.");
    }

    public function edit(Student $student): View
    {
        $student->load(['user', 'schoolClass']);
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', "unique:users,email,{$student->user_id}"],
            'class_id'       => ['required', 'integer', 'exists:school_classes,id'],
            'current_points' => ['nullable', 'integer', 'min:0'],
        ]);

        $student->user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        $student->update([
            'class_id'       => $data['class_id'],
            'current_points' => $data['current_points'] ?? $student->current_points,
        ]);

        return redirect()->route('admin.students.index')->with('status', 'Данные ученика обновлены.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('status', 'Ученик удалён.');
    }
}
