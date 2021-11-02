<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 文章
class Article extends Base
{

    // 文章评论
    public function setComment()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid', 'content', 'type'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $pd = model('Pinglun')->save([
            'aid' => input('cid'),
            'mid' => input('UserId'),
            'type' => input('type'),
            'content' => input('content'),
        ]);


        $info = model('Pinglun')->with('user')->find( model('Pinglun')->id);
        $info['is_Dianzan'] = 1;
        if ($pd) {
            return json_s($info, '评论成功！');
        } else {
            return json_s([], '失败！', -1);
        }
    }


    // 文章视频点赞取消 和 评论点赞取消
    public function setBrowsePraiseDel()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid', 'type'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', input('cid')], ['type', '=', input('type')]])->delete();

        if ($pd) {

            // 文章点赞
            if (input('type') == 1) {
                model('Article')->where('id', input('cid'))->setDec('praise', 1);
            } else if (input('type') == 2) {
                // 文章浏览
                model('Article')->where('id', input('cid'))->setDec('browse', 1);
            } else if (input('type') == 4) {
                // 比赛文章点赞
                model('Audit')->where('id', input('cid'))->setDec('praise', 1);
            } else if (input('type') == 6) {
                // 比赛文章浏览
                model('Audit')->where('id', input('cid'))->setDec('browse', 1);
            } else if (input('type') == 5) {
                // 生活分享文章点赞
                model('Share')->where('id', input('cid'))->setDec('praise', 1);
            } else if (input('type') == 7) {
                //  生活分享文章浏览
                model('Share')->where('id', input('cid'))->setDec('browse', 1);
            }

            return json_s([], '成功！');
        } else {
            return json_s([], '请勿重复操作！', -1);
        }
    }

    // 文章 浏览 点赞 评论点赞
    public function setBrowsePraise()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid', 'type'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }


        // 判断是否已经点赞或者浏览
        $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', input('cid')], ['type', '=', input('type')]])->find();
        if ($pd) {
            return json_s([], '请勿重复操作！', -1);
        }

        // 闭包事务 异常自动回滚
        return model('Dianzan')->transaction(function () {

            // 增加点赞记录
            $insert = model('Dianzan')->save([
                'mid' => input('UserId'),
                'aid' => input('cid'),
                'type' => input('type'),
            ]);


            if ($insert) {

                // 文章点赞
                if (input('type') == 1) {
                    model('Article')->where('id', input('cid'))->setInc('praise', 1);
                } else if (input('type') == 2) {
                    // 文章浏览
                    model('Article')->where('id', input('cid'))->setInc('browse', 1);
                } else if (input('type') == 4) {
                    // 比赛文章点赞
                    model('Audit')->where('id', input('cid'))->setInc('praise', 1);

                    // 点赞人增加积分 只有第一次点才增加 点赞取消再点赞不加积分
                    // 获取比赛文章点赞获赞比例
                    $Praise1 = model('SystemConfig')->where('info', 'cfg_GamePraise1')->find()['value'];
                    $Praise2 = model('SystemConfig')->where('info', 'cfg_GamePraise2')->find()['value'];

                    // 增加点赞积分记录
                    if (!model('Praiselog')->where([['type', '=', input('type')], ['mid', '=', input('UserId')], ['aid', '=', input('cid')]])->find()) {

                        // 增加用户积分，增加到临时未领取积分里
                        model('Jifenlog',1)->save([
                            'mid' => input('UserId'),
                            'state' => 1,                        // 未领取积分
                            'type' => 1,             // - +
                            'classify' => 3,   // 积分类型 课堂学习
                            'jifen' => $Praise1,
                        ]);

                        $mmid = model('Audit')->find(input('cid'))['mid'];
                        // 被点赞的文章发布者获得积分 --------------------
                        model('Jifenlog',1)->save([
                            'mid' => $mmid,
                            'state' => 1,                        // 未领取积分
                            'type' => 1,             // - +
                            'classify' => 3,   // 积分类型 课堂学习
                            'jifen' => $Praise2,
                        ]);


                        $pd = model('Praiselog')->save([
                            'mid' => input('UserId'),
                            'aid' => input('cid'),
                            'type' => input('type'),
                        ]);
                    }

                } else if (input('type') == 6) {
                    // 比赛文章浏览
                    model('Audit')->where('id', input('cid'))->setInc('browse', 1);
                } else if (input('type') == 5) {
                    // 生活分享文章点赞
                    model('Share')->where('id', input('cid'))->setInc('praise', 1);


                    // 点赞人增加积分 只有第一次点才增加 点赞取消再点赞不加积分
                    // 获取比赛文章点赞获赞比例
                    $Praise1 = model('SystemConfig')->where('info', 'cfg_SharePraise1')->find()['value'];
                    $Praise2 = model('SystemConfig')->where('info', 'cfg_SharePraise2')->find()['value'];

                    // 增加点赞积分记录
                    if (!model('Praiselog')->where([['type', '=', input('type')], ['mid', '=', input('UserId')], ['aid', '=', input('cid')]])->find()) {

                        // 增加用户积分，增加到临时未领取积分里
                        model('Jifenlog',1)->save([
                            'mid' => input('UserId'),
                            'state' => 1,                        // 未领取积分
                            'type' => 1,             // - +
                            'classify' => 3,   // 积分类型 课堂学习
                            'jifen' => $Praise1,
                        ]);

                        $mmid = model('Share')->find(input('cid'))['mid'];
                        // 被点赞的文章发布者获得积分 --------------------
                        model('Jifenlog',1)->save([
                            'mid' => $mmid,
                            'state' => 1,                        // 未领取积分
                            'type' => 1,             // - +
                            'classify' => 3,   // 积分类型 课堂学习
                            'jifen' => $Praise2,
                        ]);


                        $pd = model('Praiselog')->save([
                            'mid' => input('UserId'),
                            'aid' => input('cid'),
                            'type' => input('type'),
                        ]);
                    }

                } else if (input('type') == 7) {
                    //  生活分享文章浏览
                    model('Share')->where('id', input('cid'))->setInc('browse', 1);
                }

                return json_s([], '操作成功');
            } else {
                return json_s([], '未知错误！', -1);
            }
        });

    }


    // 文章视频详情
    public function getInfo()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Article')->find(input('cid'));

        if (!empty($info)) {

            if ($info['type'] == 1) {


                // 获取当前用户对该文章是否点赞
                $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', input('cid')], ['type', '=', 1]])->find();
                if ($pd) {
                    $info['is_Dianzan'] = 2;  // 已点赞
                } else {
                    $info['is_Dianzan'] = 1;  // 未点赞
                }


                // 获取总评论数量
                $info['sum'] = model_s('Pinglun', [['aid', '=', input('cid')], ['type', '=', 1]], 2)->with('user')->count();

                // 获取评论
                if ($info['sum'] > 100) {
                    // 评论大于100 只显示最新100评论
                    $info['Pinglun'] = model_s('Pinglun', [['aid', '=', input('cid')], ['type', '=', 1]], 2, 1)->limit(100)->select();

                    // 获取当前用户对该评论的点赞详情
                    for ($j = 0; $j < count($info['Pinglun']); $j++) {

                        $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', $info['Pinglun'][$j]['id']], ['type', '=', 3]])->find();
                        if ($pd) {
                            $info['Pinglun'][$j]['is_Dianzan'] = 2;  // 已点赞
                        } else {
                            $info['Pinglun'][$j]['is_Dianzan'] = 1;  // 未点赞
                        }
                    }

                } else {
                    $info['Pinglun'] = model_s('Pinglun', [['aid', '=', input('cid')], ['type', '=', 1]], 2, 1)->with('user')->select();

                    // 获取当前用户对该评论的点赞详情
                    for ($j = 0; $j < count($info['Pinglun']); $j++) {

                        $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', $info['Pinglun'][$j]['id']], ['type', '=', 3]])->find();
                        if ($pd) {
                            $info['Pinglun'][$j]['is_Dianzan'] = 2;  // 已点赞
                        } else {
                            $info['Pinglun'][$j]['is_Dianzan'] = 1;  // 未点赞
                        }
                    }
                }

                return json_s($info);
            } else {
                // 视频详情
                return json_s($info);
            }

        } else {
            return json_s([], '不存在！', -1);
        }
    }

    // 文章视频列表
    public function getList()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 获取视频和文章
        $list = model_s('Article', [], 2, 1)->select();

        for ($i = 0; $i < count($list); $i++) {

            // 获取分类标签
            $ids = explode(',', $list[$i]['types']);
            $list[$i]['types'] = model_s('Type', [['id', 'in', $ids]]);

        }

        return json_s($list);
    }


    // 搜索文章视频
    public function search()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId','type','title'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 获取视频和文章
        $list = model_s('Article', [['title','like','%'.input('title').'%'],['type','=',input('type')]], 2, 1)->select();

        for ($i = 0; $i < count($list); $i++) {

            // 获取分类标签
            $ids = explode(',', $list[$i]['types']);
            $list[$i]['types'] = model_s('Type', [['id', 'in', $ids]]);

        }

        return json_s($list);
    }

}


