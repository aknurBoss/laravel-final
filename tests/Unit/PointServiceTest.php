<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\PointHistory;
use App\Models\Student;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class PointServiceTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_assign_points_updates_balance_and_history(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(3, 'reward');
        $semesterId = $this->activeSemester()->id;

        app(PointService::class)->assignPoints(
            studentIds: [$student->id],
            ruleIds: [$rule->id],
            comment: 'Отличная работа',
            actorUser: $admin,
            actorRole: 'admin',
        );

        $student->refresh();
        $this->assertSame(103, $student->current_points);

        $this->assertDatabaseHas('point_histories', [
            'student_id'     => $student->id,
            'rule_id'        => $rule->id,
            'teacher_id'     => $admin->id,
            'semester_id'    => $semesterId,
            'points'         => 3,
            'balance_before' => 100,
            'balance_after'  => 103,
        ]);
    }

    public function test_penalty_below_threshold_creates_low_balance_notifications(): void
    {
        config(['discipline.low_points_threshold' => 21]);

        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 25);
        $studentUser = $student->user;
        $rule = $this->createRule(-10, 'penalty');

        app(PointService::class)->assignPoints(
            studentIds: [$student->id],
            ruleIds: [$rule->id],
            comment: '',
            actorUser: $admin,
            actorRole: 'admin',
        );

        $this->assertSame(15, $student->fresh()->current_points);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $studentUser->id,
            'type'    => 'low_points_warning',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'type'    => 'student_low_points',
        ]);
    }

    public function test_reward_does_not_trigger_low_balance_notification(): void
    {
        config(['discipline.low_points_threshold' => 21]);

        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 15);
        $rule = $this->createRule(2, 'reward');

        app(PointService::class)->assignPoints(
            studentIds: [$student->id],
            ruleIds: [$rule->id],
            comment: '',
            actorUser: $admin,
            actorRole: 'admin',
        );

        $this->assertSame(0, Notification::where('type', 'low_points_warning')->count());
    }

    public function test_inactive_rules_are_ignored(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(-5, 'penalty', active: false);

        app(PointService::class)->assignPoints(
            studentIds: [$student->id],
            ruleIds: [$rule->id],
            comment: '',
            actorUser: $admin,
            actorRole: 'admin',
        );

        $this->assertSame(100, $student->fresh()->current_points);
        $this->assertSame(0, PointHistory::count());
    }
}
