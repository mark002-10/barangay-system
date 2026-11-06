<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RequestsController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $archived = $request->boolean('archived');
        $query = $archived ? ServiceRequest::onlyTrashed() : ServiceRequest::query();
        $requests = $query->with(['resident', 'requester'])
            ->when(in_array($status, ServiceRequest::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('requests.index', compact('requests', 'status', 'archived'));
    }

    public function create(): View
    {
        return view('requests.create', [
            'residents' => Resident::where('is_active', true)->orderBy('last_name')->orderBy('first_name')->get(),
            'types' => ServiceRequest::CERTIFICATE_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'resident_id' => ['required', 'integer', Rule::exists('residents', 'id')->where('is_active', true)],
            'request_type' => ['required', 'string', Rule::in(ServiceRequest::CERTIFICATE_TYPES)],
            'purpose' => ['required', 'string', 'max:2000'],
        ]);

        ServiceRequest::create($data);

        return redirect()->route('requests.index')->with('status', 'Service request recorded.');
    }

    public function edit(ServiceRequest $serviceRequest): View
    {
        return view('requests.edit', [
            'serviceRequest' => $serviceRequest->load(['resident', 'requester']),
            'residents' => Resident::where('is_active', true)->orderBy('last_name')->orderBy('first_name')->get(),
            'statuses' => ServiceRequest::STATUSES,
        ]);
    }

    public function certificate(ServiceRequest $serviceRequest): View
    {
        abort_unless($serviceRequest->isCertificate() && $serviceRequest->status === 'issued' && $serviceRequest->resident_id, 404);

        $serviceRequest->load('resident');
        abort_unless($serviceRequest->resident, 404);

        return view('certificates.print', compact('serviceRequest'));
    }

    public function updateStatus(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(ServiceRequest::STATUSES)],
            'resident_id' => ['sometimes', 'nullable', 'integer', Rule::exists('residents', 'id')->where('is_active', true)],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $residentId = array_key_exists('resident_id', $data) ? $data['resident_id'] : $serviceRequest->resident_id;

        if (in_array($data['status'], ['approved', 'issued'], true) && ! $residentId) {
            return back()->withErrors(['resident_id' => 'Link this request to a verified resident record before approval.'])->withInput();
        }

        if ($data['status'] === 'issued' && ! $serviceRequest->isCertificate()) {
            return back()->withErrors(['status' => 'Only certificate requests can be marked as issued.']);
        }

        if ($data['status'] === 'issued' && $serviceRequest->status !== 'approved') {
            return back()->withErrors(['status' => 'Approve the request before issuing its certificate.']);
        }

        $serviceRequest->update([
            ...$data,
            'resident_id' => $residentId,
            'processed_by' => $data['status'] === 'pending' ? null : $request->user()->id,
            'processed_at' => $data['status'] === 'pending' ? null : now(),
        ]);

        return to_route('requests.index')->with('status', 'Request status updated.');
    }

    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->delete();

        return redirect()->route('requests.index')->with('status', 'Service request archived.');
    }

    public function restore(int $serviceRequestId): RedirectResponse
    {
        ServiceRequest::onlyTrashed()->findOrFail($serviceRequestId)->restore();

        return to_route('requests.index', ['archived' => 1])->with('status', 'Service request restored.');
    }
}
