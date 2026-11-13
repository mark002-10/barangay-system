<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_account_and_hashes_password(): void
    {
        $this->artisan('user:create')
            ->expectsQuestion('Full name', 'Barangay Clerk')
            ->expectsQuestion('Email address', 'clerk@example.com')
            ->expectsQuestion('Role', 'staff')
            ->expectsQuestion('Password', 'A-Long-Secret-123')
            ->expectsQuestion('Confirm password', 'A-Long-Secret-123')
            ->expectsOutputToContain('Login account created with role: staff.')
            ->assertExitCode(0);

        $user = User::where('email', 'clerk@example.com')->firstOrFail();

        $this->assertSame('Barangay Clerk', $user->name);
        $this->assertSame('staff', $user->role);
        $this->assertTrue(Hash::check('A-Long-Secret-123', $user->password));
        $this->assertStringNotContainsString('A-Long-Secret-123', $user->password);
    }

    public function test_command_can_create_first_admin_account(): void
    {
        $this->artisan('user:create')
            ->expectsQuestion('Full name', 'System Admin')
            ->expectsQuestion('Email address', 'admin@example.com')
            ->expectsQuestion('Role', 'admin')
            ->expectsQuestion('Password', 'A-Long-Secret-123')
            ->expectsQuestion('Confirm password', 'A-Long-Secret-123')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'role' => 'admin']);
    }

    public function test_command_rejects_short_or_mismatched_passwords(): void
    {
        $this->artisan('user:create')
            ->expectsQuestion('Full name', 'Barangay Clerk')
            ->expectsQuestion('Email address', 'clerk@example.com')
            ->expectsQuestion('Role', 'staff')
            ->expectsQuestion('Password', 'short')
            ->expectsQuestion('Confirm password', 'different')
            ->expectsOutputToContain('The password field must be at least 12 characters.')
            ->expectsOutputToContain('The password field confirmation does not match.')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }
}
