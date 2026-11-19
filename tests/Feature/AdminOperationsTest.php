<?php

namespace Tests\Feature;

use App\Models\ContactQuery;
use App\Models\EventEnquiry;
use App\Models\Review;
use App\Models\StaffDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class AdminOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_manager_but_managers_cannot_manage_admin_accounts(): void
    {
        Notification::fake();
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.managers.store'), [
            'name' => 'Casey Manager',
            'email' => 'casey.manager@example.com',
            'phone' => '555-0188',
        ])->assertRedirect();

        $manager = User::query()->where('email', 'casey.manager@example.com')->firstOrFail();
        $this->assertSame('manager', $manager->role);
        Notification::assertSentTo($manager, ResetPassword::class);
        $this->actingAs($admin)->get(route('admin.managers.index'))->assertOk();

        $this->actingAs($manager)
            ->get(route('admin.managers.index'))
            ->assertForbidden();
        $this->get(route('admin.administrators.index'))->assertForbidden();
    }

    public function test_admin_can_create_another_admin_but_cannot_deactivate_their_own_account(): void
    {
        Notification::fake();
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.administrators.store'), [
            'name' => 'Robin Administrator',
            'email' => 'robin.admin@example.com',
        ])->assertRedirect();

        $newAdmin = User::query()->where('email', 'robin.admin@example.com')->firstOrFail();
        $this->assertSame('admin', $newAdmin->role);
        Notification::assertSentTo($newAdmin, ResetPassword::class);

        $this->patch(route('admin.administrators.status', $admin), ['is_active' => 0])
            ->assertSessionHasErrors('administrator');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_and_manager_dashboards_and_management_pages_render(): void
    {
        $admin = $this->userWithRole('admin');
        $this->actingAs($admin);

        foreach ([
            'admin.dashboard',
            'admin.users.index',
            'admin.managers.index',
            'admin.administrators.index',
            'admin.reviews.index',
            'admin.queries.index',
            'admin.event-enquiries.index',
            'admin.content.edit',
            'admin.catalog.index',
        ] as $route) {
            $parameters = $route === 'admin.catalog.index' ? ['type' => 'venues'] : [];
            $this->get(route($route, $parameters))->assertOk();
        }

        $manager = $this->userWithRole('manager');
        $this->actingAs($manager)->get(route('manager.dashboard'))->assertOk();
        $this->get(route('management.staff.create'))->assertOk();
    }

    public function test_manager_staff_records_are_scoped_and_manager_cannot_delete_them(): void
    {
        $managerA = $this->userWithRole('manager');
        $managerB = $this->userWithRole('manager');
        $staff = StaffDetail::query()->create([
            'manager_id' => $managerA->id,
            'created_by' => $managerA->id,
            'name' => 'Alex Staff',
            'job_title' => 'Concierge',
            'status' => 'active',
        ]);

        $this->actingAs($managerB)
            ->get(route('management.staff.edit', $staff))
            ->assertNotFound();

        $this->actingAs($managerB)->post(route('management.staff.store'), [
            'name' => 'Sam Staff',
            'job_title' => 'Receptionist',
            'status' => 'active',
            'manager_id' => $managerA->id,
        ])->assertRedirect(route('management.staff.index'));

        $created = StaffDetail::query()->where('name', 'Sam Staff')->firstOrFail();
        $this->assertSame($managerB->id, $created->manager_id);

        $this->actingAs($managerB)
            ->delete(route('management.staff.destroy', $created))
            ->assertForbidden();
    }

    public function test_admin_can_moderate_reviews_resolve_enquiries_and_edit_content(): void
    {
        $admin = $this->userWithRole('admin');
        $guest = User::factory()->create();
        $review = Review::query()->create([
            'user_id' => $guest->id,
            'rating' => 5,
            'title' => 'A lovely stay',
            'description' => 'The service was wonderful.',
        ]);
        $query = ContactQuery::query()->create([
            'name' => 'Jordan Guest',
            'email' => 'jordan@example.com',
            'subject' => 'Arrival',
            'message' => 'What time is check-in?',
        ]);
        $event = EventEnquiry::query()->create([
            'name' => 'Jordan Guest',
            'email' => 'jordan@example.com',
            'message' => 'We are planning a reception.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.content.edit'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.reviews.status', $review), ['status' => 'approved'])
            ->assertRedirect();
        $this->assertSame('approved', $review->fresh()->status);

        $this->patch(route('admin.queries.status', $query), ['status' => 'resolved'])
            ->assertRedirect();
        $this->assertNotNull($query->fresh()->resolved_at);

        $this->patch(route('admin.event-enquiries.status', $event), ['status' => 'resolved'])
            ->assertRedirect();
        $this->assertNotNull($event->fresh()->resolved_at);

        $this->put(route('admin.content.update'), [
            'about' => [
                'title' => 'Our renewed welcome',
                'body' => 'Thoughtful stays for every guest.',
                'mission' => 'Care in every detail.',
                'vision' => 'A destination guests return to.',
                'contact_address' => '10 New Example Street, Sample City',
                'contact_phone' => '+1 202-555-0199',
                'contact_email' => 'contact@example.com',
            ],
            'resort' => [
                'title' => 'A quieter escape',
                'body' => 'Time to pause and restore.',
                'experiences' => 'Wellness, dining and time outdoors.',
            ],
        ])->assertRedirect();

        $about = \App\Models\HotelSetting::query()->where('key', 'about')->firstOrFail()->value;
        $this->assertSame('Our renewed welcome', $about['title']);
        $this->assertSame('contact@example.com', $about['contact_email']);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }
}
