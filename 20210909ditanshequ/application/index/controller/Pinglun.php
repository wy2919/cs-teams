<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;


use data\facade\Sms;

use data\model\system\SystemBanner;
use think\facade\Request;

// 评论
class Pinglun extends Base
{




    // 获取问答详情
    public function getWindaInfo()
    {
        // 验证登录
//        if ($pd = $this->yz(['token'])) {
//            return json(['code' => -1, 'message' => '未登录']);
//        }

        // 解密token得用户id
        $str = $this->unlock_url(input('token'));
        $idd = explode(',',$str)[0];

        // 获取一级评论（问答）
        $list = $this->getModel('Pinglun')->where([['type', '=', 3], ['pid', '=', 0], ['id', '=', input('id')]])
            ->with('user')->find();

        // 获取当前用户对当前问答的点赞信息
        $pd = $this->getModel_s('Dianzan',[['mid','=',$idd],['artid','=',$list['id']],['type','=',9]],2)->find();
        if (empty($pd)){
            $list['DZ'] = false;
        }else{
            $list['DZ'] = true;
        }

        // 获取当前用户对当前问答的点赞信息
        $pd = $this->getModel_s('Dianzan',[['mid','=',$idd],['artid','=',$list['id']],['type','=',7]],2)->find();
        if (empty($pd)){
            $list['SC'] = false;
        }else{
            $list['SC'] = true;
        }



        // 获取二级评论
        $list['er'] = $this->getModel_s('Pinglun', [['pid', '=', $list['id']]], 2)
            ->with('user')->select();

        // 获取三级评论
        for ($j = 0; $j < count($list['er']); $j++) {

            // 获取当前用户对二级评论用户的关注信息
            $gz = $this->getModel_s('Fensi',[['myid','=',$idd],['youid','=',$list['er'][$j]['user']['idd']],['type','=',1]],2)->find();
            $list['er'][$j]['pd'] = empty($gz) ? false : true;  // false未关注  true已关注


//            $list['er'][$j]['san'] = $this->getModel_s('Pinglun', [['pid', '=', $list['er'][$j]['id']]], 2)
//                ->with('user')->select();

            // 获取每个评论的点赞数量
            $list['er'][$j]['DianzanSum'] = $this->getModel_s('Dianzan', [['artid', '=', $list['er'][$j]['id']], ['type', '=', 10]], 2)
                ->count();

            // 获取当前用户对该评论的点赞信息
            $PD = $this->getModel_s('Dianzan', [['artid', '=', $list['er'][$j]['id']],['mid', '=', $idd], ['type', '=', 10]], 2)
                ->find();

            $list['er'][$j]['DianzanPD'] = !empty($PD);
        }


        if (empty($list)) {
            return $this->json_s([], '获取失败', -1);
        } else {
            return $this->json_s($list);
        }
    }




    // 获取问答列表
    public function getWinda()
    {

        // 获取所有问答
        $list = $this->getModel('Pinglun')->where([['type', '=', 3], ['state', '=', 2], ['pid', '=', 0]])->order('id desc')
            ->with('user')->select();

        // 遍历获取二级评论数量
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['er'] = $this->getModel_s('Pinglun', [['pid', '=', $list[$i]['id']]], 2)->count();

            // 获取一条二级评论
            $list[$i]['erData'] = $this->getModel_s('Pinglun', [['pid', '=', $list[$i]['id']]], 2)->find();

        }

