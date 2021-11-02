<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\util;


use data\service\WechatService;
use think\facade\Cache;
use think\facade\Request;

/**
 * 微信工具类   相关信息 在配置文件 app.php 中
 *     'wx_appid'=>'开发者ID(AppID)',
 *     'wx_app_secret'=>'开发者密码(AppSecret)',
 *     'wx_token'=>'令牌(Token)',
 *     'wx_url'=>'https://api.weixin.qq.com/', //通用域名(api.weixin.qq.com)
 * Class WechatTool
 * @package data\util
 */
class WechatTool
{


    /**
     * 公众号服务器配置 入口
     */
    public function wechatCheck()
    {
        if ( !empty(Request::param('echostr')) ) {
            $this->checkSignature(); //验证签名
            exit;
        } else {
            $this->responseMsg(); //公众号 响应消息
        }
    }

    /**
     * 验证签名
     * @return bool
     */
    private function checkSignature()
    {
        $data = Request::param();
        $signature = $data("signature");
        $timestamp = $data("timestamp");
        $nonce = $data("nonce");

        $token = config('wx_token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 发送模版消息
     *      $data=array(
     *            'touser'=>$openid,
     *            'template_id'=>"HHfIBns0fIBkG-SsBVj_otthRi5cB5Ukx6glZHEL2eQ",
     *            'url'=>"http://fy.huidemanage.com",
     *            'topcolor'=>"#7B68EE",
     *            'data'=>$data
     *        );
     * @param $data
     * @return array
     */
    public function send_notice($data){
        $token = $this->get_access_token();

        $data = json_encode($data);

        $url = config('wx_url') . 'cgi-bin/message/template/send?access_token=' . $token;
        $res = $this->post_url($url, $data);

        if ($res['errcode'] == '0') {
            return ['code'=>1,'message'=>'success'];
        } else {
            return ['code'=>-999,'message'=>$res];
        }
    }


    /**
     * 手机端微信授权登录
     */
    public function wapLogin(){   //https://api.weixin.qq.com/sns/userinfo
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".config('wx_appid')."&redirect_uri=".config('redirect_uri')."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header("location:$url");
    }

    /*手机端微信登录*/
    public function getToken($code=''){
        $appid = config('wx_appid');
        $appsecret = config('wx_app_secret');
        if($code==''){
            $code = $_GET['code'];
        }
        //判断是否授权
        if (empty($code)){
            return ['code'=>-1,'message'=>'授权失败'] ;
        }
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code=' . $code . '&grant_type=authorization_code';
        //获取token，为了获取access_token 如果没有就弹出错误
        $token = json_decode(file_get_contents($token_url));
        if (isset($token->errcode)) {
            echo '<h1>错误：</h1>' . $token->errcode;
            echo '<br/><h2>错误信息：</h2>' . $token->errmsg;
            exit;
        }
        $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
        //获取access_token ，为了获取微信的个人信息，如果没有就弹出错误
        $access_token = json_decode(file_get_contents($access_token_url));
        if (isset($access_token->errcode)) {
            echo '<h1>错误：</h1>' . $access_token->errcode;
            echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
            exit;
        }
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
        //获取用户信息
        $user_info = json_decode(file_get_contents($user_info_url));
        if (isset($user_info->errcode)) {
            echo '<h1>错误：</h1>' . $user_info->errcode;
            echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
            exit;
        }
        //这里转换为数组
        $rs = (array)$user_info;
        return ['code'=>1,'message'=>'授权成功','data'=>$rs] ;
//        $openid = $rs['openid'];
//        $this->setUser($rs,$openid);

    }



    /**
     * 获取公众号 登录二维码
     * 去WechatService  验证登录 和 登录之后的逻辑
     * @param array $user_info
     * @return string
     */
    public function get_login_qr($user_info = [])
    {
//        session_start();
        $session_id = session_id();

        Cache::set('login_str_' . $session_id, '0', 3600);

        $scene_str = 'login-' . $session_id;

        $data = [
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_str' => $scene_str //这就是 $object['EventKey']
                ]
            ]
        ];

        $data = json_encode($data);

        $token = $this->get_access_token();
        $url = config('wx_url') . 'cgi-bin/qrcode/create?access_token=' . $token;
        $res = $this->post_url($url, $data);

        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $res['ticket'];
    }


