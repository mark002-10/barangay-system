<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResidentsController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $showArchived = $request->user()->hasRole('admin') && $request->boolean('archived');
        $query = $showArchived ? Resident::onlyTrashed() : Resident::query();
        $residents = $query
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('residents.index', compact('residents', 'search', 'showArchived'));
    }

    public function create(): View
    {
        return view('residents.create', ['resident' => new Resident()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $resident = Resident::create($data);

        return redirect()->route('residents.index')->with('status', "Resident {$resident->full_name} added.");
    }

    public function show(Resident $resident): View
    {
        return view('residents.show', compact('resident'));
    }

    public function edit(Resident $resident): View
    {
        return view('residents.edit', compact('resident'));
    }

    public function update(Request $request, Resident $resident): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $resident->update($data);

        return redirect()->route('residents.index')->with('status', 'Resident record updated.');
    }

    public function restore(int $residentId): RedirectResponse
    {
        $resident = Resident::withTrashed()->findOrFail($residentId);
        $resident->restore();

        return redirect()->route('residents.index')->with('status', 'Resident record restored.');
    }

    public function destroy(Resident $resident): RedirectResponse
    {
        $resident->delete();

        return redirect()->route('residents.index')->with('status', 'Resident record archived. Linked service history was preserved.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', Rule::in(Resident::GENDERS)],
            'civil_status' => ['nullable', 'string', Rule::in(Resident::CIVIL_STATUSES)],
            'purok' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}

