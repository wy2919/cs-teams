<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */
// 回复信息配置
return [
    1 => [
        'code' => 'SUCCESS',
        'msg'  => '操作成功',
        //...
    ],
    // user信息
    -1000 => [
        'code' => 'USER_LOGIN_VALIDATE_ERROR',
        'msg'  => '用户登入校验不成功',
    ],
    -1001 => [
        'code' => 'ERROR_NO_USER',
        'msg'  => '用户不存在',
    ],
    -1002 => [
        'code' => 'ERROR_USER_START',
        'msg'  => '用户限制登录',
    ],
    -1003 => [
        'code' => 'ERROR_PASSWORD',
        'msg'  => '用户密码错误',
    ],
    -1005 => [
        'code' => 'ERROR_LOGIN_EXCESS_TIME_OUT',
        'msg'  => '登入超过规定次数',
    ],
    // 用户组信息
    -2001 => [
        'code' => 'ERROR_USER_GROUP_REPEAT',
        'msg'  => '用户组名重复'
    ],
    -2002 => [
        'code' => 'ERROR_USER_GROUP',
        'msg'  => '用户组名操作失败'
    ],

    //接口信息
    -3001 => [
        'code' => 'API_NOT_FOUND',
        'msg'  => '接口不存在'
    ],
];
