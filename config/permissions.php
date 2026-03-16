<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Protectable routes (permission per page)
    | Key = route name, Value = label shown in Permission settings
    | Grouped by section for the UI.
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'Dashboard' => [
            'dashboard' => 'Dashboard',
        ],
        'LBS' => [
            'lbs.add' => 'LBS Add New',
            'lbs.list' => 'LBS List',
            'lbs.completed' => 'LBS Completed',
            'lbs.review' => 'LBS For Review',
            'lbs.mailbox' => 'LBS Mailbox',
            'lbs.trash' => 'LBS Archive',
        ],
        'BPH' => [
            'bph.add' => 'BPH Add New',
            'bph.list' => 'BPH List',
        ],
        'CSP' => [
            'csp.add' => 'CSP Add New',
            'csp.list' => 'CSP List',
            'csp.completed' => 'CSP Completed',
            'csp.review' => 'CSP For Review',
            'csp.trash' => 'CSP Archive',
        ],
        'Bluinq' => [
            'bluinq.add' => 'Bluinq Add New',
            'bluinq.list' => 'Bluinq List',
            'bluinq.completed' => 'Bluinq Completed',
            'bluinq.review' => 'Bluinq For Review',
            'bluinq.trash' => 'Bluinq Archive',
        ],
        'NH' => [
            'nh.add' => 'NH Add New',
            'nh.list' => 'NH List',
            'nh.completed' => 'NH Completed',
            'nh.review' => 'NH For Review',
            'nh.trash' => 'NH Archive',
        ],
        'LC Home Builder' => [
            'lc_home_builder.add' => 'LC Home Builder Add New',
            'lc_home_builder.list' => 'LC Home Builder List',
            'lc_home_builder.completed' => 'LC Home Builder Completed',
            'lc_home_builder.review' => 'LC Home Builder For Review',
            'lc_home_builder.trash' => 'LC Home Builder Archive',
        ],
        'Efficient Living' => [
            'efficient_living.add' => 'Efficient Living Add New',
            'efficient_living.list' => 'Efficient Living List',
            'efficient_living.completed' => 'Efficient Living Completed',
            'efficient_living.review' => 'Efficient Living For Review',
            'efficient_living.trash' => 'Efficient Living Archive',
        ],
        'Leading Energy' => [
            'leading_energy.add' => 'Leading Energy Add New',
            'leading_energy.list' => 'Leading Energy List',
            'leading_energy.completed' => 'Leading Energy Completed',
            'leading_energy.review' => 'Leading Energy For Review',
            'leading_energy.trash' => 'Leading Energy Archive',
        ],
        'Reports' => [
            'reports' => 'Reports',
        ],
        'Settings' => [
            'settings.email_config' => 'Email Configuration',
            'settings.slack_config' => 'Slack Configuration',
            'settings.permission' => 'Permission',
        ],
        'Job' => [
            'compliance.index' => 'Compliance',
            'priority.index' => 'Priority',
            'status.index' => 'Status',
            'job_request.index' => 'Job Request',
            'client.index' => 'Client',
        ],
        'Branch' => [
            'branch.index' => 'Branch List',
            'branch.archive' => 'Branch Archive',
        ],
        'Accounts' => [
            'users.index' => 'User Accounts',
            'accounts.clients.index' => 'Client Accounts',
            'users.archive' => 'User Archive',
        ],
        'Account' => [
            'account.settings.edit' => 'My Account Settings',
        ],
    ],

    'roles' => ['Admin', 'Branch', 'Staff', 'Checker', 'User'],
];
