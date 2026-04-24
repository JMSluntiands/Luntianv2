<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailAmt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientEmailAmtController extends Controller
{
    public function index()
    {
        $emails = ClientEmailAmt::orderBy('email')->paginate(20);

        return view('amt_client_email.index', [
            'sidebar_active' => 'amt_client_email.index',
            'emails' => $emails,
        ]);
    }

    public function create()
    {
        return view('amt_client_email.create', [
            'sidebar_active' => 'amt_client_email.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'unique:client_email_amt,email'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('amt_client_email.create')
                ->withErrors($validator)
                ->withInput();
        }

        ClientEmailAmt::create($validator->validated());

        return redirect()
            ->route('amt_client_email.index')
            ->with('success', 'Email address saved.');
    }

    public function edit(ClientEmailAmt $client_email_amt)
    {
        return view('amt_client_email.edit', [
            'sidebar_active' => 'amt_client_email.edit',
            'clientEmailAmt' => $client_email_amt,
        ]);
    }

    public function update(Request $request, ClientEmailAmt $client_email_amt)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('client_email_amt', 'email')->ignore($client_email_amt->id),
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('amt_client_email.edit', $client_email_amt)
                ->withErrors($validator)
                ->withInput();
        }

        $client_email_amt->update($validator->validated());

        return redirect()
            ->route('amt_client_email.index')
            ->with('success', 'Email address updated.');
    }

    public function destroy(ClientEmailAmt $client_email_amt)
    {
        $client_email_amt->delete();

        return redirect()
            ->route('amt_client_email.index')
            ->with('success', 'Email address deleted.');
    }
}
