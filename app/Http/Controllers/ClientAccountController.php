<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientAccountController extends Controller
{
    public function index()
    {
        $clients = ClientAccount::orderByDesc('client_account_id')->paginate(15);

        return view('client.index', [
            'sidebar_active' => 'client.index',
            'clients' => $clients,
        ]);
    }

    public function create()
    {
        return view('client.create', [
            'sidebar_active' => 'client.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_account_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('client.create')
                ->withErrors($validator)
                ->withInput();
        }

        ClientAccount::create($validator->validated());

        return redirect()
            ->route('client.index')
            ->with('success', 'Client created successfully.');
    }

    public function edit(ClientAccount $client_account)
    {
        return view('client.edit', [
            'sidebar_active' => 'client.edit',
            'client' => $client_account,
        ]);
    }

    public function update(Request $request, ClientAccount $client_account)
    {
        $validator = Validator::make($request->all(), [
            'client_account_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('client.edit', $client_account)
                ->withErrors($validator)
                ->withInput();
        }

        $client_account->update($validator->validated());

        return redirect()
            ->route('client.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(ClientAccount $client_account)
    {
        $client_account->delete();

        return redirect()
            ->route('client.index')
            ->with('success', 'Client deleted successfully.');
    }
}
