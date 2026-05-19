<?php

namespace Tests\Feature;

use App\Models\PointHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class AdminPointsTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_admin_can_view_points_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.points'))
            ->assertOk();
    }

    public function test_admin_can_assign_points_mode_one(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(2, 'reward');

        $this->actingAs($admin)
            ->post(route('admin.points.assign'), [
                'mode'        => '1',
                'rule_id'     => $rule->id,
                'student_ids' => [$student->id],
                'comment'     => 'Хорошо',
            ])
            ->assertRedirect(route('admin.points', ['mode' => '1']))
            ->assertSessionHas('status');

        $this->assertSame(102, $student->fresh()->current_points);
        $this->assertSame(1, PointHistory::count());
    }

    public function test_admin_can_assign_points_mode_two(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 100);
        $rule1 = $this->createRule(2, 'reward');
        $rule2 = $this->createRule(1, 'reward');

        $this->actingAs($admin)
            ->post(route('admin.points.assign'), [
                'mode'       => '2',
                'student_id' => $student->id,
                'rule_ids'   => [$rule1->id, $rule2->id],
            ])
            ->assertRedirect(route('admin.points', ['mode' => '2']));

        $this->assertSame(103, $student->fresh()->current_points);
        $this->assertSame(2, PointHistory::count());
    }

    public function test_admin_can_cancel_assignment_in_active_semester(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 100);
        $rule = $this->createRule(5, 'reward');

        $this->actingAs($admin)->post(route('admin.points.assign'), [
            'mode'        => '1',
            'rule_id'     => $rule->id,
            'student_ids' => [$student->id],
        ]);

        $history = PointHistory::first();
        $this->assertSame(105, $student->fresh()->current_points);

        $this->actingAs($admin)
            ->post(route('admin.points.cancel', $history))
            ->assertRedirect();

        $this->assertSame(100, $student->fresh()->current_points);
        $this->assertSoftDeleted($history);
    }
}
