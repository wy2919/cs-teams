<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\validate;


class AdminVerify extends BaseValidate
{

    protected $batch = true;

    protected $rule = [
        'id'=>'require',
        'pid'=>'require',
        /*config*/
        'varname'=>'require',
        'info'=>'require',
        'value'=>'require',
        /*methoed*/
        'module_name'=>'require',
        /*role*/
        'role_name'=>'require',
        /*user*/
        'account'=>'require',
        'user_status'=>'require',
        'password'=>'require',
        'real_name'=>'require',
        'user_tel'=>'require',
        'banner_type'=>'require',
        'bannersrc'=>'require',

    ];

    protected $message = [
        'id'=>[ 'require'=>['code'=>999,"message"=>'id不能为空'] ],
        'pid'=>[ 'require'=>['code'=>999,"message"=>'pid不能为空'] ],
        'varname'=>[ 'require'=>['code'=>999,"message"=>'变量名不能为空'] ],
        'info'=>[ 'require'=>['code'=>999,"message"=>'说明不能为空'] ],
        'value'=>[ 'require'=>['code'=>999,"message"=>'内容不能为空'] ],
        'module_name'=>[ 'require'=>['code'=>999,"message"=>'权限名称不能为空'] ],
        'role_name'=>[ 'require'=>['code'=>999,"message"=>'角色名称不能为空'] ],
        'account'=>[ 'require'=>['code'=>999,"message"=>'用户账号不能为空'] ],
        'user_status'=>[ 'require'=>['code'=>999,"message"=>'用户状态不能为空'] ],
        'password'=>[ 'require'=>['code'=>999,"message"=>'用户密码不能为空'] ],
        'real_name'=>[ 'require'=>['code'=>999,"message"=>'管理员姓名不能为空'] ],
        'user_tel'=>[ 'require'=>['code'=>999,"message"=>'管理员手机号不能为空'] ],
        'banner_type'=>[ 'require'=>['code'=>999,"message"=>'图片类型不能为空'] ],
        'bannersrc'=>[ 'require'=>['code'=>999,"message"=>'图片路径不能为空'] ],
    ];

    /*场景*/
    protected $scene = [
        'id'  =>  ['id'],
        'pid'  =>  ['pid'],
        'system_config_add'  =>  ['varname','info','value'],
        'system_config_edit'  =>  ['id','varname','info','value'],
        'system_methoed_add'  =>  ['module_name'],
        'system_methoed_edit'  =>  ['id','module_name'],
        'system_role_add'  =>  ['role_name'],
        'system_role_edit'  =>  ['id','role_name'],
        'system_user_account'  =>  ['account'],
        'system_user_info'  =>  ['id','real_name','user_tel'],
        'system_user_status'  =>  ['id','user_status'],
        'system_user_password'  =>  ['id','password'],
        'banner_add'  =>  ['banner_type','bannersrc'],
        'banner_edit'  =>  ['id','banner_type','bannersrc'],
    ];

}