        if (empty($list)) {
            return $this->json_s([], '获取失败', -1);
        } else {
            return $this->json_s($list);
        }
    }

    // 获取文章 课程评论
    public function getPinglun()
    {



        if ($pd = $this->yz(['id'])) {
            return json(['code' => -1, 'message' => '参数 ' . $pd . ' 未传']);
        }
        // echo var_dump(empty(input('token')));
        if(!empty(input('token'))){
            // 解密获token得用户id
            $str = $this->unlock_url(input('token'));
            $idd = explode(',',$str)[0];
        }


        // 获取所有一级评论
        $list = $this->getModel('Pinglun')->where([['type', '=', input('type')], ['pid', '=', 0],['aid', '=', input('id')]])
            ->with('user')->select();


        // 遍历获取二级评论
        for ($i = 0; $i < count($list); $i++) {
//            $list[$i]['er'] = $this->getModel_s('Pinglun', [['pid', '=', $list[$i]['id']], ['aid', '=', input('id')]],2)
//                ->with('user')->select();
//
//            $list[$i]['pd'] = true;

            // 获取每个评论的点赞数量
            $list[$i]['DianzanSum'] = $this->getModel_s('Dianzan', [['artid', '=', $list[$i]['id']], ['type', '=', 10]], 2)
                ->count();

            if(empty(input('token'))){
                $list[$i]['DianzanPD'] = false;
            }else{
                // 获取当前用户对该评论的点赞信息
                $PD = $this->getModel_s('Dianzan', [['artid', '=', $list[$i]['id']],['mid', '=', $idd], ['type', '=', 10]], 2)
                    ->find();

                $list[$i]['DianzanPD'] = !empty($PD);
            }

        }

        if (empty($list)) {
            return $this->json_s([], '获取失败', -1);
        } else {
            return $this->json_s($list);
        }

//        if ($pd = $this->yz(['id'])) {
//            return json(['code' => -1, 'message' => '参数 ' . $pd . ' 未传']);
//        }
//
//        // 解密获token得用户id
//        $str = $this->unlock_url(input('token'));
//        $idd = explode(',',$str)[0];
//
//        // 获取所有一级评论
//        $list = $this->getModel('Pinglun')->where([['type', '=', input('type')], ['pid', '=', 0],['aid', '=', input('id')]])
//            ->with('user')->select();
//
//
//        // 遍历获取二级评论
//        for ($i = 0; $i < count($list); $i++) {
////            $list[$i]['er'] = $this->getModel_s('Pinglun', [['pid', '=', $list[$i]['id']], ['aid', '=', input('id')]],2)
////                ->with('user')->select();
////
////            $list[$i]['pd'] = true;
//
//            // 获取每个评论的点赞数量
//            $list[$i]['DianzanSum'] = $this->getModel_s('Dianzan', [['artid', '=', $list[$i]['id']], ['type', '=', 10]], 2)
//              ->count();
//
//            // 获取当前用户对该评论的点赞信息
//            $PD = $this->getModel_s('Dianzan', [['artid', '=', $list[$i]['id']],['mid', '=', $idd], ['type', '=', 10]], 2)
//                ->find();
//
//            $list[$i]['DianzanPD'] = !empty($PD);
//        }
//
//        if (empty($list)) {
//            return $this->json_s([], '获取失败', -1);
//        } else {
//            return $this->json_s($list);
//        }
    }


    // 发布评论
    public function setPinglun()
    {
        // 验证登录
        if ($pd = $this->yz(['token'])) {
            return json(['code' => -1, 'message' => '未登录']);
        }
        if ($pd = $this->yz(['contentData'])) {
            return json(['code' => -1, 'message' => '请输入评论内容']);
        }

        // 解密获token得用户id
        $str = $this->unlock_url(input('token'));
        $idd = explode(',', $str)[0];


        $list = $this->getModel('Pinglun')->save([
            'type' => input('type'),
            'aid' => input('aid'),  // 文章id
            'pid' => input('pid'),  // 上级评论id
            'uid' => $idd,           // 评论用户
            'content' => input('contentData'),  // 评论内容
        ]);

        if (empty($list)) {
            return $this->json_s([], '评论失败', -1);
        } else {
            $info = $this->getModel('Pinglun')->where('id',$this->getModel('Pinglun')->id)->with('user')->find();

            // 获取每个评论的点赞数量
            $info['DianzanSum'] = $this->getModel_s('Dianzan', [['artid', '=', $info['id']], ['type', '=', 10]], 2)
                ->count();

            // 获取当前用户对该评论的点赞信息
            $PD = $this->getModel_s('Dianzan', [['artid', '=', $info['id']],['mid', '=', $idd], ['type', '=', 10]], 2)
                ->find();

            $info['DianzanPD'] = !empty($PD);


            // 获取当前用户对二级评论用户的关注信息
            $gz = $this->getModel_s('Fensi',[['myid','=',$idd],['youid','=',$idd],['type','=',1]],2)->find();
            $info['pd'] = empty($gz) ? false : true;  // false未关注  true已关注



            return $this->json_s($info, '评论成功');
        }
    }


}