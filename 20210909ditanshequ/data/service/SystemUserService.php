<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\service;


use data\facade\OnlyLogin;
use data\facade\Rbac;
use data\facade\Sessions;
use data\model\system\SystemConfig;
use data\model\system\SystemUser;
use data\model\system\SystemUserRole;
use data\util\log_class\Agent;

class SystemUserService
{
    /**
     * 定义所有的登入方式
     */
    private $loginWay = [
        'id',        //
//        'user_email',
//        'user_tel',
    ];

    public function login($username, $password)    {
        // 一般做登入 先判断用户名是否存在，如存在我们就判断用户的密码是否正确
        foreach ($this->loginWay as $key => $value) {
            $user = SystemUser::where($value, '=', $username)->find();
            if ($user) { // 如果存在就证明是有这个用户
                break;
            }
        }

        // 用户名是否存在
        if (!$user) {   return ERROR_NO_USER; }
        // 用户是否被封
        if ($user->user_status != 1) { return ERROR_USER_START;  }
        // 用户密码是否错误 这个项目已经是用 md5
        // if (!(md5($password) == $user->user_password)) {  return ERROR_PASSWORD; }
        if (!password_verify($password, $user->user_password)) {  return ERROR_PASSWORD; }
        /*哈希加密
         * $string = 'admin';
         * echo password_hash($string,1);
         * password_verify($string,password_hash($string,1));
         */

        // 用户登入成功 保存session
        $this->initLogin($user);
        OnlyLogin::onlyRecord($user->id);//生成token

        return SUCCESS;
    }

    /*登录成功调用*/
    public function initLogin($user){

        Sessions::setLogin(true);// 根据自己安全需求

         if ($user->is_system == 1) { // 这么写的原因
             Sessions::setIsSystem(true);
             Sessions::setUserRole(Rbac::getRoleModule($user->id)); //根据用户id获取角色的权限 id
         } else {
             Sessions::setIsSystem(false);
         }
        /*用户信息*/
        Sessions::setUserInfo([
            'uid'       => $user->id,
            'real_name' => $user->real_name,
            'depart_id' => $user->depart_id,
            'qianming' => $user->qianming, //签名
            'shouyin' => $user->shouyin, //手印
            'is_system' => $user->is_system
        ]);

        /*系统配置*/
        Sessions::setConfig((new SystemConfig())->getConfigs());

        // 用户登入之后信息记入
        $data = [
            'current_login_ip' => request()->ip(),
            'current_login_time' => time(),
            'current_login_type' => Agent::getOs(),
            'last_login_ip' => $user->current_login_ip,
            'last_login_type' => $user->current_login_type,
            'last_login_time' => $user->current_login_time
        ];
        SystemUser::where('id', $user->id)->update($data);
    }

    /**
     * 获取发布招标公共的管理员
     * @return array
     */
    public function getZhaoBiaoUser(){
        $role_id = "4"; //默认是
        $user_ids = SystemUserRole::field('user_id')->where('role_id','in',$role_id)->select()->toArray();
        $ids = '';
        foreach ($user_ids as $v){
            $ids .= $v['user_id'].',';
        }
        $where = [
            ['id','in',substr($ids,0,-1)]
        ];
        return SystemUser::where($where)->field('id,real_name')->select()->toArray();
    }
}