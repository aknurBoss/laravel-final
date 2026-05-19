<?php

namespace Tests\Feature;

use App\Models\PointHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class TeacherPointsTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_teacher_can_assign_points(): void
    {
        $teacher = $this->createTeacher();
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(-1, 'penalty');

        $this->actingAs($teacher->user)
            ->post(route('teacher.assign'), [
                'mode'        => '1',
                'rule_id'     => $rule->id,
                'student_ids' => [$student->id],
            ])
            ->assertRedirect(route('teacher.assignments'));

        $this->assertSame(99, $student->fresh()->current_points);

        $history = PointHistory::first();
        $this->assertSame($teacher->user->id, $history->teacher_id);
    }

    public function test_teacher_can_cancel_own_assignment(): void
    {
        $teacher = $this->createTeacher();
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(-2, 'penalty');

        $this->actingAs($teacher->user)->post(route('teacher.assign'), [
            'mode'        => '1',
            'rule_id'     => $rule->id,
            'student_ids' => [$student->id],
        ]);

        $history = PointHistory::first();

        $this->actingAs($teacher->user)
            ->post(route('teacher.history.cancel', $history))
            ->assertRedirect();

        $this->assertSame(100, $student->fresh()->current_points);
    }

    public function test_teacher_cannot_cancel_another_teachers_assignment(): void
    {
        $teacher1 = $this->createTeacher(['email' => 't1@test.com']);
        $teacher2 = $this->createTeacher(['email' => 't2@test.com']);
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(-1, 'penalty');

        $this->actingAs($teacher1->user)->post(route('teacher.assign'), [
            'mode'        => '1',
            'rule_id'     => $rule->id,
            'student_ids' => [$student->id],
        ]);

        $history = PointHistory::first();

        $this->actingAs($teacher2->user)
            ->post(route('teacher.history.cancel', $history))
            ->assertForbidden();
    }
}
