<?php

namespace App\Http\Controllers;

use App\Models\Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckerController extends Controller
{
    public function index()
    {
        $checkers = Checker::orderByDesc('id')->paginate(15);

        return view('checker.index', [
            'sidebar_active' => 'checker.index',
            'checkers' => $checkers,
        ]);
    }

    public function create()
    {
        return view('checker.create', [
            'sidebar_active' => 'checker.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checker_id' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('checker.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (empty($data['username'] ?? null)) {
            $data['username'] = $data['checker_id'];
        }
        if (empty($data['password'] ?? null)) {
            $data['password'] = '123456';
        }

        $nextId = (Checker::max('id') ?? 0) + 1;
        $data['id'] = $nextId;

        Checker::create($data);

        return redirect()
            ->route('checker.index')
            ->with('success', 'Checker created successfully.');
    }

    public function edit(Checker $checker)
    {
        return view('checker.edit', [
            'sidebar_active' => 'checker.edit',
            'checker' => $checker,
        ]);
    }

    public function update(Request $request, Checker $checker)
    {
        $validator = Validator::make($request->all(), [
            'checker_id' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('checker.edit', $checker)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (!array_key_exists('username', $data)) {
            unset($data['username']);
        }
        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        }

        $checker->update($data);

        return redirect()
            ->route('checker.index')
            ->with('success', 'Checker updated successfully.');
    }

    public function destroy(Checker $checker)
    {
        $checker->delete();

        return redirect()
            ->route('checker.index')
            ->with('success', 'Checker deleted successfully.');
    }
}

