<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 线下活动
class Activity extends Base
{


    // 社区活动详情
    public function getInfo()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId','cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Activity')->find(input('cid'));

        if (!empty($info)){

            // 获取该用户是否已经报名该活动
            $userPD = model_s('Apply', [['mid', '=', input('UserId')],['cid', '=', input('cid')]], 2)->find();


            if (!empty($userPD)){
                $info['is_Apply'] = 1;   // 已经报名
            }else{
                $info['is_Apply'] = 2;   // 未报名
            }

            // 获取所有报名数
            $info['sum'] = model_s('Apply', [['cid', '=', input('cid')]], 2)->count();

            // 活动是否结束
            if (time() > $info['begin'] && time() < $info['finish']) {
                $info['state'] = 1;  // 未结束
            } else {
                $info['state'] = 2;  // 已结束
            }

            // 随机获取6参与人数头像
            $info['users'] = DB::query('SELECT head_img,name FROM qx_member  where id in (SELECT mid FROM(SELECT distinct mid FROM qx_apply where cid='.input('cid').'  ORDER BY  RAND() LIMIT 3) as apply)');

            $info['begin'] = date('Y-m-d H:m:s',$info['begin']);
            $info['finish'] = date('Y-m-d H:m:s',$info['finish']);

            return json_s($info);

        }else{
            return json_s([],'该活动不存在！',-1);
        }

    }


    // 社区活动 和 我参与的活动数量
    public function getListSum()
    {

        $weekarray = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"];


        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 获取该用户参与的所有活动数量
        $sum = model_s('Apply', [['mid', '=', input('UserId')]], 2)->count();

        // 获取所有活动
        $list = model_s('Activity');

        for ($i = 0; $i < count($list); $i++) {

            // 开始时间
            $xq = $weekarray[date("w", $list[$i]['begin'])];  // 获取星期
            $yue = date("n", $list[$i]['begin']);               // 获取月
            $ri = date('j', $list[$i]['begin']);               // 获取日


            if (time() > $list[$i]['begin'] && time() < $list[$i]['finish']) {
                $list[$i]['state'] = 1;  // 未结束
                $list[$i]['time'] = $yue . '/' . $ri . $xq;
            } else {
                $list[$i]['state'] = 2;  // 已结束
                $list[$i]['time'] = $yue . '/' . $ri . '已结束';
            }

            // 获取总参与人数
            $list[$i]['Sum'] = model('Apply')->where('cid',$list[$i]['id'])->count();

            // 随机获取3参与人数头像
            $list[$i]['imgs'] = DB::query('SELECT head_img FROM qx_member  where id in (SELECT mid FROM(SELECT distinct mid FROM qx_apply where cid='.$list[$i]['id'].'  ORDER BY  RAND() LIMIT 3) as apply)');
        }


        return json_s(['Sum' => $sum, 'list' => $list]);

    }


    // 打卡 线下活动打卡
    public function setApplyState()
    {

        $err = '<div style="font-size: 50px;text-align:center;color: red">无权限</div>';


        // 校验参数
        if ($pd = yz(['openid', 'id'])) {
            if ($pd == 'token') return $err;
            return $err;
        }

        // 获取该用户是否是管理员
        $User = model_s('Member', [['openid', '=', input('openid')]], 2)->find();

        if (empty($User)) {
            // 找不到用户 无权使用
            return $err;
        } else {
            if ($User['administrator'] == 1) {

                // 查询打卡是否已经完成
                $pd = model('Apply')->find(input('id'));
                if (!empty($pd)) {
                    if ($pd['state'] == 2) {
                        return '<div style="font-size: 50px;text-align:center;color: red">已经打卡成功！</div>';
                    }

                    $Apply = model('Apply')->find(input('id'));

                    // 闭包事务 异常自动回滚
                    return model('Member')->transaction(function () use ($Apply) {

                        // 更改打卡状态
                        $save = $Apply->save([
                            'state' => 2  // 打卡成功
                        ]);
                        if ($save) {

                            // 获取该比赛打卡获得的积分数量
                            $jifen = model('Activity')->find($Apply->cid);


                            if (!empty($jifen)) {
                                // 增加用户积分，增加到临时未领取积分里
                                $Jifenlog = model('Jifenlog')->save([
                                    'mid' => $Apply->mid,
                                    'state' => 1,
                                    'type' => 1,
                                    'classify' => 2,
                                    'jifen' => $jifen['jifen'],
                                ]);

                                if ($Jifenlog){
                                    return '<div style="font-size: 50px;text-align:center;color: #40FF00">打卡成功！</div>';
                                }else{
                                    return '<div style="font-size: 50px;text-align:center;color: red">打卡失败！</div>';
                                }

                            } else {
                                return json_s([], '查询不到该比赛！', -1);
                            }
                        }
                    });
                } else {
                    return '<div style="font-size: 50px;text-align:center;color: red">未报名或被删除！</div>';
                }
            } else {
                // 不是管理员 无权使用
                return $err;
            }
        }
    }


    // 报名活动 生成二维码
    public function setApply()
    {

        // 校验参数
        if ($pd = yz(['token', 'cid', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 判断活动是否过期
        $ActivityPD = model('Activity')->find(input('cid'));

        // 判断是否已报名过
        $ApplyPD = model('Apply')->where([['mid', '=', input('UserId')], ['cid', '=', input('cid')]])->find();

        if (!empty($ApplyPD)) {
            return json_s([], '请勿重复报名！', -1);
        }

        if (!empty($ActivityPD)) {

            if (time() > $ActivityPD['begin'] && time() < $ActivityPD['finish']) {

                // 闭包事务 异常自动回滚
                return model('Apply')->transaction(function () {
                    // 新增订单
                    $pd = model('Apply')->save([
                        'mid' => input('UserId'),
                        'cid' => input('cid')
                    ]);


                    if ($pd) {

                        // 获取当前域名
                        $http = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');

                        // 拼接路径 加上当前报名id
                        $url = $http . '/index/Activity/setApplyState?id=' . model('Apply')->id;

                        $QR = $this->QR($url);

                        // 生成二维码
                        $save = model('Apply')->save([
                            'url' => $QR  // 生成订单二维码
                        ]);
                        return json_s(['QR' => $QR], '报名成功！');
                    }

                });

            } else {
                return json_s([], '活动未开始或者已经结束!', -1);
            }

        } else {
            return json_s([], '活动不存在!', -1);
        }

    }
}