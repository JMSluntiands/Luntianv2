<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriorityController extends Controller
{
    public function index()
    {
        $priorities = Priority::orderByDesc('created_at')->paginate(15);

        return view('priority.index', [
            'sidebar_active' => 'priority.index',
            'priorities' => $priorities,
        ]);
    }

    public function create()
    {
        return view('priority.create', [
            'sidebar_active' => 'priority.create',
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
                ->route('priority.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['color']) && ($data['color'][0] ?? '') !== '#') {
            $data['color'] = '#' . $data['color'];
        }
        Priority::create($data);

        return redirect()
            ->route('priority.index')
            ->with('success', 'Priority created successfully.');
    }

    public function edit(Priority $priority)
    {
        return view('priority.edit', [
            'sidebar_active' => 'priority.edit',
            'priority' => $priority,
        ]);
    }

    public function update(Request $request, Priority $priority)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#?([A-Fa-f0-9]{6})$/'],
        ], [
            'color.regex' => 'Color must be a valid hex code (e.g. #ff0000 or ff0000).',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('priority.edit', $priority)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['color']) && ($data['color'][0] ?? '') !== '#') {
            $data['color'] = '#' . $data['color'];
        }
        $priority->update($data);

        return redirect()
            ->route('priority.index')
            ->with('success', 'Priority updated successfully.');
    }

    public function destroy(Priority $priority)
    {
        $priority->delete();

        return redirect()
            ->route('priority.index')
            ->with('success', 'Priority deleted successfully.');
    }
}
