<?php

namespace App\Http\Controllers;

use App\Models\ProblemReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProblemReportsController extends Controller
{
    public function mine(Request $request): View
    {
        $reports = ProblemReport::where('user_id', $request->user()->id)->latest()->paginate(10);

        return view('community.problems.index', compact('reports'));
    }

    public function create(): View
    {
        return view('community.problems.create', ['categories' => ProblemReport::CATEGORIES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reporter_phone' => ['nullable', 'string', 'max:30'],
            'category' => ['required', 'string', Rule::in(ProblemReport::CATEGORIES)],
            'subject' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:500'],
        ]);

        ProblemReport::create([
            ...$data,
            'user_id' => $request->user()->id,
            'reporter_name' => $request->user()->name,
            'reporter_email' => $request->user()->email,
            'status' => 'open',
        ]);

        return to_route('my.problems.index')->with('status', 'Thanks for letting us know. Your report has been sent to the barangay office. You can follow updates here.');
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $archived = $request->boolean('archived');
        $query = $archived ? ProblemReport::onlyTrashed() : ProblemReport::query();
        $reports = $query->with('processor')
            ->when(in_array($status, ProblemReport::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('problem-reports.index', compact('reports', 'status', 'archived'));
    }

    public function updateStatus(Request $request, ProblemReport $problemReport): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(ProblemReport::STATUSES)],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $problemReport->update([
            ...$data,
            'processed_by' => $data['status'] === 'open' ? null : $request->user()->id,
            'processed_at' => $data['status'] === 'open' ? null : now(),
        ]);

        return back()->with('status', 'Problem report updated.');
    }

    public function destroy(ProblemReport $problemReport): RedirectResponse
    {
        $problemReport->delete();

        return to_route('problem-reports.index')->with('status', 'Problem report archived.');
    }

    public function restore(int $problemReportId): RedirectResponse
    {
        ProblemReport::onlyTrashed()->findOrFail($problemReportId)->restore();

        return to_route('problem-reports.index', ['archived' => 1])->with('status', 'Problem report restored.');
    }
}
