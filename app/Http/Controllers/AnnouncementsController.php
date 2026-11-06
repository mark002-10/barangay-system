<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementsController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::with('author')->latest()->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('announcements.create', ['announcement' => new Announcement()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $publish = $request->boolean('publish');

        Announcement::create([
            ...$data,
            'created_by' => $request->user()->id,
            'published_at' => $publish ? now() : null,
        ]);

        return redirect()->route('announcements.index')->with('status', $publish ? 'Announcement published.' : 'Announcement saved as a draft.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update($this->validated($request));

        if ($request->boolean('publish') && ! $announcement->published_at) {
            $announcement->update(['published_at' => now()]);
        } elseif (! $request->boolean('publish') && $announcement->published_at) {
            $announcement->update(['published_at' => null]);
        }

        return redirect()->route('announcements.index')->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('announcements.index')->with('status', 'Announcement deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:10000'],
        ]);
    }
}
