<?php

namespace App\Http\Controllers;

use App\Models\EmailConfig;
use App\Models\SlackConfig;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        return view('settings.notifications', [
            'sidebar_active' => 'settings.notifications',
            'emailConfig' => EmailConfig::first(),
            'slackConfig' => SlackConfig::first(),
        ]);
    }
}
