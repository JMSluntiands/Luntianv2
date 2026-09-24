<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $data = $validator->validated();
        $oldCode = trim((string) $client->client_code);
        $newCode = trim((string) $data['client_code']);

        try {
            DB::transaction(function () use ($client, $data, $oldCode, $newCode) {
                if ($oldCode !== '' && strcasecmp($oldCode, $newCode) !== 0) {
                    Schema::disableForeignKeyConstraints();
                    try {
                        $client->update($data);
                        $this->renameClientCodeReferences($oldCode, $newCode);
                    } finally {
                        Schema::enableForeignKeyConstraints();
                    }
                } else {
                    $client->update($data);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('accounts.clients.edit', $client)
                ->withErrors([
                    'client_code' => 'Could not change the client code because related job records still use the current code.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('accounts.clients.index')
            ->with('success', 'Client account updated successfully.');
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('accounts.clients.index')
                ->with('error', 'Cannot delete this client while job requests or jobs still use its code.');
        }

        return redirect()
            ->route('accounts.clients.index')
            ->with('success', 'Client account deleted successfully.');
    }

    private function renameClientCodeReferences(string $oldCode, string $newCode): void
    {
        foreach ($this->tablesWithClientCodeColumn() as $table) {
            if ($table === 'clients' || ! Schema::hasTable($table) || ! Schema::hasColumn($table, 'client_code')) {
                continue;
            }

            DB::table($table)->where('client_code', $oldCode)->update(['client_code' => $newCode]);
        }
    }

    /** @return list<string> */
    private function tablesWithClientCodeColumn(): array
    {
        $schema = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT TABLE_NAME AS table_name FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND COLUMN_NAME = ?',
            [$schema, 'client_code']
        );

        $tables = [];
        foreach ($rows as $row) {
            $name = trim((string) ($row->table_name ?? ''));
            if ($name !== '') {
                $tables[] = $name;
            }
        }

        return array_values(array_unique($tables));
    }
}
