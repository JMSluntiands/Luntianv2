<?php

/*
 * One entry per push to origin/main, newest first.
 * The version number shown in Settings → Version Details is the entry count,
 * so add a new entry at the top on every push.
 */

return [
    [
        'version' => 22,
        'date' => '2026-11-07',
        'changes' => [
            'Completed tabs: Export to Excel (Date, Address, Client, Job Type Request), following the current search and filters.',
            'Completed tabs: Completion Date Range filter (From / To).',
            'Reports: same job type with different spacing (e.g. "2S DB Base Model - …" vs "2S DB Base Model- …") is now grouped into one row.',
            'Job list and Reports filters are remembered per user and restored on your next visit.',
            'Settings: Version Details page.',
        ],
    ],
    [
        'version' => 21,
        'date' => '2026-11-03',
        'changes' => [
            'Unify job tables with LBS layout, default Log Date ascending sort, and in-place row updates without page reload.',
        ],
    ],
    [
        'version' => 20,
        'date' => '2026-10-27',
        'changes' => [
            'Add replies to Bulletin comments.',
        ],
    ],
    [
        'version' => 19,
        'date' => '2026-10-27',
        'changes' => [
            'Allow comments on all Bulletin posts and add comment edit.',
        ],
    ],
    [
        'version' => 18,
        'date' => '2026-10-27',
        'changes' => [
            'Add edit for Bulletin posts.',
        ],
    ],
    [
        'version' => 17,
        'date' => '2026-10-26',
        'changes' => [
            'Auto-add Slack new job branch columns when missing.',
        ],
    ],
    [
        'version' => 16,
        'date' => '2026-10-26',
        'changes' => [
            'Auto-add Slack branch color column when missing.',
        ],
    ],
    [
        'version' => 15,
        'date' => '2026-10-26',
        'changes' => [
            'Rebuild assets for the Slack settings colour pickers.',
        ],
    ],
    [
        'version' => 14,
        'date' => '2026-10-26',
        'changes' => [
            'Slack new-job colours are editable per branch in Slack settings.',
            'Swap BPH and Luntian default colours.',
            'Label the reference as Job Reference Number.',
        ],
    ],
    [
        'version' => 13,
        'date' => '2026-10-26',
        'changes' => [
            'Remove A&M, NH, LC Home Builder and Leading Energy (code, routes, sidebar, permissions, dashboard, reports).',
            'Colour Slack new-job messages per branch.',
            'Hide CSP from the Permission page.',
            'Add SQL to grant admins full permissions.',
        ],
    ],
    [
        'version' => 12,
        'date' => '2026-10-24',
        'changes' => [
            'Raise PHP upload limits via .user.ini.',
        ],
    ],
    [
        'version' => 11,
        'date' => '2026-10-24',
        'changes' => [
            'Slack new-job messages show branch, details and a public job link, with a per-branch checklist.',
            'Paste/drop multiple pictures in job comments and Bulletin.',
            'Remember dashboard chart filters.',
            'Hide A&M.',
            'New notification sound.',
        ],
    ],
    [
        'version' => 10,
        'date' => '2026-10-23',
        'changes' => [
            'Reports: Total Units counts modeled units only.',
        ],
    ],
    [
        'version' => 9,
        'date' => '2026-10-23',
        'changes' => [
            'Sidebar: client logos at icon size with name shown.',
        ],
    ],
    [
        'version' => 8,
        'date' => '2026-10-23',
        'changes' => [
            'Task Database: default to Board view.',
        ],
    ],
    [
        'version' => 7,
        'date' => '2026-10-23',
        'changes' => [
            'GEN EA tables: drop Client Name, show Client Reference.',
            'Sidebar shows logo only when a client logo exists.',
        ],
    ],
    [
        'version' => 6,
        'date' => '2026-10-23',
        'changes' => [
            'Add completion dates, client logo management, Redistar rename, and checked/modeled report.',
        ],
    ],
    [
        'version' => 5,
        'date' => '2026-08-05',
        'changes' => [
            'Add report time spent, branding labels, timesheet stripes, and task view scopes.',
        ],
    ],
    [
        'version' => 4,
        'date' => '2026-08-05',
        'changes' => [
            'Fix production dashboard 500 when attendance tables or routes are missing.',
        ],
    ],
    [
        'version' => 3,
        'date' => '2026-08-04',
        'changes' => [
            'Add attendance clock out, task feed from allocated jobs, and HR/forum tools.',
        ],
    ],
    [
        'version' => 2,
        'date' => '2026-07-29',
        'changes' => [
            'Fix FYRS list Stage update 405 by returning JSON and using POST.',
        ],
    ],
    [
        'version' => 1,
        'date' => '2026-07-29',
        'changes' => [
            'Improve FYRS job workflow and default Compliance to 2022.',
        ],
    ],
];
