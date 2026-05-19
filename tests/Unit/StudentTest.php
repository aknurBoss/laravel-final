<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_starting_points_comes_from_config(): void
    {
        config(['discipline.starting_points' => 100]);

        $this->assertSame(100, Student::startingPoints());
    }

    public function test_new_student_gets_default_starting_points(): void
    {
        config(['discipline.starting_points' => 100]);

        $class = $this->createClass();
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::create(['user_id' => $user->id, 'class_id' => $class->id]);

        $this->assertSame(100, $student->fresh()->current_points);
    }
}
