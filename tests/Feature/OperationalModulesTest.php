<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OperationalModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_resident_can_be_created_and_listed_by_staff(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));

        $this->post(route('residents.store'), [
            'first_name' => 'Rosa', 'middle_name' => 'M', 'last_name' => 'Santos',
            'purok' => 'Purok 1', 'address' => 'Estanza', 'phone' => '09170000000',
        ])->assertRedirect(route('residents.index'));

        $resident = Resident::firstOrFail();
        $this->assertSame('Rosa M Santos', $resident->full_name);
        $this->get(route('residents.index'))->assertOk()->assertSee('Rosa M Santos');
        $this->get(route('residents.show', $resident))->assertOk()->assertSee('Resident record details');
    }

    public function test_staff_can_record_and_process_a_certificate_request(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $resident = Resident::create(['first_name' => 'Ana', 'last_name' => 'Reyes']);
        $this->actingAs($user);

        $this->post(route('requests.store'), [
            'resident_id' => $resident->id, 'request_type' => 'Barangay Clearance', 'purpose' => 'Employment',
        ])->assertRedirect(route('requests.index'));

        $request = ServiceRequest::firstOrFail();
        $this->patch(route('requests.status', $request), ['status' => 'approved'])->assertRedirect();
        $this->patch(route('requests.status', $request), ['status' => 'issued'])->assertRedirect();

        $this->assertDatabaseHas('service_requests', [
            'id' => $request->id, 'status' => 'issued', 'processed_by' => $user->id,
        ]);
        $this->get(route('certificates.index'))->assertOk()->assertSee('Barangay Clearance')->assertSee('Ana Reyes');
    }

    public function test_certificate_cannot_be_issued_before_approval(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        $resident = Resident::create(['first_name' => 'Ana', 'last_name' => 'Reyes']);
        $request = ServiceRequest::create([
            'resident_id' => $resident->id, 'request_type' => 'Certificate of Residency',
            'purpose' => 'School enrollment',
        ]);

        $this->from(route('requests.index'))
            ->patch(route('requests.status', $request), ['status' => 'issued'])
            ->assertRedirect(route('requests.index'))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('service_requests', ['id' => $request->id, 'status' => 'pending']);
    }

    public function test_published_announcement_and_saved_records_appear_on_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        Resident::create(['first_name' => 'Ana', 'last_name' => 'Reyes']);
        Announcement::create(['title' => 'Community Assembly', 'body' => 'The assembly is on Saturday.', 'published_at' => now()]);

        $this->get(route('dashboard'))->assertOk()->assertSee('Community Assembly')->assertSee('Live records')->assertSee('Total residents')->assertSee('1');
    }

    public function test_staff_cannot_delete_records_and_admin_can_archive_resident_history(): void
    {
        $resident = Resident::create(['first_name' => 'Ana', 'last_name' => 'Reyes']);
        $request = ServiceRequest::create(['resident_id' => $resident->id, 'request_type' => 'Barangay Clearance', 'purpose' => 'Employment']);

        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->delete(route('residents.destroy', $resident))->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->delete(route('residents.destroy', $resident))->assertRedirect(route('residents.index'));

        $this->assertSoftDeleted('residents', ['id' => $resident->id]);
        $this->assertDatabaseHas('service_requests', ['id' => $request->id, 'resident_id' => $resident->id]);
        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->get(route('requests.index'))->assertOk()->assertSee('Ana Reyes')->assertSee('Resident record archived');
    }

    public function test_resident_enum_values_are_validated_on_the_server(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->from(route('residents.create'))
            ->post(route('residents.store'), [
                'first_name' => 'Ana', 'last_name' => 'Reyes', 'gender' => 'invalid', 'civil_status' => 'invalid',
            ])
            ->assertRedirect(route('residents.create'))
            ->assertSessionHasErrors(['gender', 'civil_status']);

        $this->assertDatabaseCount('residents', 0);
    }

    public function test_admin_can_delete_announcement_and_service_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = Resident::create(['first_name' => 'Ana', 'last_name' => 'Reyes']);
        $request = ServiceRequest::create(['resident_id' => $resident->id, 'request_type' => 'Barangay Clearance', 'purpose' => 'Employment']);
        $announcement = Announcement::create(['title' => 'Old notice', 'body' => 'Expired.']);
        $this->actingAs($admin);

        $this->delete(route('requests.destroy', $request))->assertRedirect(route('requests.index'));
        $this->delete(route('announcements.destroy', $announcement))->assertRedirect(route('announcements.index'));

        $this->assertDatabaseMissing('service_requests', ['id' => $request->id]);
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_staff_can_update_account_details_and_change_password(): void
    {
        $user = User::factory()->create(['role' => 'staff', 'password' => 'password']);
        $this->actingAs($user);

        $this->put(route('settings.update'), [
            'name' => 'Office Staff', 'email' => $user->email, 'current_password' => 'password',
            'password' => 'Long-New-Password-123', 'password_confirmation' => 'Long-New-Password-123',
        ])->assertRedirect();

        $this->assertSame('Office Staff', $user->fresh()->name);
        $this->assertTrue(Hash::check('Long-New-Password-123', $user->fresh()->password));
    }

    public function test_operational_pages_require_staff_sign_in(): void
    {
        $this->get(route('residents.index'))->assertRedirect(route('login'));
        $this->get(route('certificates.index'))->assertRedirect(route('login'));
        $this->get(route('announcements.index'))->assertRedirect(route('login'));
        $this->get(route('reports.index'))->assertRedirect(route('login'));
        $this->get(route('settings.edit'))->assertRedirect(route('login'));
    }
}



