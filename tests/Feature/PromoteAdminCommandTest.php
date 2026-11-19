<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoteAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_promotes_an_existing_account_after_explicit_confirmation(): void
    {
        $user = User::factory()->create();

        $this->artisan('arissa:make-admin', ['email' => $user->email, '--force' => true])
            ->assertSuccessful();

        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_command_does_not_promote_an_unknown_email(): void
    {
        $this->artisan('arissa:make-admin', ['email' => 'missing@example.com', '--force' => true])
            ->assertFailed();
    }
}
