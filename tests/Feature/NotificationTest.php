<?php

namespace Tests\Feature;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDisciplineWorld;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use CreatesDisciplineWorld;
    use RefreshDatabase;

    public function test_user_can_view_notifications(): void
    {
        $student = $this->createStudent();

        Notification::create([
            'user_id' => $student->user_id,
            'type'    => 'test',
            'title'   => 'Тест',
            'body'    => 'Тело',
            'is_read' => false,
        ]);

        $this->actingAs($student->user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Тест');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $student = $this->createStudent();

        $notification = Notification::create([
            'user_id' => $student->user_id,
            'type'    => 'test',
            'title'   => 'Тест',
            'body'    => 'Тело',
            'is_read' => false,
        ]);

        $this->actingAs($student->user)
            ->post(route('notifications.read', $notification))
            ->assertRedirect();

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_user_cannot_mark_others_notification(): void
    {
        $student1 = $this->createStudent(null, ['email' => 's1@test.com']);
        $student2 = $this->createStudent(null, ['email' => 's2@test.com']);

        $notification = Notification::create([
            'user_id' => $student1->user_id,
            'type'    => 'test',
            'title'   => 'Чужое',
            'body'    => 'Тело',
            'is_read' => false,
        ]);

        $this->actingAs($student2->user)
            ->post(route('notifications.read', $notification))
            ->assertForbidden();
    }
}
