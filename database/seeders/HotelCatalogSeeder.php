<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\RestaurantMenuItem;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Deluxe Room',
            'Superior Room',
            'Executive Room',
            'Premium Room',
            'Suite',
            'Presidential Suite',
        ] as $name) {
            RoomCategory::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            );
        }

        foreach ([
            ['Swimming Pool', 'A peaceful pool area for a refreshing break during your stay.', 'images/facilities/pool.jpg'],
            ['Fitness Centre', 'A dedicated space to keep up your workout routine while travelling.', 'images/facilities/fitness.jpg'],
            ['Spa and Wellness', 'A calm setting for restorative treatments and moments of relaxation.', 'images/facilities/spa.jpg'],
            ['Free Wi-Fi', 'Stay connected in guest areas with convenient wireless internet access.', 'images/facilities/wifi.jpg'],
            ['Parking', 'Convenient on-site parking for guests arriving by car.', 'images/facilities/parking.jpg'],
            ['Room Service', 'Enjoy dining in the comfort and privacy of your room.', 'images/facilities/room-service.jpg'],
            ['Conference Facilities', 'Flexible meeting spaces for business gatherings and presentations.', 'images/facilities/conference.jpg'],
            ['Laundry Services', 'Laundry assistance to help make longer stays more comfortable.', 'images/facilities/laundry.jpg'],
            ['Concierge', 'Guest assistance for local recommendations and stay arrangements.', 'images/facilities/concierge.jpg'],
            ['Airport Transfers', 'Transfer assistance for convenient journeys to and from the airport.', 'images/facilities/airport.jpg'],
        ] as [$name, $description, $imagePath]) {
            $facility = Facility::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'image_path' => $imagePath, 'is_active' => true],
            );

            if (! $facility->description || ! $facility->image_path) {
                $facility->update([
                    'description' => $facility->description ?: $description,
                    'image_path' => $facility->image_path ?: $imagePath,
                ]);
            }
        }

        foreach ([
            [
                'room_number' => 'DEMO-101',
                'category' => 'deluxe-room',
                'name' => '[DEMO] Deluxe King Room',
                'description' => 'DEMO LISTING: A sample king room with a restful, contemporary feel. Replace these details and the sample rate with verified hotel information before accepting bookings.',
                'price_per_night' => 180,
                'max_occupancy' => 2,
                'image_path' => 'images/rooms/deluxe.jpg',
                'facilities' => ['Free Wi-Fi', 'Room Service'],
            ],
            [
                'room_number' => 'DEMO-202',
                'category' => 'superior-room',
                'name' => '[DEMO] Superior Twin Room',
                'description' => 'DEMO LISTING: A sample twin room suited to a comfortable shared stay. Replace these details and the sample rate with verified hotel information before accepting bookings.',
                'price_per_night' => 220,
                'max_occupancy' => 2,
                'image_path' => 'images/rooms/superior.jpg',
                'facilities' => ['Free Wi-Fi', 'Room Service'],
            ],
            [
                'room_number' => 'DEMO-301',
                'category' => 'executive-room',
                'name' => '[DEMO] Executive Corner Suite',
                'description' => 'DEMO LISTING: An illustrative executive suite with extra space to unwind or work. Replace these details and the sample rate with verified hotel information before accepting bookings.',
                'price_per_night' => 320,
                'max_occupancy' => 3,
                'image_path' => 'images/rooms/executive.jpg',
                'facilities' => ['Free Wi-Fi', 'Room Service', 'Concierge'],
            ],
            [
                'room_number' => 'DEMO-401',
                'category' => 'presidential-suite',
                'name' => '[DEMO] Presidential Suite',
                'description' => 'DEMO LISTING: A sample premium suite for guests seeking a more spacious stay. Replace these details and the sample rate with verified hotel information before accepting bookings.',
                'price_per_night' => 520,
                'max_occupancy' => 4,
                'image_path' => 'images/rooms/presidential.jpg',
                'facilities' => ['Free Wi-Fi', 'Room Service', 'Concierge'],
            ],
        ] as $roomData) {
            $facilityNames = $roomData['facilities'];
            unset($roomData['facilities']);
            $categoryId = RoomCategory::query()->where('slug', $roomData['category'])->value('id');
            unset($roomData['category']);

            $roomData['room_category_id'] = $categoryId;
            $roomData['status'] = 'active';
            $room = Room::query()->firstOrCreate(
                ['room_number' => $roomData['room_number']],
                $roomData,
            );

            $facilityIds = Facility::query()->whereIn('name', $facilityNames)->pluck('id');
            $room->facilities()->syncWithoutDetaching($facilityIds);
        }

        foreach ([
            'food' => [
                [
                    'name' => '[DEMO] Garden Harvest Salad',
                    'description' => 'DEMO MENU ITEM: A fresh seasonal salad with greens, vegetables, and a light house dressing. Verify ingredients and allergens before serving.',
                    'category' => 'Starters',
                    'price' => 12.50,
                    'image_path' => 'images/menu/garden-salad.jpg',
                ],
                [
                    'name' => '[DEMO] Herb-Roasted Chicken',
                    'description' => 'DEMO MENU ITEM: A sample roasted chicken dish served with seasonal accompaniments. Verify ingredients and allergens before serving.',
                    'category' => 'Mains',
                    'price' => 24.00,
                    'image_path' => 'images/menu/main-course.jpg',
                ],
                [
                    'name' => '[DEMO] Wild Mushroom Risotto',
                    'description' => 'DEMO MENU ITEM: A creamy mushroom and herb risotto from the sample menu. Verify ingredients and allergens before serving.',
                    'category' => 'Mains',
                    'price' => 22.00,
                    'image_path' => 'images/menu/mushroom-dish.jpg',
                ],
                [
                    'name' => '[DEMO] Seasonal Dessert',
                    'description' => 'DEMO MENU ITEM: A sample dessert selection to complete the meal. Verify ingredients and allergens before serving.',
                    'category' => 'Desserts',
                    'price' => 10.00,
                    'image_path' => 'images/menu/dessert.jpg',
                ],
            ],
            'bar' => [
                [
                    'name' => '[DEMO] Citrus Garden Spritz',
                    'description' => 'DEMO MENU ITEM: A bright citrus-inspired sample drink. Verify ingredients and alcohol content before serving.',
                    'category' => 'Cocktails',
                    'price' => 15.00,
                    'image_path' => 'images/menu/spritz.jpg',
                ],
                [
                    'name' => '[DEMO] Berry Cooler',
                    'description' => 'DEMO MENU ITEM: A refreshing berry-inspired sample cooler. Verify ingredients before serving.',
                    'category' => 'Zero-proof drinks',
                    'price' => 9.00,
                    'image_path' => 'images/menu/mocktail.jpg',
                ],
                [
                    'name' => '[DEMO] Classic House Cocktail',
                    'description' => 'DEMO MENU ITEM: A sample classic cocktail selection. Verify ingredients and alcohol content before serving.',
                    'category' => 'Cocktails',
                    'price' => 16.00,
                    'image_path' => 'images/menu/spritz.jpg',
                ],
            ],
        ] as $type => $menuItems) {
            foreach ($menuItems as $menuItem) {
                RestaurantMenuItem::query()->firstOrCreate(
                    ['menu_type' => $type, 'name' => $menuItem['name']],
                    $menuItem + ['is_available' => true],
                );
            }
        }
    }
}
