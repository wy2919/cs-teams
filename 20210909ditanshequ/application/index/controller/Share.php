<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

use think\Db;

// 低碳生活分享
class Share extends Base
{


    // 低碳生活文章列表
    public function getList()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $list = model_s('Share', [], 2, 1)->with('user')->limit(200)->select();


        for ($i = 0; $i < count($list); $i++) {

            $list[$i]['imgs'] = explode(',', $list[$i]['imgs']);

            // 随机获取4点赞头像
            $list[$i]['head_imgs'] = DB::query('SELECT head_img FROM qx_member  where id in (SELECT mid FROM(SELECT distinct mid FROM qx_dianzan where type=5&&aid='.$list[$i]['id'].'  ORDER BY  RAND() LIMIT 4) as apply)');


            // 获取当前用户对该文章的点赞状态
            $userPD = model_s('Dianzan', [['mid', '=', input('UserId')], ['aid', '=', $list[$i]['id']], ['type', '=', 5]], 2)->find();
            if (!empty($userPD)) {
                $list[$i]['is_Dianzan'] = 1;   // 已点赞
            } else {
                $list[$i]['is_Dianzan'] = 2;   // 未点赞
            }
        }



        return json_s($list);


    }


    // 详情
    public function getInfo()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'cid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Share')->find(input('cid'));

        if ($info) {

            // 获取当前用户对该文章是否点赞
            $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', input('cid')], ['type', '=', 5]])->find();
            if ($pd) {
                $info['is_Dianzan'] = 2;  // 已点赞
            } else {
                $info['is_Dianzan'] = 1;  // 未点赞
            }


            $info['Pinglun'] = model_s('Pinglun', [['aid', '=', input('cid')], ['type', '=', 3]], 2, 1)->with('user')->select();

            $info['imgs'] = explode(',', $info['imgs']);

            // 获取总评论数量
            $info['sum'] = model_s('Pinglun', [['aid', '=', input('cid')],['type', '=',3]], 2)->with('user')->count();



            // 获取当前用户对该评论的点赞详情
            for ($j = 0; $j < count($info['Pinglun']); $j++) {

                $pd = model('Dianzan')->where([['mid', '=', input('UserId')], ['aid', '=', $info['Pinglun'][$j]['id']], ['type', '=', 3]])->find();
                if ($pd) {
                    $info['Pinglun'][$j]['is_Dianzan'] = 2;  // 已点赞
                } else {
                    $info['Pinglun'][$j]['is_Dianzan'] = 1;  // 未点赞
                }
            }

            return json_s($info);

        } else {
            return json_s([], '该文章不存在！', -1);
        }

    }


    // 发布用户分享文章
    public function setArticle()
    {

        // 校验参数
        if ($pd = yz(['token', 'UserId', 'title','content','imgs'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $info = model('Share')->save([
            'title'=>input('title'),
//            'type'=>input('type'),
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

}