<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\service;


use data\facade\Sessions;
use data\facade\SystemLogs;
use data\facade\WechatTool;
use data\model\xiaoshou\XsUser;
use think\facade\Cache;
use think\facade\Request;

class WechatService extends BaseService
{

    //微信设置token函数
    public function checkToken()
    {
        if (input('echostr') <> '') {
            WechatTool::checkSignature();
            exit;
        } else {
            WechatTool::responseMsg();
        }
    }
    /*关注公众号二维码*/
    public function getLoginQr(){
        WechatTool::get_login_qr();
    }

    /**
     * 扫码关注公众号 后续操作
     * @return string
     */
     public function receiveEvent($user_id,$open_id){
         $uid = explode('-', $user_id)[0]; //login-bbtpalj7o4btcsr5pfagkmd9q0
         $user_info_id = explode('-', $user_id)[1];

         if ($uid == 'login') {
             if (Cache::get('login_str_' . $user_info_id) == '0') {
                 Cache::set('login_str_' . $user_info_id, 1, 3600); //登录成功
                 Cache::set('login_str_' . $user_info_id . '_open_id', $open_id, 3600);
             }
             $content = '扫码成功，即将登录';
         } else{
             $content = '登录失败';
         }

         return $content;
     }

    /**
     * 验证公众号登录  轮询调用这个方法
     * @return mixed
     */
    public function checklogin(){
        session_start();
        $session_id = session_id();

        $res = Cache::get('login_str_' . $session_id);
        if ($res == '1') {
            $data = Request::param();
            $open_id = Cache::get('login_str_' . $session_id . '_open_id');
            $message = '登录成功';
            if(!empty($data['uid'])){
                $message = '绑定成功';
                $result = $this->bindUser($data['uid'],$open_id);
            }else{
                $result = $this->setLoginSessions($open_id);
            }
        } else {
            $result = '0';
        }

        cache('login_str_' . $session_id, NULL);
        cache('login_str_' . $session_id . '_open_id', NULL);

        return  ['code' => 1, 'message' => $message,'data'=>$result];
    }

    /*绑定微信*/
    public function bindUser($uid,$open_id){
        $wxUserInfo = WechatTool::getUserInfo($open_id); // 获取用户 微信公共信息
        XsUser::update([
            'id'=>$uid,
            'nick_name'=>$wxUserInfo['nickname'],
            'user_headimg'=>$wxUserInfo['headimgurl'],
            'wx_openid'=>$wxUserInfo['openid'],
        ]);
    }


    /**
     * 根据openid 获取用户微信信息 判断跳转路径  登录之后的逻辑
     * @param $open_id
     * @return string
     */
     public function setLoginSessions($open_id){
         $wxUserInfo = WechatTool::getUserInfo($open_id); // 获取用户 微信公共信息
         $user = XsUser::where('openid','=',$open_id)->find();
         //如果没有
         if(!$user){
             $wxUserInfo['isxiaoshou'] = "否";
             $umodel=new XsUser();
             $result = $umodel->allowField(true)->save($wxUserInfo);
             if($result){
                 Sessions::setLogin(true,SESSIONINDEX);
                 Sessions::setUserInfo([
                     'name'=>$umodel['nickname'],
                     'headpic'=>$umodel['headimgurl'],
                     'openid'=>$umodel['openid'],
                     'uid'=>$umodel['id'],
                     'isxs'=>$umodel['isxiaoshou'],
                 ],SESSIONINDEX);
                 SystemLogs::logwrite(Sessions::getUserInfo('name',SESSIONINDEX) . "(" . Sessions::getUserInfo('uid',SESSIONINDEX) . ")" . "注册房源微信用户成功", $result);
                 return "/user/userinfo";
             }else{
                 SystemLogs::logwrite(Sessions::getUserInfo('name',SESSIONINDEX) . "(" . Sessions::getUserInfo('uid',SESSIONINDEX) . ")" . "注册房源微信用户失败", $result);
                 return "/index/index";
             }

         }else{
             Sessions::setLogin(true,SESSIONINDEX);
             Sessions::setUserInfo([
                 'name'=>$user['nickname'],//用户姓名
                 'headpic'=>$user['picpath'],//用户头像
                 'openid'=>$user['openid'],//用户openid
                 'uid'=>$user['id'],//用户账号
                 'isxs'=>$user['isxiaoshou'],//用户是否销售
             ],SESSIONINDEX);
            return "/index/index";
         }
     }


    /**
     * 发送模版消息
     * @param $openid
     * @param $data
     */
     public function send_notice($openid,$data){
         $template=array(
             'touser'=>$openid,
             'template_id'=>"HHfIBns0fIBkG-SsBVj_otthRi5cB5Ukx6glZHEL2eQ",
             'url'=>"http://fy.huidemanage.com",
             'topcolor'=>"#7B68EE",
             'data'=>$data
         );

         WechatTool::send_notice($template);
     }

}