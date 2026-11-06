<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        $year = now()->year;
        $monthlyRequests = collect(range(1, 12))->map(fn ($month) => ServiceRequest::whereYear('created_at', $year)->whereMonth('created_at', $month)->count())->all();

        return view('reports.index', [
            'year' => $year,
            'totalResidents' => Resident::count(),
            'activeResidents' => Resident::where('is_active', true)->count(),
            'totalRequests' => ServiceRequest::count(),
            'pendingRequests' => ServiceRequest::where('status', 'pending')->count(),
            'issuedCertificates' => ServiceRequest::whereIn('request_type', ServiceRequest::CERTIFICATE_TYPES)->where('status', 'issued')->count(),
            'monthlyRequests' => $monthlyRequests,
            'months' => collect(range(1, 12))->map(fn ($month) => Carbon::create($year, $month, 1)->format('M'))->all(),
            'statusCounts' => collect(ServiceRequest::STATUSES)->mapWithKeys(fn ($status) => [$status => ServiceRequest::where('status', $status)->count()])->all(),
        ]);
    }
}
