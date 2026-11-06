<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificatesController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $certificates = ServiceRequest::with(['resident', 'requester'])
            ->whereIn('request_type', ServiceRequest::CERTIFICATE_TYPES)
            ->when(in_array($status, ServiceRequest::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('certificates.index', compact('certificates', 'status'));
    }
}
