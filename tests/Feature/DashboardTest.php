<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_admin_dashboard_shows_active_semester(): void
    {
        $admin = $this->createAdmin();
        $semester = $this->activeSemester();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($semester->name);
    }

    public function test_teacher_dashboard_is_accessible(): void
    {
        $teacher = $this->createTeacher();

        $this->actingAs($teacher->user)
            ->get(route('teacher.dashboard'))
            ->assertOk();
    }

    public function test_student_dashboard_is_accessible(): void
    {
        $student = $this->createStudent();

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_teacher_assignments_page(): void
    {
        $teacher = $this->createTeacher();

        $this->actingAs($teacher->user)
            ->get(route('teacher.assignments'))
            ->assertOk();
    }
}