    /**
     * 设置公众号菜单
    $data = array(
        'button' => array(
            array(
                'type' => 'click',
                'name' => '数据报告',
                'key' => 'yest_data'
            ),
            array(
                'type' => 'click',
                'name' => '用户支持1',
                'key' => 'exptime'//EventKey
            )
        )
    );
    $data = '{
        "button":[
            {
                "type":"view",
                "name":"惠德官网",
                "url":"http://www.huidemanage.com/"
            },
            {
                "name":"平台入口",
                "sub_button":[
                    {
                        "type":"view",
                        "name":"招标平台",
                        "url":"http://zb.huidemanage.com"
                    },
                    {
                        "type":"view",
                        "name":"房源平台",
                        "url":"http://fy.huidemanage.com"
                    }
                ]
            },
            {
                "name":"关于惠德",
                "sub_button":[
                    {
                        "type":"view",
                        "name":"关于惠德",
                        "url":"http://www.huidemanage.com/a/qiyejieshao/"
                    },{
                        "type":"view",
                        "name":"人力资源",
                        "url":"http://www.huidemanage.com/a/renliziyuan/"
                    },{
                        "type":"view",
                        "name":"联系我们",
                        "url":"http://www.huidemanage.com/a/lianxiwomen/"
                    }
                ]
            }
        ]
    }';
     * @param $data
     * @return array|string
     */
    public function create_menu($data)
    {
        $token = $this->get_access_token();

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $url = config('wx_url') . 'cgi-bin/menu/create?access_token=' . $token;
        $res = $this->post_url($url, $data);

        if ($res['errcode'] == '0') {
            return 'success';
        } else {
            return array('info' => $res['errmsg']);
        }
    }


    /**
     * 公众号 响应消息
     */
    public function responseMsg()
    {
        $message = $this->xml_to_array(file_get_contents('php://input'));
        $this->receiveEvent($message);
        exit;
    }

