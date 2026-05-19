<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class TeacherModelTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_homeroom_teacher_manages_own_class_students(): void
    {
        $class = $this->createClass();
        $teacher = $this->createTeacher(homeroom: true);
        $this->assignHomeroom($teacher, $class);

        $student = $this->createStudent($class);
        $otherClass = $this->createClass('9Б');
        $otherStudent = $this->createStudent($otherClass);

        $this->assertTrue($teacher->fresh()->managesStudent($student));
        $this->assertFalse($teacher->fresh()->managesStudent($otherStudent));
    }

    public function test_regular_teacher_does_not_manage_class(): void
    {
        $class = $this->createClass();
        $teacher = $this->createTeacher(homeroom: false);
        $student = $this->createStudent($class);

        $this->assertFalse($teacher->managesStudent($student));
    }
}
