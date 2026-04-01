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
            'lbs.job.view' => 'LBS View Job',
            'lbs.job.update' => 'LBS Update Job',
            'lbs.job.uploadFiles' => 'LBS Upload Files',
            'lbs.job.deleteFile' => 'LBS Delete File',
            'lbs.job.file' => 'LBS Download File',
            'lbs.job.checkerUploads' => 'LBS Checker Uploads',
            'lbs.job.runComment' => 'LBS Run Comment',
            'lbs.job.comment' => 'LBS Comment',
            'lbs.job.archive' => 'LBS Archive Job',
            'lbs.job.restore' => 'LBS Restore Job',
            'lbs.job.sendSlack' => 'LBS Slack Notify',
            'lbs.job.sendSubmissionEmail' => 'LBS Submission Email',
            'lbs.job.emailPreview' => 'LBS Email Preview',
            'lbs.job.sendMailboxEmail' => 'LBS Send Mailbox Email',
        ],
        'BPH' => [
            'bph.add' => 'BPH Add New',
            'bph.list' => 'BPH List',
            'bph.completed' => 'BPH Completed',
            'bph.review' => 'BPH For Review',
            'bph.mailbox' => 'BPH Mailbox',
            'bph.trash' => 'BPH Archive',
            'bph.store' => 'BPH Save Job',
            'bph.view' => 'BPH View Job',
            'bph.update' => 'BPH Update Job',
            'bph.job.uploadFiles' => 'BPH Upload Files',
            'bph.job.deleteFile' => 'BPH Delete File',
            'bph.job.file' => 'BPH Download File',
            'bph.job.checkerUploads' => 'BPH Checker Uploads',
            'bph.job.runComment' => 'BPH Run Comment',
            'bph.job.comment' => 'BPH Comment',
            'bph.job.archive' => 'BPH Archive',
            'bph.job.mergeFile' => 'BPH Download Print Merge File',
            'bph.job.printCompliance' => 'BPH Print Compliance Summary',
            'bph.job.sendSlack' => 'BPH Slack Notify',
            'bph.job.sendSubmissionEmail' => 'BPH Submission Email',
        ],
        'BPH Email' => [
            'bph_client_email.index' => 'BPH Email — List',
            'bph_client_email.create' => 'BPH Email — Create',
            'bph_client_email.store' => 'BPH Email — Save',
            'bph_client_email.edit' => 'BPH Email — Edit',
            'bph_client_email.update' => 'BPH Email — Update',
            'bph_client_email.destroy' => 'BPH Email — Delete',
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
            'bluinq.store' => 'Bluinq Save Job',
            'bluinq.list' => 'Bluinq List',
            'bluinq.completed' => 'Bluinq Completed',
            'bluinq.review' => 'Bluinq For Review',
            'bluinq.mailbox' => 'Bluinq Mailbox',
            'bluinq.trash' => 'Bluinq Archive',
            'bluinq.update' => 'Bluinq Update Job',
            'bluinq.job.sendSlack' => 'Bluinq Slack Notify',
            'bluinq.job.sendSubmissionEmail' => 'Bluinq Submission Email',
            'bluinq.job.emailPreview' => 'Bluinq Email Preview',
            'bluinq.job.sendMailboxEmail' => 'Bluinq Send Mailbox Email',
        ],
        'NH' => [
            'nh.add' => 'NH Add New',
            'nh.store' => 'NH Save Job',
            'nh.list' => 'NH List',
            'nh.completed' => 'NH Completed',
            'nh.review' => 'NH For Review',
            'nh.mailbox' => 'NH Mailbox',
            'nh.trash' => 'NH Archive',
            'nh.update' => 'NH Update Job',
            'nh.job.sendSlack' => 'NH Slack Notify',
            'nh.job.sendSubmissionEmail' => 'NH Submission Email',
            'nh.job.emailPreview' => 'NH Email Preview',
            'nh.job.sendMailboxEmail' => 'NH Send Mailbox Email',
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
            'efficient_living.mailbox' => 'Efficient Living Mailbox',
            'efficient_living.trash' => 'Efficient Living Archive',
            'efficient_living.job.view' => 'Efficient Living View Job',
            'efficient_living.job.update' => 'Efficient Living Update Job',
            'efficient_living.job.restore' => 'Efficient Living Restore Job',
            'efficient_living.job.emailPreview' => 'Efficient Living Email Preview',
            'efficient_living.job.sendMailboxEmail' => 'Efficient Living Send Mailbox Email',
            'efficient_living.job.sendSlack' => 'Efficient Living Slack Notify',
            'efficient_living.job.sendSubmissionEmail' => 'Efficient Living Submission Email',
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
        /*
         * UI-only toggles (no HTTP route): which cards appear on the shared job detail page (LBS / Efficient Living / BPH).
         */
        'Job view (cards)' => [
            'job_view.card.client_details' => 'Job view — Client Details',
            'job_view.card.job_details' => 'Job view — Job Details',
            'job_view.card.notes' => 'Job view — Notes',
            'job_view.card.complexity' => 'Job view — Complexity',
            'job_view.card.plans' => 'Job view — Plans',
            'job_view.card.documents' => 'Job view — Documents',
            'job_view.card.checker_uploads' => 'Job view — Checker uploads',
            'job_view.card.run_comments' => 'Job view — Run comments',
            'job_view.card.comments' => 'Job view — Comments',
            'job_view.card.activity' => 'Job view — Activity log',
            'job_view.card.bph_additional' => 'Job view — BPH Additional Information',
        ],
        'Job' => [
            'compliance.index' => 'Compliance',
            'priority.index' => 'Priority',
            'status.index' => 'Status',
            'job_request.index' => 'Job Request',
            'client.index' => 'Client',
        ],
        'Announcement' => [
            'announcement.index' => 'Announcement List',
            'announcement.create' => 'Announcement Create Page',
            'announcement.store' => 'Announcement Save New',
            'announcement.edit' => 'Announcement Edit Page',
            'announcement.update' => 'Announcement Save Update',
            'announcement.destroy' => 'Announcement Delete',
        ],
        'Branch offices' => [
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

    /*
    |--------------------------------------------------------------------------
    | Permission UI: section cards grouped like the sidebar (Job management, …)
    |--------------------------------------------------------------------------
    */
    'route_display' => [
        ['heading' => null, 'sections' => ['Dashboard']],
        ['heading' => 'Job management', 'sections' => [
            'LBS',
            'BPH',
            'BPH Email',
            'Efficient Living',
            'Bluinq',
            'CSP',
            'NH',
            'LC Home Builder',
            'Leading Energy',
        ]],
        ['heading' => 'Job view & master data', 'sections' => ['Job view (cards)', 'Job', 'Announcement']],
        ['heading' => 'Reports & settings', 'sections' => ['Reports', 'Settings']],
        ['heading' => 'Branches & accounts', 'sections' => ['Branch offices', 'Accounts', 'Account']],
    ],
];
