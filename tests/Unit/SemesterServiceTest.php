<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\PointHistory;
use App\Models\Semester;
use App\Models\SemesterStudentSnapshot;
use App\Models\Student;
use App\Services\SemesterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class SemesterServiceTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_active_returns_semester_from_migration(): void
    {
        $service = app(SemesterService::class);

        $semester = $service->active();

        $this->assertNull($semester->closed_at);
        $this->assertSame($semester->id, $service->activeSemesterId());
    }

    public function test_close_and_start_new_archives_and_resets_students(): void
    {
        config(['discipline.starting_points' => 100]);

        $admin = $this->createAdmin();
        $class = $this->createClass();
        $student = $this->createStudent($class, points: 85);
        $rule = $this->createRule(-5, 'penalty');
        $semester = $this->activeSemester();

        PointHistory::create([
            'semester_id'    => $semester->id,
            'student_id'     => $student->id,
            'rule_id'        => $rule->id,
            'teacher_id'     => $admin->id,
            'points'         => -5,
            'balance_before' => 90,
            'balance_after'  => 85,
            'comment'        => 'test',
            'occurred_at'    => now()->toDateString(),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'type'    => 'test',
            'title'   => 'Test',
            'body'    => 'Test',
        ]);

        $result = app(SemesterService::class)->closeAndStartNew(
            'I семестр 2025',
            'II семестр 2025',
            $admin,
        );

        $this->assertNotNull($result['closed']->closed_at);
        $this->assertSame('I семестр 2025', $result['closed']->name);
        $this->assertSame(1, $result['closed']->students_count);
        $this->assertSame(1, $result['closed']->records_count);
        $this->assertNull($result['new']->closed_at);

        $this->assertSame(100, $student->fresh()->current_points);
        $this->assertDatabaseCount('notifications', 0);
        $this->assertDatabaseHas('semester_student_snapshots', [
            'semester_id'  => $result['closed']->id,
            'student_id'   => $student->id,
            'final_points' => 85,
            'global_rank'  => 1,
        ]);

        $this->assertSame(2, Semester::count());
        $this->assertSame(1, Semester::query()->active()->count());
    }
}
