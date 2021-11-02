<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;


class User extends Base
{

    // 提交用户头像
    public function setUserData()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId', 'head_img'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $user = model('Member')->find(input('UserId'))->save([
            'head_img' => input('head_img'),
        ]);

        if (!empty($user)) {
            return $this->json_s($user);
        } else {
            return $this->json_s([], '提交失败', -1);
        }
    }

    // 提交用户实名
    public function setUserSM()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $user = model('Member')->find(input('UserId'))->save([
            'z_phone' => input('z_phone'),
            'z_name' => input('z_name'),
        ]);

        if (!empty($user)) {
            return $this->json_s($user);
        } else {
            return $this->json_s([], '提交失败', -1);
        }
    }


//    // 实名认证
//    public function setAutonym()
//    {
//        // 校验参数
//        if ($pd = yz(['token', 'UserId','z_name','z_phone'])) {
//            if ($pd == 'token') return json_s([], '未登录', -2);
//            return json_s([], '参数 ' . $pd . ' 未传', -1);
//        }
//
//        // 增加用户积分，增加到临时未领取积分里
//        $Jifenlog = model('Member')->find(input('UserId'))->save([
//            'z_name' => input('z_name'),
//            'z_phone' => input('z_phone'),
//        ]);
//
//        if ($Jifenlog){
//            return json_s([],'成功！');
//        }else{
//            return json_s([],'失败！',-1);
//        }
//    }


    // 获取首页联系方式
    public function getSYphone()
    {
        // 获取首页联系方式
        $info = model('SystemConfig')->where('info', 'cfg_phone')->find()['value'];
        return json_s($info);
    }

    // 获取用户信息
    public function getUserInfo()
    {

        // 校验参数
        if ($pd = yz(['openid', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }


        // 获取用户id
        $id = input('UserId');
        $openid = input('openid');

        $list = model('Member')->where([['id', '=', $id], ['openid', '=', $openid]])->find();


        if ($list) {
            if ($list['forbidden'] == 1) {
                return json_s([], '禁止登录', -1);
            }
            return json_s($list);
        } else {
            return json_s([], '用户被删除', -1);

        }


    }

    // 积分排行榜
    public function getRanking()
    {

        // 验证登录
        if ($pd = $this->yz(['token'])) {
            return json(['code' => -1, 'message' => '未登录']);
        }

        // 解密获token得用户id
        $str = $this->unlock_url(input('token'));
        $idd = explode(',', $str)[0];

        $date = date('Y-m-d');  //当前日期
        $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w = date('w', strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $now_start = date('Y-m-d', strtotime("$date -" . ($w ? $w - $first : 6) . ' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $now_end = date('Y-m-d', strtotime("$now_start +6 days"));  //本周结束日期


        $ks = strtotime($now_start);                  // 本周开始日期
        $js = strtotime($now_end) + 24 * 60 * 60;     // 本周结束日期

        // 通过判断本周开始日期和本周结束日期 来获取本周的用户的积分排行

        $list = $this->getModel('Task')->with('user')->where('create_time>' . $ks . ' && create_time < ' . $js)
            ->order('sum desc')
            ->limit(10)
            ->field('sum(sum) as sum,mid')
            ->group('mid')
            ->select();


        // 获取排行榜奖励和规则
        $config = $this->getModel('SystemConfig')->select();


        return $this->json_s(['data' => $list, 'config' => $config]);

    }


}