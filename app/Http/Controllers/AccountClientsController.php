<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AccountClientsController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('client_code')->paginate(15);

        return view('accounts.clients.index', [
            'sidebar_active' => 'accounts.clients.index',
            'clients' => $clients,
        ]);
    }

    public function create()
    {
        $users = User::whereNotIn('role', ['Admin', 'Staff', 'Checker'])
            ->orderBy('unique_code')
            ->get(['id', 'unique_code', 'fullname', 'email']);

        return view('accounts.clients.create', [
            'sidebar_active' => 'accounts.clients.create',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $allowedCodes = User::whereNotIn('role', ['Admin', 'Staff', 'Checker'])->pluck('unique_code')->toArray();
        $validator = Validator::make($request->all(), [
            'client_code' => ['required', 'string', 'max:50', 'unique:clients,client_code', Rule::in($allowedCodes)],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
        ], [
            'client_code.in' => 'The selected code must be a user with role Branch or User (Admin, Staff, Checker are not allowed).',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('accounts.clients.create')
                ->withErrors($validator)
                ->withInput();
        }

        Client::create($validator->validated());

        return redirect()
            ->route('accounts.clients.index')
            ->with('success', 'Client account created successfully.');
    }

    public function edit(Client $client)
    {
        $users = User::whereNotIn('role', ['Admin', 'Staff', 'Checker'])
            ->orderBy('unique_code')
            ->get(['id', 'unique_code', 'fullname', 'email']);

        return view('accounts.clients.edit', [
            'sidebar_active' => 'accounts.clients.edit',
            'client' => $client,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $validator = Validator::make($request->all(), [
            'client_code' => [
                'required',
                'string',
                'max:50',
                'unique:clients,client_code,' . $client->id,
                Rule::in(array_merge([$client->client_code], User::whereNotIn('role', ['Admin', 'Staff', 'Checker'])->pluck('unique_code')->toArray())),
            ],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
        ], [
            'client_code.in' => 'The selected code must exist in user accounts (unique_code) or be the current code.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('accounts.clients.edit', $client)
                ->withErrors($validator)
                ->withInput();
        }

        $client->update($validator->validated());

        return redirect()
            ->route('accounts.clients.index')
            ->with('success', 'Client account updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('accounts.clients.index')
            ->with('success', 'Client account deleted successfully.');
    }
}
