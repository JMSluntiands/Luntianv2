<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::orderByDesc('created_at')->paginate(15);

        return view('status.index', [
            'sidebar_active' => 'status.index',
            'statuses' => $statuses,
        ]);
    }

    public function create()
    {
        return view('status.create', [
            'sidebar_active' => 'status.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#?([A-Fa-f0-9]{6})$/'],
        ], [
            'color.regex' => 'Color must be a valid hex code (e.g. #ff0000 or ff0000).',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('status.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['color']) && $data['color'][0] !== '#') {
            $data['color'] = '#' . $data['color'];
        }

        Status::create($data);

        return redirect()
            ->route('status.index')
            ->with('success', 'Status created successfully.');
    }

    public function edit(Status $status)
    {
        return view('status.edit', [
            'sidebar_active' => 'status.edit',
            'status' => $status,
        ]);
    }

    public function update(Request $request, Status $status)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#?([A-Fa-f0-9]{6})$/'],
        ], [
            'color.regex' => 'Color must be a valid hex code (e.g. #ff0000 or ff0000).',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('status.edit', $status)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['color']) && $data['color'][0] !== '#') {
            $data['color'] = '#' . $data['color'];
        }

        $status->update($data);

        return redirect()
            ->route('status.index')
            ->with('success', 'Status updated successfully.');
    }

    public function destroy(Status $status)
    {
        $status->delete();

        return redirect()
            ->route('status.index')
            ->with('success', 'Status deleted successfully.');
    }
}
