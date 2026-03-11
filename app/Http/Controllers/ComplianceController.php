<?php

namespace App\Http\Controllers;

use App\Models\Compliance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplianceController extends Controller
{
    /**
     * Display a listing of compliances.
     */
    public function index()
    {
        $compliances = Compliance::orderByDesc('created_at')->paginate(15);

        return view('compliance.index', [
            'sidebar_active' => 'compliance.index',
            'compliances' => $compliances,
        ]);
    }

    /**
     * Show the form for creating a new compliance.
     */
    public function create()
    {
        return view('compliance.create', [
            'sidebar_active' => 'compliance.create',
        ]);
    }

    /**
     * Store a newly created compliance.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'column' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('compliance.create')
                ->withErrors($validator)
                ->withInput();
        }

        Compliance::create($validator->validated());

        return redirect()
            ->route('compliance.index')
            ->with('success', 'Compliance created successfully.');
    }

    /**
     * Show the form for editing the specified compliance.
     */
    public function edit(Compliance $compliance)
    {
        return view('compliance.edit', [
            'sidebar_active' => 'compliance.edit',
            'compliance' => $compliance,
        ]);
    }

    /**
     * Update the specified compliance.
     */
    public function update(Request $request, Compliance $compliance)
    {
        $validator = Validator::make($request->all(), [
            'column' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('compliance.edit', $compliance)
                ->withErrors($validator)
                ->withInput();
        }

        $compliance->update($validator->validated());

        return redirect()
            ->route('compliance.index')
            ->with('success', 'Compliance updated successfully.');
    }

    /**
     * Remove the specified compliance.
     */
    public function destroy(Compliance $compliance)
    {
        $compliance->delete();

        return redirect()
            ->route('compliance.index')
            ->with('success', 'Compliance deleted successfully.');
    }
}
