<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\util;

use think\facade\Session;

/**
 * 工具类 用来
 * 用户缓存 facade代理 SC
 */
class Sessions
{
    /**
     * 用户登录的session key
     */
    CONST LOGIN_MARK_SESSION_KEY = 'LOGIN_MARK_SESSION';
    /**
     * 权限信息
     * @var string
     */
    CONST USER_ROLE_SESSION = 'USER_ROLE_SESSION';
    /**
     * USER用户信息
     * @var string
     */
    CONST USER_INFO_SESSION = 'USER_INFO_SESSION';

    CONST USER_IS_SYSTEM_SESSION = 'USER_IS_SYSTEM_SESSION';
    CONST SYSTEM_CONFIG_SESSION = 'SYSTEM_CONFIG_SESSION';
    CONST USER_PROJECTS_SESSION = 'USER_PROJECTS_SESSION';

    // /**
    //  * 是否设置用户登入的有效时间
    //  * @var string
    //  */
    // CONST CHECK_TIME_SESSION = 'CHECK_TIME_SESSION';
    //
    // private $checkTime = false;

    //---------------------------设置和判断用户的是否登入
    // 设置用户登入token
    public function setLogin($value,$prefix=SESSIONADMIN) {
        Session::set(self::LOGIN_MARK_SESSION_KEY, password_hash($value, 1),$prefix);
    }
    // 判断用户是否登入成功
    public function getLogin($prefix=SESSIONADMIN) {
        return Session::get(self::LOGIN_MARK_SESSION_KEY,$prefix);
    }

    //---------------------------设置用户和获取用户的登入信息
    // 设置用户的信息
    public function setUserInfo($value,$prefix=SESSIONADMIN) {
        Session::set(self::USER_INFO_SESSION, $value,$prefix);
    }
    // 获取用户的信息
    public function getUserInfo($value = null,$prefix=SESSIONADMIN) {
//        Session::get(self::USER_INFO_SESSION);
        $userInfo = Session::get(self::USER_INFO_SESSION,$prefix);
        return ($value) ? $userInfo[$value] : $userInfo;
    }

    //--------------------------判断用户是否为系统管理员
    // 判断用户是否为系统管理员
    public function setIsSystem($value,$prefix=SESSIONADMIN)
    {
        Session::set(self::USER_IS_SYSTEM_SESSION, $value,$prefix);
    }
    // 判断用户是否登入成功
    public function getIsSystem($prefix=SESSIONADMIN)
    {
        return Session::get(self::USER_IS_SYSTEM_SESSION,$prefix);
    }

    //--------------------------设置和获取用户的操作项目id
    // 设置用户的信息
    public function setUserProject($value,$prefix=SESSIONADMIN) {
        Session::set(self::USER_PROJECTS_SESSION, $value,$prefix);
    }
    // 获取用户的信息
    public function getUserProject($prefix=SESSIONADMIN) {
        return Session::get(self::USER_PROJECTS_SESSION,$prefix);
    }


    //--------------------------设置和获取用户的权限
    // 设置用户的信息
    public function setUserRole($value,$prefix=SESSIONADMIN) {
//        var_dump(self::USER_ROLE_SESSION, $value,$prefix);
        Session::set(self::USER_ROLE_SESSION, $value,$prefix);
    }
    // 获取用户的信息
    public function getUserRole($prefix=SESSIONADMIN) {
//        var_dump(Session::get(self::USER_ROLE_SESSION,$prefix));
//        Session::set(self::USER_ROLE_SESSION,null);
//        var_dump(Session::get(self::USER_ROLE_SESSION,$prefix));
        return Session::get(self::USER_ROLE_SESSION,$prefix);
    }

    //--------------------------设置和获取系统配置
    // 设置用户的信息
    public function setConfig($value,$prefix=SESSIONADMIN) {
        Session::set(self::SYSTEM_CONFIG_SESSION, $value,$prefix);
    }
    // 获取用户的信息
    public function getConfig($prefix=SESSIONADMIN) {
        return Session::get(self::SYSTEM_CONFIG_SESSION,$prefix);
    }

    //-------------------------用户退出清空用户缓存信息
    // 退出登入
    public function clear($prefix=SESSIONADMIN) {
//        Session::clear($prefix);
        Session::delete(self::USER_INFO_SESSION,$prefix); //删除用户信息
        Session::delete(self::USER_ROLE_SESSION,$prefix); //删除用户角色信息
        Session::delete(self::USER_PROJECTS_SESSION,$prefix); //删除用户项目id 信息
        Session::delete(self::LOGIN_MARK_SESSION_KEY,$prefix); //删除用户登录标识
        Session::delete(self::USER_IS_SYSTEM_SESSION,$prefix); //是否是管理用户
        Session::delete(self::SYSTEM_CONFIG_SESSION,$prefix); // 系统配置
    }

}