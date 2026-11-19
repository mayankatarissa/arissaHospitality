<?php

namespace Tests\Feature;

use App\Models\ContactQuery;
use App\Models\EventEnquiry;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DemoReviewSeeder;
use Database\Seeders\HotelCatalogSeeder;
use Database\Seeders\HotelContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalityPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_informational_hospitality_pages_render(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('favicon.svg')
            ->assertSee('.navbar .dropdown:hover > .dropdown-menu', false);

        foreach ([
            'facilities.index',
            'restaurant.food',
            'restaurant.bar',
            'about',
            'resort',
            'events.index',
            'reviews.index',
            'contact',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_seeded_demo_food_and_bar_menus_render_with_images(): void
    {
        $this->seed(HotelCatalogSeeder::class);

        $this->get(route('restaurant.food'))
            ->assertOk()
            ->assertSee('[DEMO] Garden Harvest Salad')
            ->assertSee('/images/menu/garden-salad.jpg')
            ->assertSee('sample prices are shown');

        $this->get(route('restaurant.bar'))
            ->assertOk()
            ->assertSee('[DEMO] Citrus Garden Spritz')
            ->assertSee('/images/menu/spritz.jpg');

        $this->seed(HotelCatalogSeeder::class);
        $this->assertDatabaseCount('restaurant_menu_items', 7);
    }

    public function test_demo_reviews_and_sample_about_contact_details_are_displayed(): void
    {
        $this->seed(HotelContentSeeder::class);
        $this->seed(DemoReviewSeeder::class);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('123 Example Avenue, Sample City, Country')
            ->assertSee('+1 202-555-0147')
            ->assertSee('hello@example.com')
            ->assertSee('mailto:hello@example.com');

        $this->get(route('reviews.index'))
            ->assertOk()
            ->assertSee('[DEMO] A restful, welcoming stay')
            ->assertSee('Sample Guest One (Demo)')
            ->assertSee('not feedback from an actual guest');

        $this->seed(DemoReviewSeeder::class);
        $this->assertDatabaseCount('reviews', 3);
    }

    public function test_contact_and_event_enquiries_are_validated_and_saved(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Morgan Guest',
            'email' => 'morgan@example.com',
            'subject' => 'Arrival details',
            'message' => 'Please share the check-in time.',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('contact_queries', ['email' => 'morgan@example.com', 'status' => 'pending']);

        $this->post(route('events.enquiry'), [
            'name' => 'Morgan Guest',
            'email' => 'morgan@example.com',
            'event_type' => 'Wedding',
            'event_date' => now()->addMonths(3)->toDateString(),
            'guest_count' => 80,
            'message' => 'Please send venue details.',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('event_enquiries', ['email' => 'morgan@example.com', 'guest_count' => 80]);
    }

    public function test_guest_review_is_pending_moderation_and_duplicate_submission_is_blocked(): void
    {
        $user = User::factory()->create();
        $payload = [
            'rating' => 5,
            'title' => 'A restful visit',
            'description' => 'The team made our stay comfortable.',
        ];

        $this->actingAs($user)->post(route('reviews.store'), $payload)
            ->assertRedirect(route('reviews.index'));

        $this->assertSame('pending', Review::query()->firstOrFail()->status);

        $this->actingAs($user)->from(route('reviews.create'))
            ->post(route('reviews.store'), $payload)
            ->assertSessionHasErrors('user_id');
    }
}
