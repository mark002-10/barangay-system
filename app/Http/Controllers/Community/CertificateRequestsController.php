<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CertificateRequestsController extends Controller
{
    public function index(Request $request): View
    {
        $requests = ServiceRequest::with('resident')
            ->where('requester_id', $request->user()->id)
            ->whereIn('request_type', ServiceRequest::CERTIFICATE_TYPES)
            ->latest()
            ->paginate(10);

        return view('community.certificates.index', compact('requests'));
    }

    public function create(): View
    {
        return view('community.certificates.create', ['types' => ServiceRequest::CERTIFICATE_TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'request_type' => ['required', 'string', Rule::in(ServiceRequest::CERTIFICATE_TYPES)],
            'purpose' => ['required', 'string', 'max:2000'],
            'requester_phone' => ['required', 'string', 'max:30'],
            'requester_address' => ['required', 'string', 'max:500'],
        ]);

        ServiceRequest::create([
            ...$data,
            'requester_id' => $request->user()->id,
            'requester_name' => $request->user()->name,
            'requester_email' => $request->user()->email,
            'status' => 'pending',
        ]);

        return to_route('my.certificates.index')->with('status', 'Thanks, your request has been sent to the barangay office. You can follow its status here.');
    }
}
