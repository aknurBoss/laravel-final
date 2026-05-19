<?php

namespace Tests\Feature;

use App\Models\Semester;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class SemesterFeatureTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_admin_can_view_semesters_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.semesters.index'))
            ->assertOk()
            ->assertSee('Активный семестр');
    }

    public function test_admin_can_close_semester(): void
    {
        config(['discipline.starting_points' => 100]);

        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 77);

        $this->actingAs($admin)
            ->post(route('admin.semesters.close'), [
                'closed_name' => 'I полугодие',
                'new_name'    => 'II полугодие',
            ])
            ->assertRedirect(route('admin.semesters.index'))
            ->assertSessionHas('status');

        $this->assertSame(100, $student->fresh()->current_points);
        $this->assertSame(2, Semester::count());
        $this->assertNotNull(Semester::where('name', 'I полугодие')->first()?->closed_at);

        $archived = Semester::where('name', 'I полугодие')->first();
        $this->actingAs($admin)
            ->get(route('admin.semesters.show', $archived))
            ->assertOk()
            ->assertSee('I полугодие');
    }

    public function test_active_semester_show_redirects_to_index(): void
    {
        $admin = $this->createAdmin();
        $active = $this->activeSemester();

        $this->actingAs($admin)
            ->get(route('admin.semesters.show', $active))
            ->assertRedirect(route('admin.semesters.index'));
    }

    public function test_cannot_cancel_history_from_archived_semester(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent(points: 50);
        $rule = $this->createRule(-5, 'penalty');

        $this->actingAs($admin)->post(route('admin.points.assign'), [
            'mode'        => '1',
            'rule_id'     => $rule->id,
            'student_ids' => [$student->id],
        ]);

        $history = \App\Models\PointHistory::first();

        $this->actingAs($admin)->post(route('admin.semesters.close'), [
            'closed_name' => 'Архив',
            'new_name'    => 'Новый',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.points.cancel', $history))
            ->assertForbidden();
    }
}
