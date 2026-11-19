<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_always_creates_a_guest_role_user(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Taylor Guest',
            'email' => 'taylor@example.com',
            'phone' => '555-0100',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'role' => 'admin',
        ]);

        $user = User::query()->where('email', 'taylor@example.com')->firstOrFail();

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('user', $user->role);
        $this->assertTrue(Hash::check('StrongPassword123!', $user->password));
    }

    public function test_dashboard_redirects_by_database_role_and_blocks_other_roles(): void
    {
        $manager = User::factory()->create();
        $manager->role = 'manager';
        $manager->save();

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertRedirect(route('manager.dashboard'));

        $this->actingAs($manager)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_inactive_accounts_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'StrongPassword123!',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'StrongPassword123!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_password_reset_link_uses_laravel_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_profile_updates_do_not_allow_role_changes_and_password_updates_require_current_password(): void
    {
        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        $this->actingAs($admin)->put(route('profile.update'), [
            'name' => 'Updated Administrator',
            'email' => $admin->email,
            'phone' => '555-0123',
            'role' => 'user',
        ])->assertRedirect();

        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertSame('555-0123', $admin->fresh()->phone);

        $this->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'NewStrongPassword123!',
            'password_confirmation' => 'NewStrongPassword123!',
        ])->assertSessionHas('password-status');

        $this->assertTrue(Hash::check('NewStrongPassword123!', $admin->fresh()->password));
    }
}
