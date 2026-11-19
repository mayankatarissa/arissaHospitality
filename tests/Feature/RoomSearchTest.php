<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Database\Seeders\HotelCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_search_filters_budget_and_approved_overlapping_bookings(): void
    {
        $category = RoomCategory::query()->create([
            'name' => 'Superior Room',
            'slug' => 'superior-room',
        ]);
        $availableRoom = $this->createRoom($category, '201', 'Garden Room', 220);
        $bookedRoom = $this->createRoom($category, '202', 'Ocean Room', 260);
        $overBudgetRoom = $this->createRoom($category, '203', 'Suite Room', 900);
        $user = User::factory()->create();
        $checkIn = now()->addDays(10)->setTime(14, 0);
        $checkOut = $checkIn->copy()->addDays(2)->setTime(11, 0);

        Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $bookedRoom->id,
            'check_in_at' => $checkIn->copy()->addHours(1),
            'check_out_at' => $checkOut->copy()->subHours(1),
            'status' => 'approved',
        ]);
        Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $availableRoom->id,
            'check_in_at' => $checkIn->copy(),
            'check_out_at' => $checkOut->copy(),
            'status' => 'pending',
        ]);

        $this->get(route('rooms.index', [
            'check_in_date' => $checkIn->toDateString(),
            'check_in_time' => $checkIn->format('H:i'),
            'check_out_date' => $checkOut->toDateString(),
            'check_out_time' => $checkOut->format('H:i'),
            'min_budget' => 200,
            'max_budget' => 300,
        ]))
            ->assertOk()
            ->assertSee('Garden Room')
            ->assertDontSee('Ocean Room')
            ->assertDontSee('Suite Room');
    }

    public function test_search_requires_a_complete_valid_stay_period(): void
    {
        $this->get(route('rooms.index', [
            'check_in_time' => '14:00',
        ]))->assertSessionHasErrors('check_in_date');

        $this->get(route('rooms.index', [
            'check_in_date' => now()->addDays(3)->toDateString(),
            'check_in_time' => '14:00',
            'check_out_date' => now()->addDays(2)->toDateString(),
            'check_out_time' => '11:00',
        ]))->assertSessionHasErrors('check_out_date');
    }

    public function test_home_and_room_details_are_public(): void
    {
        $category = RoomCategory::query()->create([
            'name' => 'Executive Room',
            'slug' => 'executive-room',
        ]);
        $room = $this->createRoom($category, '301', 'Executive Corner', 450);

        $this->get(route('home'))->assertOk();
        $this->get(route('rooms.show', $room))->assertOk()->assertSee('Executive Corner');
    }

    public function test_seeded_demo_rooms_and_facilities_render_with_local_images(): void
    {
        $this->seed(HotelCatalogSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('[DEMO] Deluxe King Room')
            ->assertSee('/images/hotel-hero.jpg')
            ->assertSee('/images/rooms/deluxe.jpg');

        $this->get(route('rooms.index'))
            ->assertOk()
            ->assertSee('[DEMO] Presidential Suite')
            ->assertSee('/images/rooms/presidential.jpg');

        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertSee('Swimming Pool')
            ->assertSee('/images/facilities/pool.jpg')
            ->assertSee('/images/facilities/airport.jpg');

        $this->seed(HotelCatalogSeeder::class);
        $this->assertDatabaseCount('rooms', 4);
    }

    private function createRoom(RoomCategory $category, string $number, string $name, int $price): Room
    {
        return Room::query()->create([
            'room_category_id' => $category->id,
            'room_number' => $number,
            'name' => $name,
            'price_per_night' => $price,
            'max_occupancy' => 2,
            'status' => 'active',
        ]);
    }
}
