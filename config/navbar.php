<?php

return [
    'menu' => [
        [
            'name' => 'Home',
            'route' => 'homeindex',
            'pattern' => '/', // Typo diperbaiki
            'icon' => 'typcn typcn-home-outline',
            'role' => ['adm', 'krw']
        ],
        [
            'name' => 'Data Tabungan',
            'route' => 'pengajuan.index',
            'pattern' => '/pengajuan',
            'icon' => 'typcn typcn-book',
            'role' => ['adm', 'krw'],
            'subitems' => [
                [
                    'name' => 'Data Peserta Tabungan',
                    'route' => 'pengajuan.index',
                    'pattern' => '/pengajuan',
                    'role' => ['adm']
                ],
                [
                    'name' => 'Approval Pendaftaran',
                    'route' => 'validate.index',
                    'pattern' => '/validate',
                    'role' => ['adm']
                ],
                [
                    'name' => 'Approval Pengajuan',
                    'route' => 'tabungan.inbox',
                    'pattern' => '/tabungan/inbox',
                    'role' => ['adm', 'krw']
                ],
                ]
        ],
        [
            'name' => 'Reference',
            'route' => 'homeindex',
            'pattern' => 'homeindex',
            'icon' => 'typcn typcn-folder',
            'role' => ['adm'],
            'subitems' => [
                [
                    'name' => 'Company',
                    'route' => 'company.index',
                    'pattern' => '/company',
                ],
                [
                    'name' => 'Employee',
                    'route' => 'employee.index',
                    'pattern' => '/employee',
                ],
                [
                    'name' => 'Program',
                    'route' => 'program.index',
                    'pattern' => '/program',
                ],
                [
                    'name' => 'User',
                    'route' => 'users.index',
                    'pattern' => '/users',
                ],
                [
                    'name' => 'Email Configuration',
                    'route' => 'email.index',
                    'pattern' => '/email/configuration',
                ],
                [
                    'name' => 'Term & Condition',
                    'route' => 'sk-add',
                    'pattern' => '/termandcondition',
                ],
            ]
            ],
    ]
];
