<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;


class Order extends Base
{
    // 完成订单 管理员扫描核销二维码
    public function setOrderState()
    {
        $err = '<div style="font-size: 50px;text-align:center;color: red">无权限</div>';


        // 校验参数
        if ($pd = yz(['openid', 'id'])) {
            if ($pd == 'token') return $err;
            return $err;
        }

        // 获取该用户是否是管理员
        $User = model_s('Member',[['openid','=',input('openid')]],2)->find();

        if (empty($User)){
            // 找不到用户 无权使用
            return $err;
        }else{
            if($User['administrator'] == 1){

                // 查询订单是否已经被核销
                $pd = model('Order')->find(input('id'));
                if ($pd){
                    if ($pd['state'] == 2){
                        return '<div style="font-size: 50px;text-align:center;color: red">订单已被核销！</div>';
                    }

                    return model('Member')->transaction(function () use ($pd,$User) {

                        // 更改订单状态
                        $save = model('Order')->where('id',input('id'))->find()->save([
                            'state' =>  2, // 订单完成
                            'hid' => $User['id']   // 核销管理员
                        ]);

                        // 为发布者添加积分
                        $Jifenlog = model('Jifenlog')->save([
                            'mid' => $pd['pid'],
                            'state' => 1,
                            'type' => 1,
                            'classify' =>7,
                            'jifen' => $pd['jifen'],
                        ]);

                        return '<div style="font-size: 50px;text-align:center;color: #40FF00">成功</div>';

                    });


                }else{
                    return '<div style="font-size: 50px;text-align:center;color: red">订单不存在或被删除！</div>';
                }



            }else{
                // 不是管理员 无权使用
                return $err;
            }
        }
    }

    // 兑换商品 生成订单
    public function setOrder()
    {

        // 校验参数
        if ($pd = yz(['token', 'cid', 'UserId', 'jifen'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 判断用户是否有足够的积分
        $UserPD = model('Member')->find(input('UserId'));

        // 判断商品是否有足够的库存
        $CommodityPD = model('Commodity')->find(input('cid'));


        if (!empty($UserPD) && !empty($CommodityPD)) {
            if ($UserPD['jifen'] < input('jifen')) {
                return json_s([], '积分不足！', -1);
            }

            if ($CommodityPD['inventory'] < 1) {
                return json_s([], '商品库存不足！', -1);
            }

            // 闭包事务 异常自动回滚
            return model('Member')->transaction(function () use ($CommodityPD) {

                // 减去用户积分
                $MemberDec = model('Member')->where('id', input('UserId'))->setDec('jifen', input('jifen'));
                if ($MemberDec){
                    // 减去商品库存
                    $CommodityDec = model('Commodity')->where('id', input('cid'))->setDec('inventory', 1);
                    if ($CommodityDec){


                        // 新增订单
                        $pd = model('Order')->save([
                            'mid' => input('UserId'),
                            'cid' => input('cid'),
                            'jifen' => $CommodityPD['jifen'],
                            'pid' => $CommodityPD['mid'],
                        ]);

                        if ($pd){
                            // 获取当前域名
                            $http = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');

                            // 拼接路径 加上订单号
                            $url = $http.'/index/Order/setOrderState?id='. model('Order')->id;

                            $QR = $this->QR($url);


                            // 生成二维码
                            $save = model('Order')->where('id',model('Order')->id)->find()->save([
                                'url' =>  $QR  // 生成订单二维码
                            ]);

                            return json_s(['QR'=>$QR,'id'=>model('Order')->id],'兑换成功');
                        }else{
                            return json_s([], '未知错误！', -1);
                        }
                    }else{
                        return json_s([], '减库存错误！', -1);
                    }
                }else{
                    return json_s([], '减用户积分错误！', -1);
                }
            });


        } else {
            return json_s([], '未知错误', -1);
        }

    }


    // 获取订单详情
    public function getOrderInfo()
    {
        // 校验参数
        if ($pd = yz(['token','id'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Order')->with(['commodity','user'])->find(input('id'));

        if ($info) {
            return json_s($info);
        } else {
            return json_s([], '该订单不存在！', -1);
        }
    }

}