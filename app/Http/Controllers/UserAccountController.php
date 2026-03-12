<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAccountController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'Admin')
            ->where(function ($q) {
                $q->whereNull('task')->orWhere('task', '!=', 'Archived');
            })
            ->orderByDesc('id')
            ->paginate(15);

        return view('users.index', [
            'sidebar_active' => 'users.index',
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'sidebar_active' => 'users.create',
            'branches' => Branch::orderBy('branch_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_code' => ['required', 'string', 'max:50'],
            'username'    => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'fullname'    => ['required', 'string', 'max:255'],
            'role'        => ['required', 'string', 'max:255', 'in:Branch,Admin,Staff,Checker,User'],
            'branch'      => ['nullable', 'string', 'max:255', 'required_if:role,Branch'],
            'password'    => ['nullable', 'string', 'min:6', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $data['task']   = 'Active';
        $data['status'] = 'Active';
        $data['branch'] = $data['branch'] ?? '';

        if (empty($data['password'] ?? null)) {
            $data['password'] = '123456';
        }

        User::create($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User account created successfully.');
    }

    public function edit(User $user)
    {
        if ($user->role === 'Admin') {
            abort(404);
        }

        return view('users.edit', [
            'sidebar_active' => 'users.edit',
            'user' => $user,
            'branches' => Branch::orderBy('branch_name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'unique_code' => ['required', 'string', 'max:50'],
            'username'    => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'fullname'    => ['required', 'string', 'max:255'],
            'role'        => ['required', 'string', 'max:255', 'in:Branch,Admin,Staff,Checker,User'],
            'branch'      => ['nullable', 'string', 'max:255', 'required_if:role,Branch'],
            'password'    => ['nullable', 'string', 'min:6', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $data['branch'] = $data['branch'] ?? '';

        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'Admin') {
            abort(404);
        }

        $user->task = 'Archived';
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User account moved to archive.');
    }

    public function archive()
    {
        $users = User::where('role', '!=', 'Admin')
            ->where('task', 'Archived')
            ->orderByDesc('id')
            ->paginate(15);

        return view('users.archive', [
            'sidebar_active' => 'users.archive',
            'users' => $users,
        ]);
    }

    public function restore(User $user)
    {
        if ($user->role === 'Admin') {
            abort(404);
        }

        $user->task = '';
        $user->save();

        return redirect()
            ->route('users.archive')
            ->with('success', 'User account restored.');
    }
}

