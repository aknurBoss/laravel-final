<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class HomeroomTeacherTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_homeroom_teacher_can_view_my_class(): void
    {
        $class = $this->createClass();
        $teacher = $this->createTeacher(homeroom: true);
        $this->assignHomeroom($teacher, $class);
        $this->createStudent($class);

        $this->actingAs($teacher->user)
            ->get(route('teacher.homeroom'))
            ->assertOk();
    }

    public function test_regular_teacher_cannot_access_homeroom(): void
    {
        $teacher = $this->createTeacher(homeroom: false);

        $this->actingAs($teacher->user)
            ->get(route('teacher.homeroom'))
            ->assertForbidden();
    }

    public function test_homeroom_teacher_can_view_student_in_class(): void
    {
        $class = $this->createClass();
        $teacher = $this->createTeacher(homeroom: true);
        $this->assignHomeroom($teacher, $class);
        $student = $this->createStudent($class);

        $this->actingAs($teacher->user)
            ->get(route('teacher.homeroom.student', $student))
            ->assertOk();
    }

    public function test_homeroom_teacher_cannot_view_student_from_other_class(): void
    {
        $class = $this->createClass('10А');
        $otherClass = $this->createClass('9Б');
        $teacher = $this->createTeacher(homeroom: true);
        $this->assignHomeroom($teacher, $class);
        $otherStudent = $this->createStudent($otherClass);

        $this->actingAs($teacher->user)
            ->get(route('teacher.homeroom.student', $otherStudent))
            ->assertForbidden();
    }
}
