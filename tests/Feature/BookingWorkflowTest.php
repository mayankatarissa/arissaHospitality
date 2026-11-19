<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use App\Notifications\BookingUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_a_request_and_management_is_notified(): void
    {
        $guest = User::factory()->create();
        $this->assertSame('user', $guest->role);
        $this->assertTrue($guest->is_active);
        $this->actingAs($guest)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('id="mainNavigation"', false)
            ->assertDontSee('auth-card', false)
            ->assertSee('dropdown-menu-end account-menu', false)
            ->assertSee('Search rooms');
        $manager = $this->createUserWithRole('manager');
        $room = $this->createRoom();
        [$checkIn, $checkOut] = $this->stayPeriod();

        $this->actingAs($guest)->post(route('bookings.store', $room), [
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'special_requests' => 'Late arrival',
        ])->assertRedirect();

        $booking = Booking::query()->firstOrFail();
        $this->assertSame('pending', $booking->status);
        $this->assertSame('Late arrival', $booking->special_requests);
        $this->assertSame('pending', $booking->statusHistories()->firstOrFail()->new_status);
        $notification = $manager->notifications()->firstOrFail();
        $this->assertSame('A new room booking request needs review.', $notification->data['message']);
        $this->assertSame(route('management.bookings.show', $booking), $notification->data['url']);
    }

    public function test_approval_is_rechecked_and_prevents_overlapping_approvals(): void
    {
        Notification::fake();
        $admin = $this->createUserWithRole('admin');
        $guest = User::factory()->create();
        $room = $this->createRoom();
        [$checkIn, $checkOut] = $this->stayPeriod();
        $first = $this->createPendingBooking($guest, $room, $checkIn, $checkOut);
        $second = $this->createPendingBooking(
            $guest,
            $room,
            $checkIn->copy()->addHours(1),
            $checkOut->copy()->subHours(1),
        );

        $this->actingAs($admin)
            ->post(route('management.bookings.approve', $first))
            ->assertRedirect();

        $this->assertSame('approved', $first->fresh()->status);
        $this->assertSame('pending', $first->fresh()->statusHistories()->latest()->firstOrFail()->old_status);
        Notification::assertSentTo($guest, BookingUpdated::class);

        $this->from(route('management.bookings.show', $second))
            ->post(route('management.bookings.approve', $second))
            ->assertSessionHasErrors('booking');

        $this->assertSame('pending', $second->fresh()->status);
    }

    public function test_users_cannot_cancel_other_users_bookings_but_can_cancel_their_own(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $manager = $this->createUserWithRole('manager');
        $room = $this->createRoom();
        [$checkIn, $checkOut] = $this->stayPeriod();
        $booking = $this->createPendingBooking($owner, $room, $checkIn, $checkOut);

        $this->actingAs($otherUser)
            ->post(route('bookings.cancel', $booking))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('bookings.cancel', $booking))
            ->assertRedirect();

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame('pending', $booking->fresh()->statusHistories()->latest()->firstOrFail()->old_status);
        Notification::assertSentTo($manager, BookingUpdated::class);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }

    private function createRoom(): Room
    {
        $category = RoomCategory::query()->create([
            'name' => 'Premium Room',
            'slug' => 'premium-room',
        ]);

        return Room::query()->create([
            'room_category_id' => $category->id,
            'room_number' => '501',
            'name' => 'Premium Garden Room',
            'price_per_night' => 500,
            'max_occupancy' => 2,
            'status' => 'active',
        ]);
    }

    private function createPendingBooking(User $user, Room $room, $checkIn, $checkOut): Booking
    {
        return Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'status' => 'pending',
        ]);
    }

    private function stayPeriod(): array
    {
        $checkIn = now()->addDays(30)->setTime(14, 0);

        return [$checkIn, $checkIn->copy()->addDays(2)->setTime(11, 0)];
    }
}
