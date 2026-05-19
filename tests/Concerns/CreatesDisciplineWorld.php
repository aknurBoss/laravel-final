<?php

namespace Tests\Concerns;

use App\Models\DisciplineRule;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait CreatesDisciplineWorld
{
    protected function createAdmin(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'      => 'admin',
            'is_active' => true,
            'password'  => Hash::make('password'),
        ], $attrs));
    }

    protected function createTeacher(array $userAttrs = [], bool $homeroom = false): Teacher
    {
        $user = User::factory()->create(array_merge([
            'role'      => 'teacher',
            'is_active' => true,
            'password'  => Hash::make('password'),
        ], $userAttrs));

        return Teacher::create([
            'user_id'             => $user->id,
            'is_homeroom_teacher' => $homeroom,
        ]);
    }

    protected function createStudent(?SchoolClass $class = null, array $userAttrs = [], ?int $points = null): Student
    {
        $class ??= $this->createClass();

        $user = User::factory()->create(array_merge([
            'role'      => 'student',
            'is_active' => true,
            'password'  => Hash::make('password'),
        ], $userAttrs));

        $data = [
            'user_id'  => $user->id,
            'class_id' => $class->id,
        ];
        if ($points !== null) {
            $data['current_points'] = $points;
        }

        return Student::create($data);
    }

    protected function createClass(string $name = '10А'): SchoolClass
    {
        return SchoolClass::create(['name' => $name]);
    }

    protected function assignHomeroom(Teacher $teacher, SchoolClass $class): void
    {
        $teacher->update(['is_homeroom_teacher' => true]);
        $class->update(['homeroom_teacher_id' => $teacher->id]);
    }

    protected function createRule(int $points, string $type = 'penalty', bool $active = true): DisciplineRule
    {
        return DisciplineRule::create([
            'name'        => 'Test rule '.$points,
            'description' => 'Test',
            'points'      => $points,
            'type'        => $type,
            'is_active'   => $active,
        ]);
    }

    protected function activeSemester(): Semester
    {
        return Semester::query()->active()->firstOrFail();
    }
}
