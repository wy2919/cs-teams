<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 比赛
class Game extends Base
{

    // 获取分类
    public function getType()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $list = model_s('Type');
        return json_s($list);

    }


    // 发布比赛文章
    public function setArticle()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'title','pid','content','imgs'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Audit')->save([
            'title'=>input('title'),
//            'type'=>input('type'),
            'pid'=>input('pid'),
            'mid'=>input('UserId'),
            'content'=>input('content'),
            'imgs'=>input('imgs'),
        ]);

        if ($info) {
            return json_s([],'发布成功！等待审核！');
        } else {
            return json_s([], '失败！', -1);
        }
    }

    // 比赛文章详情
    public function getArticleInfo()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Audit')->find(input('cid'));

        if (!empty($info)) {

            // 获取当前用户对该文章是否点赞
            $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', input('cid')], ['type', '=', 4]])->find();
            if ($pd) {
                $info['is_Dianzan'] = 2;  // 已点赞
            } else {
                $info['is_Dianzan'] = 1;  // 未点赞
            }

            $info['imgs'] = explode(',', $info['imgs']);


            // 文章详情
            // 获取总评论数量
            $info['sum'] = model_s('Pinglun', [['aid', '=', input('cid')],['type', '=',2]], 2)->with('user')->count();

            // 获取评论
            if ($info['sum'] > 100) {
                // 评论大于100 只显示最新100评论
                $info['Pinglun'] = model_s('Pinglun', [['aid', '=', input('cid')],['type', '=',2]], 2, 1)->limit(100)->select();

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
                $info['Pinglun'] = model_s('Pinglun', [['aid', '=', input('cid')],['type', '=',2]], 2, 1)->with('user')->select();

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
            return json_s([], '不存在！', -1);
        }
    }


    // 比赛详情
    public function getInfo()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Game')->find(input('cid'));

        if ($info) {

            // 获取当前用户是否报名了该比赛（发布文章）
            $PD = model_s('Audit', [['mid', '=', input('UserId')], ['pid', '=', input('cid')]], 2)->find();

            if (!empty($PD)) {
                $info['is_Audit'] = 1;   // 已报名
            } else {
                $info['is_Audit'] = 2;   // 未报名
            }

            $info['finish'] =  date('Y-m-d H:m:s',$info['finish']);

            // 获取最新文章
            $List = model('Audit')
                ->with('user')
                ->where('pid',input('cid'))
                ->where('state', 2)  // 获取审核通过的文章
                ->order('id desc')       // 降序
                ->select();

            for ($i = 0; $i < count($List); $i++) {
                // 随机获取4点赞头像
                $List[$i]['head_imgs'] = DB::query('SELECT head_img FROM qx_member  where id in (SELECT mid FROM(SELECT distinct mid FROM qx_dianzan where type=4&&aid='.$List[$i]['id'].'  ORDER BY  RAND() LIMIT 4) as apply)');

                $List[$i]['imgs'] = explode(',', $List[$i]['imgs']);

                // 获取当前用户对该文章的点赞状态
                $userPD = model_s('Dianzan', [['mid', '=', input('UserId')], ['aid', '=', $List[$i]['id']], ['type', '=', 4]], 2)->find();

                if (!empty($userPD)) {
                    $List[$i]['is_Dianzan'] = 1;   // 已点赞
                } else {
                    $List[$i]['is_Dianzan'] = 2;   // 未点赞
                }

            }
            return json_s(['info' => $info, 'list' => $List]);
        } else {
            return json_s([], '没有该比赛！', -1);
        }
    }

    // 比赛列表
    public function getList()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 获取进行中的比赛
        $List = model('Game')
            ->where('finish>' . time() . ' && begin < ' . time())  // 获取进行中的比赛
            ->order('id desc')       // 降序
            ->select();

        for ($i = 0; $i < count($List); $i++) {
            // 获取分类标签
            $ids = explode(',', $List[$i]['type']);
            $List[$i]['types'] = model_s('Type', [['id', 'in', $ids]]);


            // 获取当前用户是否报名了该比赛（发布文章）
            $PD = model_s('Audit', [['mid', '=', input('UserId')], ['pid', '=', $List[$i]['id']]], 2)->find();

            if (!empty($PD)) {
                $List[$i]['is_Audit'] = 1;   // 已报名
            } else {
                $List[$i]['is_Audit'] = 2;   // 未报名
            }

        }



        return json_s($List);
    }

}