<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Support\AddJobModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserAccountController extends Controller
{
    public function index()
    {
        // Show every row in users except archived (those stay on Archive page).
        $users = User::query()
            ->where(function ($q) {
                $q->whereNull('task')
                    ->orWhereRaw('LOWER(TRIM(COALESCE(task, ""))) != ?', ['archived']);
            })
            ->orderByDesc('id')
            ->get();

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
            'is_employee' => ['nullable', 'boolean'],
            'add_job_staff_modules' => ['nullable', 'array'],
            'add_job_staff_modules.*' => ['string', Rule::in(AddJobModules::keys())],
            'add_job_checker_modules' => ['nullable', 'array'],
            'add_job_checker_modules.*' => ['string', Rule::in(AddJobModules::keys())],
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
        $data['is_employee'] = $request->boolean('is_employee');
        $data['add_job_staff_modules'] = array_values($data['add_job_staff_modules'] ?? []);
        $data['add_job_checker_modules'] = array_values($data['add_job_checker_modules'] ?? []);

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
        return view('users.edit', [
            'sidebar_active' => 'users.edit',
            'user' => $user,
            'branches' => Branch::orderBy('branch_name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'unique_code' => ['required', 'string', 'max:50'],
            'username'    => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'fullname'    => ['required', 'string', 'max:255'],
            'role'        => ['required', 'string', 'max:255', 'in:Branch,Admin,Staff,Checker,User'],
            'branch'      => ['nullable', 'string', 'max:255', 'required_if:role,Branch'],
            'password'    => ['nullable', 'string', 'min:6', 'max:255'],
            'status'      => ['nullable', 'string', 'in:Active,Inactive'],
            'is_employee' => ['nullable', 'boolean'],
            'add_job_staff_modules' => ['nullable', 'array'],
            'add_job_staff_modules.*' => ['string', Rule::in(AddJobModules::keys())],
            'add_job_checker_modules' => ['nullable', 'array'],
            'add_job_checker_modules.*' => ['string', Rule::in(AddJobModules::keys())],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $data['branch'] = $data['branch'] ?? '';
        $data['is_employee'] = $request->boolean('is_employee');
        if (isset($data['status'])) {
            $data['status'] = $data['status'];
            if (strtolower(trim((string) ($user->task ?? ''))) !== 'archived') {
                $data['task'] = $data['status'];
            }
        }
        $data['add_job_staff_modules'] = array_values($data['add_job_staff_modules'] ?? []);
        $data['add_job_checker_modules'] = array_values($data['add_job_checker_modules'] ?? []);

        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User account updated successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        if (strtolower(trim((string) $user->role)) === 'admin') {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:Active,Inactive'],
        ]);

        $status = $data['status'];
        $user->status = $status;
        // Keep task in sync for list/archive/timesheet filters (Archive stays separate)
        if (strtolower(trim((string) ($user->task ?? ''))) !== 'archived') {
            $user->task = $status;
        }
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated.',
                'user_status' => $status,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'User status updated to '.$status.'.');
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