    /**
     * 接收事件 消息处理
     * 去WechatService  处理 业务逻辑
     * @param $object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function receiveEvent($object)
    {
        $content = $result = "";
        $key = $object['EventKey'];

        $open_id = $object['FromUserName'];
        $bind = 'false';//是否关注
        switch ($object['Event']) {
            case "subscribe": //关注事件 关注前面自动加 qrscene_      qrscene_login-bbtpalj7o4btcsr5pfagkmd9q0
                $user_id = str_replace('qrscene_', '', $key);
                $content = "欢迎关注惠德管理公众号,";
                $bind = 'true';
                break;
            case "SCAN": //进入公众号事件
                $user_id = $key;
                $content = "欢迎回到惠德管理公众号,";
                $bind = 'true';
                break;
            case "CLICK":
                $content = $this->click($object);
                break;
        }
        //逻辑处理
        if ($bind == 'true' && $user_id <> '') {
            $content .= (new WechatService)->receiveEvent($user_id,$open_id);
        }

        /*返回公众号 信息*/
        if (is_array($content)) {
            foreach ($content as $key => $value) {
                if ($content <> '') {
                    $result = $this->transmitText($object, $value);
                }
            }
        } else {
            if ($content <> '') {
                $result = $this->transmitText($object, $content);
            }
        }
        echo $result;
        exit;
    }

    /**
     * 点击菜单
     * @param $object
     * @return mixed|string
     */
    private function click($object)
    {
        $open_id = $object['FromUserName'];
        $content = '';
        switch ($object['EventKey']) {
            case "exptime":
                $err_arr = array(
                    '100' => '您没有开通这个功能',
                    '201' => '获取您的信息失败',
                    '101' => '没有绑定的网站信息',
                    '102' => '没有绑定的网站信息'
                );
                $content = $err_arr[100];
                break;
        }

        return $content;
    }



    /**
     * 回复文本消息
     * @param $object
     * @param $content
     * @return string
     */
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>  
        <ToUserName><![CDATA[%s]]></ToUserName>  
        <FromUserName><![CDATA[%s]]></FromUserName>  
        <CreateTime>%s</CreateTime>  
        <MsgType><![CDATA[text]]></MsgType>  
        <Content><![CDATA[%s]]></Content>  
        </xml>";
        $result = sprintf($xmlTpl, $object['FromUserName'], $object['ToUserName'], time(), $content);
        return $result;
    }

    /**
     * 回复图文消息
     * @param $object
     * @param $newsArray
     * @return string|void
     */
    private function transmitNews($object, $newsArray)
    {
        if (!is_array($newsArray)) {
            return;
        }
        $itemTpl = "        <item>  
            <Title><![CDATA[%s]]></Title>  
            <Description><![CDATA[%s]]></Description>  
            <PicUrl><![CDATA[%s]]></PicUrl>  
            <Url><![CDATA[%s]]></Url>  
        </item>";
        $item_str = "";
        foreach ($newsArray as $item) {
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>  
        <ToUserName><![CDATA[%s]]></ToUserName>  
        <FromUserName><![CDATA[%s]]></FromUserName>  
        <CreateTime>%s</CreateTime>  
        <MsgType><![CDATA[news]]></MsgType>  
        <ArticleCount>%s</ArticleCount>  
        <Articles>$item_str</Articles>  
        </xml>";

        $result = sprintf($xmlTpl, $object['FromUserName'], $object['ToUserName'], time(), count($newsArray));
        return $result;
    }

    /**
     * 回复音乐消息
     * @param $object
     * @param $musicArray
     * @return string
     */
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>  
        <Title><![CDATA[%s]]></Title>  
        <Description><![CDATA[%s]]></Description>  
        <MusicUrl><![CDATA[%s]]></MusicUrl>  
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>  
        </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>  
        <ToUserName><![CDATA[%s]]></ToUserName>  
        <FromUserName><![CDATA[%s]]></FromUserName>  
        <CreateTime>%s</CreateTime>  
        <MsgType><![CDATA[music]]></MsgType>  
        $item_str  
        </xml>";

        $result = sprintf($xmlTpl, $object['FromUserName'], $object['ToUserName'], time());
        return $result;
    }




    /**
     * 获取accessToken
     * @return mixed
     */
    private function get_access_token()
    {
        if (!Cache::get('access_token')) {
            $content = $this->get_url(config('wx_url') . 'cgi-bin/token?grant_type=client_credential&appid=' . config('wx_appid') . '&secret=' . config('wx_app_secret'));

            Cache::set('access_token', $content['access_token'], ($content['expires_in'] - 200));
            return $content['access_token'];
        } else {
            return Cache::get('access_token');
        }
    }


    /**
     * 将xml 转成数组
     * @param $xml
     * @return mixed
     */
    private function xml_to_array($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }

    /**
     * 根据openid 获取用户信息
     * @param string $openid
     * @return bool|mixed
     */
    public function getUserInfo($openid = '')
    {
        if (!$openid) return false;
        $urlStr = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
        $url = sprintf($urlStr, $this->get_access_token(), $openid);
        $result = $this->get_url($url);

        return $result;
    }

    /**
     * curl_get
     * @param $url
     * @return mixed
     */
    private function get_url($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $return = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($return, true);

        return $return;
    }

    /**
     * curl_post
     * @param $url
     * @param $data
     * @return mixed
     */
    private function post_url($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $return = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($return, true);

        return $return;
    }



}