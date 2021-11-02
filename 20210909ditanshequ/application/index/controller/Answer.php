<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 答题
class Answer extends Base
{

    // 答题前 获取答题机会 和 此题目是否已经答题
    public function getChance()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 本日开始时间
        $beginToday = strtotime(date('Y-m-d') . ' 00:00:00');
        // 本日结束时间
        $endToday = strtotime(date('Y-m-d') . ' 23:59:59');


        // 获取今日剩余答题机会
        $count = model('Dianzan')
            ->where([['mid', '=', input('UserId')], ['type', '=', 8]])
            ->where('create_time>' . $beginToday . ' && create_time < ' . $endToday)  // 获取创建时间
            ->count();
        if ($count >= 3) {
            return json_s([], '今日已经没有答题机会！', -1);
        }

        // 获取该题是否已经答过
        $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', input('cid')], ['type', '=', 8]])->find();
        if ($pd) {
            return json_s([], '该题已答过！', -2);
        }

        return json_s([], '成功！');

    }


    // 答题次数增加
    public function setCount()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 增加记录
        $insert = model('Dianzan')->save([
            'mid' => input('UserId'),
            'aid' => input('cid'),
            'type' => 8,
        ]);

        if ($insert) {
            return json_s([], '成功！');
        } else {
            return json_s([], '失败！', -1);
        }
    }


    // 获取题目列表
    public function getList()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $list = model_s('Answer');


        for ($i = 0; $i < count($list); $i++) {
            // 获取题目是否已答
            $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', $list[$i]['id']], ['type', '=', 8]])->find();

            if ($pd) {
                $list[$i]['is_Dianzan'] = 2; // 已答
            } else {
                $list[$i]['is_Dianzan'] = 1; // 未答
            }
        }

        return json_s($list);
    }


    // 获取题目详情
    public function getInfo()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId','cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 本日开始时间
        $beginToday = strtotime(date('Y-m-d') . ' 00:00:00');
        // 本日结束时间
        $endToday = strtotime(date('Y-m-d') . ' 23:59:59');

        $info = model('Answer')->find(input('cid'));

        // 获取今日剩余答题机会
        $count = model('Dianzan')
            ->where([['mid', '=', input('UserId')], ['type', '=', 8]])
            ->where('create_time>' . $beginToday . ' && create_time < ' . $endToday)  // 获取创建时间
            ->count();
//        if ($count >= 3) {
//            return json_s([], '今日已经没有答题机会！', -1);
//        }
        $info['count'] = 3 - $count;

        return json_s($info);
    }

}