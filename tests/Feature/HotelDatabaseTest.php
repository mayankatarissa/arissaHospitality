<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_booking_and_facility_relationships_are_persisted(): void
    {
        $user = User::factory()->create();
        $category = RoomCategory::query()->create([
            'name' => 'Deluxe Room',
            'slug' => 'deluxe-room',
            'default_occupancy' => 2,
        ]);
        $facility = Facility::query()->create(['name' => 'Free Wi-Fi']);
        $room = Room::query()->create([
            'room_category_id' => $category->id,
            'room_number' => '101',
            'name' => 'Deluxe Garden View',
            'price_per_night' => 180.00,
            'max_occupancy' => 2,
        ]);
        $room->facilities()->attach($facility);
        $booking = Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_at' => '2027-01-10 14:00:00',
            'check_out_at' => '2027-01-12 11:00:00',
        ]);

        $this->assertSame('user', $user->fresh()->role);
        $this->assertSame($room->id, $category->rooms()->firstOrFail()->id);
        $this->assertSame('Free Wi-Fi', $room->fresh()->facilities()->firstOrFail()->name);
        $this->assertSame($user->id, $booking->fresh()->user->id);
        $this->assertSame($room->id, $user->bookings()->firstOrFail()->room->id);
    }
}
