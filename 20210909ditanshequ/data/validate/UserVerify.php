<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\validate;

use think\Validate;

class UserVerify extends BaseValidate
{
    protected $batch = true;

    protected $rule = [
        'username'=>'require',
        'password'=>'require'
    ];

    protected $message = [
        'username'=>[
            'require'=>['code'=>999,"message"=>'用户名不能为空']
        ],
        'password'=>[
            'require'=>['code'=>999,"message"=>'密码不能为空']
        ]
    ];

    /*场景*/
    protected $scene = [
        'login'  =>  ['username','password'],
    ];

}