<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminRoomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_room_with_a_validated_image(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('admin');
        $category = RoomCategory::query()->create([
            'name' => 'Suite',
            'slug' => 'suite',
        ]);
        $facility = Facility::query()->create(['name' => 'Private balcony']);

        $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'room_category_id' => $category->id,
            'room_number' => '601',
            'name' => 'Courtyard Suite',
            'description' => 'A quiet suite overlooking the courtyard.',
            'price_per_night' => '850.00',
            'max_occupancy' => 3,
            'status' => 'active',
            'image' => UploadedFile::fake()->create('courtyard.jpg', 10, 'image/jpeg'),
            'facilities' => [$facility->id],
        ])->assertRedirect(route('admin.rooms.index'));

        $room = Room::query()->where('room_number', '601')->firstOrFail();
        $this->assertSame('Courtyard Suite', $room->name);
        $this->assertSame($facility->id, $room->facilities()->firstOrFail()->id);
        $this->assertStringStartsWith('rooms/', $room->image_path);
        Storage::disk('public')->assertExists($room->image_path);
    }

    public function test_managers_cannot_access_room_administration(): void
    {
        $manager = $this->userWithRole('manager');

        $this->actingAs($manager)
            ->get(route('admin.rooms.index'))
            ->assertForbidden();
    }

    public function test_room_deactivation_preserves_the_room_record(): void
    {
        $admin = $this->userWithRole('admin');
        $category = RoomCategory::query()->create([
            'name' => 'Deluxe Room',
            'slug' => 'deluxe-room',
        ]);
        $room = Room::query()->create([
            'room_category_id' => $category->id,
            'room_number' => '602',
            'name' => 'Deluxe Balcony',
            'price_per_night' => 500,
            'max_occupancy' => 2,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.rooms.deactivate', $room))
            ->assertRedirect();

        $this->assertSame('inactive', $room->fresh()->status);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }
}
