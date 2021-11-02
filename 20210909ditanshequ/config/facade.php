<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */
return [
    'facade' => [
        \data\facade\Sessions::class => \data\util\Sessions::class,
        \data\facade\TreeUtil::class => \data\util\TreeUtil::class,
        \data\facade\Uploads::class => \data\util\Uploads::class,
        \data\facade\Excels::class => \data\util\Excels::class,
        \data\facade\Sms::class => \data\util\Sms::class,
        \data\facade\WechatTool::class => \data\util\WechatTool::class,
        \data\facade\OnlyLogin::class => \data\provider\OnlyLogin::class,
        \data\facade\Rbac::class => \data\provider\Rbac::class,
        \data\facade\SystemLogs::class => \data\provider\SystemLogs::class,
        \data\facade\Department::class => \data\provider\Department::class,
    ],
    'alias'=>[
        'Sessions' => \data\facade\Sessions::class,
        'TreeUtil' => \data\facade\TreeUtil::class,
        'OnlyLogin' => \data\facade\OnlyLogin::class,
        'Uploads' => \data\facade\Uploads::class,
        'Excels' => \data\facade\Excels::class,
        'Sms' => \data\facade\Sms::class,
        'WechatTool' => \data\facade\WechatTool::class,
        'Rbac' => \data\facade\Rbac::class,
        'SystemLogs' => \data\facade\SystemLogs::class,
        'Department' => \data\facade\Department::class,
    ]
];