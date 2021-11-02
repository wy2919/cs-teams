<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */
// 白名单 记入不需要校验的权限URL
return [
    'test' =>[
        'index' => [
            'index',
            'execute'
        ]
    ],
    'index' =>[
        'index' => [
            'index',
        ]
    ],
    'admin' =>[
        'login' => [
            'login',
            ''
        ],
        'Base' => [
            'upload',
            ''
        ],
    ],
];