<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderByDesc('start_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('announcement.index', [
            'sidebar_active' => 'announcement.index',
            'announcements' => $announcements,
        ]);
    }

    public function create()
    {
        return view('announcement.form', [
            'sidebar_active' => 'announcement.create',
            'announcement' => new Announcement(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = Auth::id() ?? session('user_id');

        Announcement::create($data);

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('announcement.form', [
            'sidebar_active' => 'announcement.edit',
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $this->validateData($request);
        $announcement->update($data);

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,active,inactive'],
        ]);
    }
}

