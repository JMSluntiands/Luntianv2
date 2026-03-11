<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::orderByDesc('id')->paginate(15);

        return view('staff.index', [
            'sidebar_active' => 'staff.index',
            'staff' => $staff,
        ]);
    }

    public function create()
    {
        return view('staff.create', [
            'sidebar_active' => 'staff.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('staff.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        if (empty($data['username'] ?? null)) {
            $data['username'] = $data['staff_id'];
        }
        if (empty($data['password'] ?? null)) {
            $data['password'] = '123456';
        }

        $nextId = (Staff::max('id') ?? 0) + 1;
        $data['id'] = $nextId;

        Staff::create($data);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff created successfully.');
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', [
            'sidebar_active' => 'staff.edit',
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('staff.edit', $staff)
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

        $staff->update($data);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff deleted successfully.');
    }
}

