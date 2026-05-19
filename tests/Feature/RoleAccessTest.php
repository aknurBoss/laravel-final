<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_routes(): void
    {
        $student = $this->createStudent();

        $this->actingAs($student->user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_teacher_cannot_access_admin_routes(): void
    {
        $teacher = $this->createTeacher();

        $this->actingAs($teacher->user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_teacher_dashboard(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_student_cannot_access_teacher_dashboard(): void
    {
        $student = $this->createStudent();

        $this->actingAs($student->user)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }
}
