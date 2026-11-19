<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_their_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\BookingUpdated',
            'data' => ['message' => 'Booking updated'],
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = $owner->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\BookingUpdated',
            'data' => ['message' => 'Private booking update'],
        ]);

        $this->actingAs($otherUser)
            ->post(route('notifications.read', $notification))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_opening_a_notification_marks_it_read_and_changes_the_badge_to_gray(): void
    {
        $user = User::factory()->create();
        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\BookingUpdated',
            'data' => ['message' => 'Booking updated', 'url' => route('rooms.index')],
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertSee('text-bg-danger');

        $this->post(route('notifications.open', $notification))
            ->assertRedirect(route('rooms.index'));

        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('home'))
            ->assertSee('text-bg-secondary')
            ->assertDontSee('text-bg-danger');
    }

    public function test_user_cannot_open_another_users_notification(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = $owner->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\BookingUpdated',
            'data' => ['message' => 'Private booking update', 'url' => route('rooms.index')],
        ]);

        $this->actingAs($otherUser)
            ->post(route('notifications.open', $notification))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }
}
