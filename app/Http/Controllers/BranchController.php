<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderByDesc('created_at')->paginate(15);

        return view('branch.index', [
            'sidebar_active' => 'branch.index',
            'branches' => $branches,
        ]);
    }

    public function archive()
    {
        $branches = Branch::onlyTrashed()
            ->orderByDesc('id')
            ->paginate(15);

        return view('branch.archive', [
            'sidebar_active' => 'branch.archive',
            'branches' => $branches,
        ]);
    }

    public function create()
    {
        return view('branch.create', [
            'sidebar_active' => 'branch.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branch.create')
                ->withErrors($validator)
                ->withInput();
        }

        Branch::create($validator->validated());

        return redirect()
            ->route('branch.index')
            ->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch)
    {
        return view('branch.edit', [
            'sidebar_active' => 'branch.edit',
            'branch' => $branch,
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branch.edit', $branch)
                ->withErrors($validator)
                ->withInput();
        }

        $branch->update($validator->validated());

        return redirect()
            ->route('branch.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()
            ->route('branch.index')
            ->with('success', 'Branch deleted successfully.');
    }

    public function restore(int $branch)
    {
        $branchModel = Branch::onlyTrashed()->findOrFail($branch);
        $branchModel->restore();

        return redirect()
            ->route('branch.archive')
            ->with('success', 'Branch restored successfully.');
    }
}

