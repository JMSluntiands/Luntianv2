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
            'font_color' => ['nullable', 'string', 'max:7', 'regex:/^#?([A-Fa-f0-9]{6})$/'],
        ], [
            'color.regex' => 'Color must be a valid hex code (e.g. #ff0000 or ff0000).',
            'font_color.regex' => 'Text color must be a valid hex code (e.g. #333333 or 333333).',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('status.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $this->normalizeStatusColorFields($validator->validated());

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
            'font_color' => ['nullable', 'string', 'max:7', 'regex:/^#?([A-Fa-f0-9]{6})$/'],
        ], [
            'color.regex' => 'Color must be a valid hex code (e.g. #ff0000 or ff0000).',
            'font_color.regex' => 'Text color must be a valid hex code (e.g. #333333 or 333333).',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('status.edit', $status)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $this->normalizeStatusColorFields($validator->validated());

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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeStatusColorFields(array $data): array
    {
        if (! empty($data['color']) && $data['color'][0] !== '#') {
            $data['color'] = '#' . $data['color'];
        }

        if (array_key_exists('font_color', $data)) {
            $fc = trim((string) $data['font_color']);
            $data['font_color'] = $fc === '' ? null : $fc;
        }
        if (! empty($data['font_color']) && $data['font_color'][0] !== '#') {
            $data['font_color'] = '#' . $data['font_color'];
        }

        return $data;
    }
}
