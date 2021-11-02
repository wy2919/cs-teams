<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 积分
class Jifen extends Base
{


    // 积分排行榜
    public function getRanking()
    {

        // 校验参数
        if ($pd = yz(['token'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 本日开始时间
        $beginToday = strtotime(date('Y-m-d').' 00:00:00');
        // 本日结束时间
        $endToday = strtotime(date('Y-m-d').' 23:59:59');
        // 本月开始时间
        $beginMonth = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y'))));
        // 本月结束时间
        $endMonth = strtotime(date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y'))));


        // 获取全部积分排行
        $list = model('Jifenlog')
            ->with('user')
            ->where('state',2)     // 已经领取积分
            ->where('type',1)       // 加积分
            ->order('sum desc')       // 降序
            ->limit(8)               // 查询前8
            ->field('sum(jifen) as sum,mid')
            ->group('mid')            // 分组查询
            ->select();



        // 获取本日的用户的积分排行
        $Month = model('Jifenlog')
            ->with('user')
            ->where('state',2)     // 已经领取积分
            ->where('type',1)       // 加积分
            ->where('create_time>' . $beginMonth . ' && create_time < ' . $endMonth)  // 获取创建时间
            ->order('sum desc')       // 降序
            ->limit(8)               // 查询前8
            ->field('sum(jifen) as sum,mid')
            ->group('mid')            // 分组查询
            ->select();


        // 获取本日的用户的积分排行
        $Today = model('Jifenlog')
            ->with('user')
            ->where('state',2)     // 已经领取积分
            ->where('type',1)       // 加积分
            ->where('create_time>' . $beginToday . ' && create_time < ' . $endToday)  // 获取创建时间
            ->order('sum desc')       // 降序
            ->limit(8)               // 查询前8
            ->field('sum(jifen) as sum,mid')
            ->group('mid')            // 分组查询
            ->select();
        return json_s([
            'list'=>$list,      // 全部排行榜
            'Month'=>$Month,    // 月排行榜
            'Today'=>$Today,  // 日排行榜
        ]);

    }


    // 我的积分页
    public function getUserJifen()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $user = model('Member')->find(input('UserId'));

        // 获取累计积分
        $sum = model('Jifenlog')->where([['mid','=',input('UserId')],['type','=',1]])->sum('jifen');

        // 积分明细
        $list = model_s('Jifenlog',[['mid','=',input('UserId')],['state','=',2]],2,1)->select();

        // 兑换记录
        $order = model_s('Order',[['mid','=',input('UserId')]],2,1)->with('commodity')->select();


        // 当前用户
        $User = model('Member')->find(input('UserId'));


        for ($i = 0; $i < count($list); $i++) {
            // 1每日一题 2社区活动 3课堂学习 4分享 5步数 6积分兑换
            if ($list[$i]['classify'] == 1){
                $list[$i]['name'] = '每日一题';
            }else  if ($list[$i]['classify'] == 2){
                $list[$i]['name'] = '社区活动';
            }else  if ($list[$i]['classify'] == 3){
                $list[$i]['name'] = '课堂学习';
            }else  if ($list[$i]['classify'] == 4){
                $list[$i]['name'] = '分享';
            }else  if ($list[$i]['classify'] == 5){
                $list[$i]['name'] = '步数';
            }else  if ($list[$i]['classify'] == 6){
                $list[$i]['name'] = '积分兑换';
            }else  if ($list[$i]['classify'] == 7){
                $list[$i]['name'] = '商品发布';
            }
        }

        return json_s([
            'user'=>$user,
            'sum'=>$sum,    // 累计积分
            'list'=>$list,  // 积分明细
            'order'=>$order,  // 兑换记录
            'User'=>$User,  // 用户信息
        ]);

    }


    // 增加临时积分
    public function setTemporary()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId', 'jifen','type','classify'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 增加用户积分，增加到临时未领取积分里
        $Jifenlog = model('Jifenlog')->save([
            'mid' => input('UserId'),
            'state' => 1,                        // 未领取积分
            'type' => input('type'),             // - +
            'classify' => input('classify'),   // 积分类型
            'jifen' => input('jifen'),
        ]);

        if ($Jifenlog) {
            return json_s([], '增加成功！');
        } else {
            return json_s([], '失败！', -1);
        }
    }


    // 领取积分
    public function setJifen()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid', 'jifen'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 检验积分是否已被领取 避免多次点击重复加积分
        if (model('Jifenlog')->find(input('cid'))['state'] == 2) {
            return json_s([], '请勿重复领取！', -1);
        }

        // 闭包事务 异常自动回滚
        return model('Jifenlog')->transaction(function () {

            // 给用户增加积分
            $pd = model('Member')->where('id', input('UserId'))->setInc('jifen', input('jifen'));

            if ($pd) {
                // 改变积分状态为已领取
                $list = model('Jifenlog')->find(input('cid'))->save([
                    'state' => 2
                ]);
                if ($list) {
                    return json_s(model('Member')->find(input('UserId')), '领取成功！');
                } else {
                    return json_s([], '未知错误！', -1);
                }
            } else {
                return json_s([], '未知错误！', -1);
            }
        });
    }


    // 获取首页待领取积分
    public function getJifen()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $list = model_s('Jifenlog', [['mid', '=', input('UserId')], ['state', '=', 1], ['type', '=', 1]]);

        return json_s($list);
    }


}