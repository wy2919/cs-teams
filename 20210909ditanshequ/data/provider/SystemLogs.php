<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\provider;


use data\facade\Sessions;
use data\util\log_class\Agent;
use data\util\log_class\Ip;
use think\Db;
use think\facade\Request;

class SystemLogs
{

    /**
     * 写入日志
     * @param $content  写入内容
     */
    public function logwrite($content,$res=''){
        $log['uid'] = Sessions::getUserInfo('uid');
        $log['content'] = $content;
        $log['ip'] = request()->ip(); //ip地址
        $log['location'] = implode(" ", Ip::find($log['ip'])); //地区
        $log['browser'] = Agent::getBroswer();     //浏览器
        $log['os'] = Agent::getOs();               //系统
        $log['url'] = Request::instance()->url(true);//访问路径
        $log['create_time'] = time();

        Db::name('system_log')->insert($log);

        if($res){
            return json(['code'=>1,'message'=>$content]);
        }else{
            return json(['code'=>-999,'message'=>$content]);
        }
    }
}