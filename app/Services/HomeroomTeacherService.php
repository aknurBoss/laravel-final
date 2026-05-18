<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Validation\ValidationException;

class HomeroomTeacherService
{
    public function sync(Teacher $teacher, bool $isHomeroom, ?int $classId): void
    {
        $this->releaseTeacherFromClass($teacher);

        $teacher->update(['is_homeroom_teacher' => $isHomeroom]);

        if (! $isHomeroom || $classId === null) {
            return;
        }

        $class = SchoolClass::query()->findOrFail($classId);

        if ($class->homeroom_teacher_id !== null && $class->homeroom_teacher_id !== $teacher->id) {
            throw ValidationException::withMessages([
                'homeroom_class_id' => 'У этого класса уже есть классный руководитель.',
            ]);
        }

        $class->update(['homeroom_teacher_id' => $teacher->id]);
    }

    public function assignClassHomeroom(SchoolClass $class, ?int $teacherId): void
    {
        if ($teacherId === null) {
            if ($class->homeroom_teacher_id) {
                Teacher::query()
                    ->where('id', $class->homeroom_teacher_id)
                    ->update(['is_homeroom_teacher' => false]);
            }
            $class->update(['homeroom_teacher_id' => null]);

            return;
        }

        $teacher = Teacher::query()->findOrFail($teacherId);

        $existingClass = SchoolClass::query()
            ->where('homeroom_teacher_id', $teacher->id)
            ->where('id', '!=', $class->id)
            ->first();

        if ($existingClass) {
            throw ValidationException::withMessages([
                'homeroom_teacher_id' => "Этот учитель уже классный руководитель класса «{$existingClass->name}».",
            ]);
        }

        if ($class->homeroom_teacher_id && $class->homeroom_teacher_id !== $teacher->id) {
            Teacher::query()
                ->where('id', $class->homeroom_teacher_id)
                ->update(['is_homeroom_teacher' => false]);
        }

        $this->releaseTeacherFromClass($teacher);

        $teacher->update(['is_homeroom_teacher' => true]);
        $class->update(['homeroom_teacher_id' => $teacher->id]);
    }

    private function releaseTeacherFromClass(Teacher $teacher): void
    {
        SchoolClass::query()
            ->where('homeroom_teacher_id', $teacher->id)
            ->update(['homeroom_teacher_id' => null]);
    }
}
