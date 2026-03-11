<?php

namespace App\Http\Controllers;

use App\Models\EmailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailConfigController extends Controller
{
    /**
     * Show the email configuration form (create or edit).
     */
    public function index()
    {
        $config = EmailConfig::first();

        return view('settings.email-config', [
            'sidebar_active' => 'settings.email_config',
            'config' => $config,
        ]);
    }

    /**
     * Store a new email config or update existing.
     */
    public function store(Request $request)
    {
        $rules = [
            'smtp_host' => ['required', 'string', 'max:255'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'from_email' => ['nullable', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'encryption' => ['nullable', 'string', 'max:10'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('settings.email_config')
                ->withErrors($validator)
                ->withInput($request->except('smtp_password'));
        }

        $data = $validator->validated();
        $data['is_active'] = true;

        // Don't update password if left blank (edit case)
        if (empty($data['smtp_password'])) {
            unset($data['smtp_password']);
        }

        $config = EmailConfig::first();

        if ($config) {
            $config->update($data);
            $message = 'Email configuration updated successfully.';
        } else {
            EmailConfig::create($data);
            $message = 'Email configuration saved successfully.';
        }

        return redirect()
            ->route('settings.email_config')
            ->with('success', $message);
    }
}
