<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RestaurantMenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_catalog_entries_and_menu_type_is_server_controlled(): void
    {
        $admin = $this->userWithRole('admin');
        $this->actingAs($admin)
            ->get(route('admin.catalog.create', 'categories'))
            ->assertOk();

        $this->actingAs($admin)->post(route('admin.catalog.store', 'facilities'), [
            'name' => 'Indoor Pool',
            'description' => 'A temperature-controlled indoor pool.',
            'operating_hours' => '06:00–22:00',
            'is_active' => 1,
        ])->assertRedirect(route('admin.catalog.index', 'facilities'));

        $this->actingAs($admin)->post(route('admin.catalog.store', 'food'), [
            'name' => 'Seasonal Risotto',
            'description' => 'A seasonal kitchen selection.',
            'category' => 'Main Course',
            'price' => '28.50',
            'is_available' => 1,
            'menu_type' => 'bar',
        ])->assertRedirect(route('admin.catalog.index', 'food'));

        $this->assertDatabaseHas('facilities', ['name' => 'Indoor Pool']);
        $this->assertDatabaseHas('restaurant_menu_items', [
            'name' => 'Seasonal Risotto',
            'menu_type' => 'food',
        ]);
    }

    public function test_non_admin_cannot_manage_catalog_and_used_categories_cannot_be_deleted(): void
    {
        $manager = $this->userWithRole('manager');

        $this->actingAs($manager)
            ->get(route('admin.catalog.index', 'facilities'))
            ->assertForbidden();

        $admin = $this->userWithRole('admin');
        $category = RoomCategory::query()->create([
            'name' => 'Superior',
            'slug' => 'superior',
        ]);
        Room::query()->create([
            'room_category_id' => $category->id,
            'room_number' => '701',
            'name' => 'Superior One',
            'price_per_night' => 200,
            'max_occupancy' => 2,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.catalog.destroy', ['categories', $category->id]))
            ->assertSessionHasErrors('record');

        $this->assertDatabaseHas('room_categories', ['id' => $category->id]);
    }

    public function test_unknown_catalog_type_is_not_exposed(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.catalog.index', 'users'))
            ->assertNotFound();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }
}
