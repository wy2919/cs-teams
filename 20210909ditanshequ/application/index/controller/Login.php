<?php

namespace app\index\controller;


use data\wx\errorCode;
use data\wx\wxBizDataCrypt;
use data\model\system\SystemBanner;
use data\service\IndexService;
use data\service\ProjectService;
use data\validate\ParamVerify;
use think\Db;
use think\facade\Request;
use think\facade\Session;

class Login extends Base
{

    /**
     * 获取微信运动
     */
    public function SetExercise()
    {

        // 验证字符串
        if ($pd = $this->yz(['session_key', 'encryptedData', 'iv', 'UserID'])) {
            return json(['code' => -1, 'message' => '参数 ' . $pd . ' 未传']);
        }

        $appid = config('wx_appid');
        $sessionKey = input('session_key');
        $encryptedData = input('encryptedData');
        $iv = input('iv');

        // 解密
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        // json解析
        $data = json_decode($data, true);

        // 本日开始时间
        $beginToday = strtotime(date('Y-m-d') . ' 00:00:00');
        // 本日结束时间
        $endToday = strtotime(date('Y-m-d') . ' 23:59:59');

        // 获取今天的运动步数
        $step = $data['stepInfoList'][count($data['stepInfoList']) - 1]['step'];
//        $step = 6500;

        // 查询记录里有没有今天的步数
        $info = model('Exercise')
            ->where('mid', input('UserID'))
            ->where('create_time>' . $beginToday . ' && create_time < ' . $endToday)  // 获取创建时间
            ->find();

        if ($info) {
            // 有今天的记录就用当前的记录减去获取到的上次的记录 得到步数差

            if ($step <= $info['num']) {
                return json_s($info['num']);
            }

            $difference = $step - $info['num'];  // 获得步数差

            // 计算步数差 兑换为积分 加入到临时积分中

            // 获取系统设置的步数兑换积分比例
            $config = model('SystemConfig')->where('info', 'cfg_step')->find();

            if (!$config) {
                $config['value'] = 0.001;
            }


            $jifen = floor($difference * $config['value']);

            // 步数太少 不购1积分就不添加
            if ($jifen >= 1) {

                $Jifenlog = model('Jifenlog')->save([
                    'mid' => input('UserID'),
                    'state' => 1,
                    'type' => 1,
                    'classify' => 5,
                    'jifen' => $jifen,
                ]);
            }


            // 在把当前步数替换刚才的步数
            model('Exercise')->find($info['id'])->save([
                'num' => $step,
            ]);

            return json_s($step);
        } else {

            // 没有今天的记录就创建
            model('Exercise')->save([
                'mid' => input('UserID'),
                'num' => $step,
            ]);

            return json_s($step);

        }


    }


    /**
     * 设置用户手机号
     */
    public function SetPhone()
    {

        // 验证字符串
        if ($pd = $this->yz(['session_key', 'encryptedData', 'iv', 'UserID'])) {
            return json(['code' => -1, 'message' => '参数 ' . $pd . ' 未传']);
        }

        $appid = config('wx_appid');
        $sessionKey = input('session_key');
        $encryptedData = input('encryptedData');
        $iv = input('iv');

        // 解密
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        // json解析
        $data = json_decode($data, true);

        if ($errCode == 0) {
            // 更新用户的电话号码

            $this->getModel_s('SystemUser', [['idd', '=', input('UserID')]], 2)->update([
                'user_tel' => $data['phoneNumber']
            ]);
            return $this->json_s([], '获取成功', 200);
        } else {
            return $this->json_s([], '获取手机号失败', -1);
        }
    }

    /**
     * 用户登录|注册
     * @return
     */
    public function login()
    {

        // 校验参数
        if ($pd = yz(['openid'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        // 判断有没有该用户
        $list = model_s('Member', [['openid', '=', input('openid')]], 2)->find();


        if (empty($list)) {

            // 还没有该用户，注册
            model('Member')->save([
                'openid' => input('openid'),
                'name' => input('nick_name'),
                'head_img' => input('head_img'),
            ]);

            $user = model('Member')->where([['id', '=', model('Member')->id]])->find();

            // 加密获取token 用于验证登录状态
            $token = lock_url([$user['id'], $user['openid']]);


            // PD 0注册
            return $this->json_s(['token' => $token, 'UserID' => $user['id'], 'PD' => 0]);
        }

        // 把用户的信息拼接起来，加密一下 生成token

        if ($list['forbidden'] == 1) {
            return $this->json_s([], '您已被禁止登录', -1);
        }

        // 加密获取token 用于验证登录状态
        $token = lock_url([$list['id'], $list['openid']]);

        // PD 1登录
        return $this->json_s(['token' => $token, 'UserID' => $list['id'], 'PD' => 1]);
    }


    // 获取openid
    public function getOpenid()
    {
        // 校验参数
        if ($pd = yz(['code'])) {
            if ($pd == 'token') return json_s([], '未登录', -2);
            return json_s([], '参数 ' . $pd . ' 未传', -1);
        }

        $code = input('code');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . config('wx_appid') . "&secret=" . config('wx_app_secret') . "&js_code=" . $code . "&grant_type=authorization_cod";
        $token = $this->curl_get($url);
        $token = json_decode($token);
        return json($token);
    }

    // 获取access_token
    public function getAccessToken()
    {

        //获取access_token
        $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . config('wx_appid') . "&secret=" . config('wx_app_secret');
        $json = $this->httpRequest($access_token);
        $json = json_decode($json, true);
        $ACCESS_TOKEN = $json['access_token'];


        //如果要获取小程序码，请求这个接口
        $qcode = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$ACCESS_TOKEN";
        $param = json_encode(array("page" => "pages/A1-index/index", "scene" => '2'));

        //POST参数
        $result = $this->httpRequest($qcode, $param, "POST");

        //生成二维码
        file_put_contents("qrcode.png", $result);
        //qrcode.png这个就是你生成的二维码图片,可以存到你指定的路径,例如:/update/img/qrcode.png
        $base64_image = "data:image/jpeg;base64," . base64_encode($result);
        echo $base64_image;

    }


    // 初始化今日任务
    public function setTask($idd)
    {


        // 获取今天的时间和所有的签到记录，判断今天是否签到了
        // 当天的零点
        $dayBegin = strtotime(date('Y-m-d', time()));

        // 当天的24
        $dayEnd = $dayBegin + 24 * 60 * 60;


        $data = $this->getModel('Task')->where('mid=' . $idd . ' && create_time>' . $dayBegin . ' && create_time < ' . $dayEnd)->find();

        if (empty($data)) {
            // 创建今天的任务数据
            $this->getModel('Task')->save([
                'mid' => $idd,
            ]);

            // 刚添加的数据肯定是今天的
//            return $this->json_s($this->getModel('Task')->where('id', $this->getModel('Task')->id)->with('user')->find());

        } else {
            // 已经有任务数据了
//            return $this->json_s($data);
        }
    }


}
