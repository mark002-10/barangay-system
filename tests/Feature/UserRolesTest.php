<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_user_role_only_has_home_and_account_settings(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->get(route('dashboard'))->assertOk()->assertSee('Latest community announcement')->assertDontSee('Total residents');
        $this->get(route('settings.edit'))->assertOk();
        $this->get(route('residents.index'))->assertForbidden();
        $this->get(route('reports.index'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_staff_can_use_office_modules_but_cannot_manage_accounts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));

        $this->get(route('residents.index'))->assertOk();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_can_create_and_change_account_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('admin.users.store'), [
            'name' => 'Portal Resident',
            'email' => 'resident@example.com',
            'role' => 'user',
            'password' => 'A-Long-Secret-123',
            'password_confirmation' => 'A-Long-Secret-123',
        ])->assertRedirect(route('admin.users.index'));

        $account = User::where('email', 'resident@example.com')->firstOrFail();
        $this->assertSame('user', $account->role);

        $this->put(route('admin.users.update', $account), [
            'name' => 'Portal Resident',
            'email' => 'resident@example.com',
            'role' => 'staff',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('staff', $account->fresh()->role);
    }

    public function test_admin_cannot_remove_own_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'staff',
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }
}

