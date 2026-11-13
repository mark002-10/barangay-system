<?php

namespace Tests\Feature;

use App\Models\ProblemReport;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPortalWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_user_can_request_certificate_and_staff_must_link_resident_before_approval(): void
    {
        $user = User::factory()->create(['role' => 'user', 'name' => 'Dincel Montales', 'email' => 'dincel@example.com']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $this->get(route('my.certificates.create'))->assertOk()->assertSee('Request a certificate');
        $this->post(route('my.certificates.store'), [
            'request_type' => 'Barangay Clearance',
            'purpose' => 'Employment',
            'requester_phone' => '09171234567',
            'requester_address' => 'Purok 2, Estanza',
        ])->assertRedirect(route('my.certificates.index'));

        $request = ServiceRequest::where('requester_id', $user->id)->firstOrFail();
        $this->assertNull($request->resident_id);
        $this->assertSame('Dincel Montales', $request->requester_name);
        $this->get(route('my.certificates.index'))->assertOk()->assertSee('Barangay Clearance')->assertDontSee('Other member private request');
        $this->get(route('requests.index'))->assertForbidden();
        $this->get(route('my.certificates.index'))->assertDontSee($otherUser->email);

        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);
        $this->patch(route('requests.status', $request), ['status' => 'approved'])
            ->assertSessionHasErrors('resident_id');

        $resident = Resident::create(['first_name' => 'Dincel', 'last_name' => 'Montales']);
        $this->patch(route('requests.status', $request), [
            'status' => 'approved', 'resident_id' => $resident->id, 'staff_notes' => 'Verified with resident list.',
        ])->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('service_requests', [
            'id' => $request->id, 'resident_id' => $resident->id, 'status' => 'approved',
        ]);
        $this->patch(route('requests.status', $request), ['status' => 'issued'])
            ->assertRedirect(route('requests.index'));
    }

    public function test_user_can_submit_problem_report_and_track_staff_updates(): void
    {
        $user = User::factory()->create(['role' => 'user', 'name' => 'Community Member', 'email' => 'member@example.com']);
        $this->actingAs($user);

        $this->get(route('my.problems.create'))->assertOk()->assertSee('Report a problem');
        $this->post(route('my.problems.store'), [
            'reporter_phone' => '09170000000',
            'category' => 'Street lighting',
            'subject' => 'Streetlight out near Purok 2',
            'description' => 'The light has been out for three nights.',
            'location' => 'Near the covered court, Purok 2',
        ])->assertRedirect(route('my.problems.index'));

        $report = ProblemReport::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('open', $report->status);
        $this->get(route('my.problems.index'))->assertOk()->assertSee('Streetlight out near Purok 2')->assertDontSee('Other user private report');
        $this->get(route('problem-reports.index'))->assertForbidden();

        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);
        $this->get(route('problem-reports.index'))->assertOk()->assertSee('Community Member');
        $this->patch(route('problem-reports.status', $report), [
            'status' => 'in_progress', 'staff_notes' => 'Maintenance team has been notified.',
        ])->assertRedirect();

        $this->assertDatabaseHas('problem_reports', [
            'id' => $report->id, 'status' => 'in_progress', 'processed_by' => $staff->id,
        ]);
        $this->actingAs($user)->get(route('my.problems.index'))->assertSee('In progress')->assertSee('Maintenance team has been notified.');
    }
}


