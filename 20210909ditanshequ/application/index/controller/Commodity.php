<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 商品
class Commodity extends Base
{
    // 发布商品
    public function setCommodity()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId', 'name', 'jifen', 'site', 'imgs', 'img', 'content', ''])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }


        $pd = model('Commodity')->save([
            'name' => input('name'),
            'jifen' => input('jifen'),
            'site' => input('site'),
            'imgs' => input('imgs'),
            'img' => input('img'),
            'mid' => input('UserId'),
            'content' => input('content'),
        ]);

        if ($pd) {
            return json_s([], '发布成功！');
        } else {
            return json_s([], '未知错误！', -1);
        }

    }

    // 兑换记录
    public function getLog()
    {
        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 判断该用户是否已经举报过该商品
        $list = model_s('Order', [['mid', '=', input('UserId')]], 2,1)->with('commodity')->select();


        if ($list) {
            return json_s($list);
        } else {
            return json_s([], '未知错误！', -1);
        }
    }


    // 举报商品
    public function getCommodityReport()
    {
        // 校验参数
        if ($pd = yz(['token', 'cid', 'UserId', 'content'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 判断该用户是否已经举报过该商品
        $data = model_s('Report', [['cid', '=', input('cid')], ['mid', '=', input('UserId')]], 2)->find();

        if (!empty($data)) {
            return json_s([], '请勿重复举报！', -1);
        } else {
            $pd = model('Report')->save([
                'cid' => input('cid'),
                'mid' => input('UserId'),
                'content' => input('content'),
            ]);

            if ($pd) {
                return json_s([], '成功');
            } else {
                return json_s([], '未知错误！', -1);
            }
        }
    }

    // 获取商品详情
    public function getCommodityInfo()
    {
        // 校验参数
        if ($pd = yz(['token', 'id'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $list = model('Commodity')->find(input('id'));

        if (!empty($list)) {
            return json_s($list);
        } else {
            return json_s([], '该商品不存在', -1);
        }
    }

    // 随机获取4商品 和 商品列表
    public function getCommodityFour()
    {
        // 校验参数
        if ($pd = yz(['type'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }


        if (input('type') == 1) {
            // 随机获取首页4条商品
            $arr = DB::query('SELECT * FROM qx_commodity where inventory>=1  ORDER BY  RAND() LIMIT 4');

            if (!empty($arr)) {
                return json_s($arr);
            } else {
                return json_s([], '暂时没有商品', -1);
            }
        } else if (input('type') == 2) {
            // 查询所有商品
            $list = model_s('Commodity', [['inventory', '>=', 1]]);
            if (!empty($list)) {
                return json_s($list);
            } else {
                return json_s([], '暂时没有商品', -1);
            }
        }

    }

}