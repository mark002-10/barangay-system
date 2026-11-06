<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ProblemReport;
use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        if ($request->user()->hasRole('user')) {
            return view('dashboard-user', [
                'announcement' => Announcement::whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at')->first(),
                'myRequests' => ServiceRequest::where('requester_id', $request->user()->id)->latest()->limit(4)->get(),
                'myReports' => ProblemReport::where('user_id', $request->user()->id)->latest()->limit(4)->get(),
            ]);
        }

        $year = now()->year;
        $monthlyRequests = collect(range(1, 12))
            ->map(fn ($month) => ServiceRequest::whereYear('created_at', $year)->whereMonth('created_at', $month)->count())
            ->all();

        return view('dashboard', [
            'metrics' => [
                ['label' => 'Total residents', 'value' => number_format(Resident::count()), 'icon' => 'residents', 'tone' => 'green'],
                ['label' => 'Pending requests', 'value' => number_format(ServiceRequest::where('status', 'pending')->count()), 'icon' => 'requests', 'tone' => 'gold'],
                ['label' => 'Certificates issued', 'value' => number_format(ServiceRequest::whereIn('request_type', ServiceRequest::CERTIFICATE_TYPES)->where('status', 'issued')->count()), 'icon' => 'certificates', 'tone' => 'green'],
            ],
            'monthlyRequests' => $monthlyRequests,
            'chartMax' => max(4, max($monthlyRequests)),
            'months' => collect(range(1, 12))->map(fn ($month) => Carbon::create($year, $month, 1)->format('M'))->all(),
            'year' => $year,
            'announcement' => Announcement::whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at')->first(),
            'recentRequests' => ServiceRequest::with(['resident', 'requester'])->latest()->limit(5)->get(),
        ]);
    }
}
