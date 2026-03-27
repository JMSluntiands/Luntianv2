<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailBph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientEmailBphController extends Controller
{
    public function index()
    {
        $emails = ClientEmailBph::orderBy('email')->paginate(20);

        return view('bph_client_email.index', [
            'sidebar_active' => 'bph_client_email.index',
            'emails' => $emails,
        ]);
    }

    public function create()
    {
        return view('bph_client_email.create', [
            'sidebar_active' => 'bph_client_email.create',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'unique:client_email_bph,email'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('bph_client_email.create')
                ->withErrors($validator)
                ->withInput();
        }

        ClientEmailBph::create($validator->validated());

        return redirect()
            ->route('bph_client_email.index')
            ->with('success', 'Email address saved.');
    }

    public function edit(ClientEmailBph $client_email_bph)
    {
        return view('bph_client_email.edit', [
            'sidebar_active' => 'bph_client_email.edit',
            'clientEmailBph' => $client_email_bph,
        ]);
    }

    public function update(Request $request, ClientEmailBph $client_email_bph)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('client_email_bph', 'email')->ignore($client_email_bph->id),
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('bph_client_email.edit', $client_email_bph)
                ->withErrors($validator)
                ->withInput();
        }

        $client_email_bph->update($validator->validated());

        return redirect()
            ->route('bph_client_email.index')
            ->with('success', 'Email address updated.');
    }

    public function destroy(ClientEmailBph $client_email_bph)
    {
        $client_email_bph->delete();

        return redirect()
            ->route('bph_client_email.index')
            ->with('success', 'Email address deleted.');
    }
}
