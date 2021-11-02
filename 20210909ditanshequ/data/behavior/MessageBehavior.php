<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\behavior;

use think\facade\Config;

class MessageBehavior
{
    public function run()
    {
        $codes = Config::get('message.');
        foreach ($codes as $key => $value) {
//            \define($key, $value);
            define($value['code'], $key);
        }
        define('ADMIN', 'admin'); //超级管理员账号
        define('SESSIONADMIN','admin'); //后台session作用域
        define('SESSIONINDEX','index'); //前台session作用域
    }
}