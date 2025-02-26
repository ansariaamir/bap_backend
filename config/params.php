<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'domain' => 'http://localhost:81',
    'module' => '',
    'dev_accounts_count' => 3, // use in project config when developer user add
    'client_domain' => 'test.com', // use in project config when user add
    'min_day_app_hold' => 3, // bo user for process the application
    'max_day_app_hold' => 4, // bo user for process the application
    'bo_users_actions_when_dept' => [
        ['id'=>'revert','name'=>'Revert'],
        ['id'=>'forward','name'=>'Forward'],
        ['id'=>'approve','name'=>'Approved'],
        ['id'=>'reject','name'=>'Reject'],
        // ['id'=>'forward_dept','name'=>'Forward To Department'],
        // ['id'=>'revert_dept','name'=>'Revert To Department'],
        // ['id'=>'dept_revert','name'=>'Department Revert'],
        // ['id'=>'dept_approve','name'=>'Department Approved']
    ],
    'bo_users_actions' => [
        ['id'=>'revert','name'=>'Revert'],
        ['id'=>'forward','name'=>'Forward'],
        ['id'=>'approve','name'=>'Approved'],
        ['id'=>'reject','name'=>'Reject']
    ]
];
