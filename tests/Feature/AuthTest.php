<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = $this->createAdmin(['email' => 'admin@test.com']);

        $this->post(route('login.post'), [
            'email'    => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_teacher_login_redirects_to_teacher_dashboard(): void
    {
        $teacher = $this->createTeacher(['email' => 'teacher@test.com']);

        $this->post(route('login.post'), [
            'email'    => $teacher->user->email,
            'password' => 'password',
        ])->assertRedirect(route('teacher.dashboard'));
    }

    public function test_student_login_redirects_to_student_dashboard(): void
    {
        $student = $this->createStudent(null, ['email' => 'student@test.com']);

        $this->post(route('login.post'), [
            'email'    => $student->user->email,
            'password' => 'password',
        ])->assertRedirect(route('student.dashboard'));
    }

    public function test_invalid_credentials_return_validation_error(): void
    {
        User::factory()->create([
            'email'    => 'user@test.com',
            'password' => Hash::make('password'),
        ]);

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email'    => 'user@test.com',
                'password' => 'wrong',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_redirects_to_login(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
