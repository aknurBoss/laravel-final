<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Teacher::with('user');

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
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'subject'  => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => UserRole::TEACHER,
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'subject' => $data['subject'] ?? null,
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('status', "Учитель «{$user->name}» создан.");
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', "unique:users,email,{$teacher->user_id}"],
            'password' => ['nullable', 'string', 'min:6'],
            'subject'  => ['nullable', 'string', 'max:255'],
        ]);

        $userUpdate = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $userUpdate['password'] = Hash::make($data['password']);
        }

        $teacher->user->update($userUpdate);
        $teacher->update(['subject' => $data['subject'] ?? null]);

        return redirect()->route('admin.teachers.index')->with('status', 'Данные учителя обновлены.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('status', 'Учитель удалён.');
    }
}
