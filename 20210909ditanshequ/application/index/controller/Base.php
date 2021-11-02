<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\index\controller;

require_once 'phpqrcode.php';


use data\facade\Uploads;
use data\model\system\SystemConfig;
use think\Controller;
use think\App;
use think\facade\Request;
use think\Image;


class Base extends Controller
{

    protected $account = '';
    protected $realname = '';
    protected $headpic = '';

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        $this->realname = session('name', '', SESSIONINDEX); ////用户姓名
        $this->headpic = session('headpic', '', SESSIONINDEX); //用户头像
        $this->account = session('account', '', SESSIONINDEX); //用户账号

        $this->assign([
            'config' => (new SystemConfig())->getConfigs(),//获取系统配置
            'userconfig' => (new SystemConfig())->getConfigs(),//获取系统配置
        ]);
    }


    /**
     * @Notes: 生成二维码
     */
    public function QR($str = '')
    {

        $name =  microtime(). '.png';           // 图片名称
        $path = '/QR/' . $name;             // 生成后的图片路径
        $filename = './QR/' . $name;    // 存储图片路径
//
        // 判断存储路径是否存在 不存在就创建
        if (!file_exists('./QR')) {
            mkdir('./QR');
        }


        // 生成二维码
        // 第1个参数$text：二维码包含的内容，可以是链接、文字、json字符串等等；
        // 第2个参数$outfile：默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径；
        // 第3个参数$level：默认为L，这个参数可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)，这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别；
        // 第4个参数$size：控制生成图片的大小，默认为4；
        // 第5个参数$margin：控制生成二维码的空白区域大小；
        // 第6个参数$saveandprint：保存二维码图片并显示出来，$outfile必须传递图片路径；
        \QRcode::png($str, $filename, 'L', 8, 1);

        return $path;
    }

    /**
     * @Notes: 统一返回
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return \think\response\Json
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/5 13:42
     */
    public function json_s($data = [], $msg = 'success', $code = 200)
    {

        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        if ($code != 200) {
            $data = [
                'code' => $code,
                'msg' => $msg,
            ];
            return json($data);
        }

        return json($data);

    }

    /**
     * @Notes:
     * @param $str   被转换的时间戳
     * @param $gs    格式
     * @return string
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/4 14:13
     */
    public function getTime($str, $gs = 'Y-m-d H:i:s')
    {
        return date($gs, $str);
    }


    // 邀请码生成
    function create_invite_code()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d')
            . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            $d = '',
            $f = 0;
            $f < 6;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;
        return $d;
    }

    /**
     * @Notes:
     * @param $name   查询的Model
     * @param array $map where条件，默认查询所有
     * @param int $pd 1查询  2返回对象
     * @return mixed
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/4 16:02
     */
    public function getModel_s($name, $map = [], $pd = 1)
    {
        if ($pd == 1) {
            return $this->getModel($name)->where($map)->select();
        }

        if ($pd == 2) {
            return $this->getModel($name)->where($map);
        }
    }


    /**
     * @Notes: 获取别的Model对象
     * @param $name  Model控制器名字 首字母大写
     * @return mixed
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/4 11:01
     */
    public function getModel($name, $pd = 0)
    {

        if ($pd == 1) {
            $str = '\data\model\\' . $name;
            return new $str;
        }

        if (empty($GLOBALS['Model_Array'])) {
            $GLOBALS['Model_Array'] = [];
        }

        foreach ($GLOBALS['Model_Array'] as $v) {
            foreach ($v as $k => $vv) {
                if ($k == $name) {
                    return $vv;
                }
            }
        }

        $str = '\data\model\\' . $name;
        $obj = new $str;
        array_push($GLOBALS['Model_Array'], [$name => $obj]);

        return $obj;
    }


    /**
     * @Notes: 验证字符串
     * @param $str 可以为字符串或者是数组
     * @return false|mixed|string
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/6 11:21
     */
    public function yz($str)
    {
        if (gettype($str) === 'string') {
            $arr = (explode(",", $str));
            foreach ($arr as $k => $v) {
                if (isset(input()[$v]) && !empty(input()[$v] && input()[$v] != 'undefined')) {
                    continue;
                } else {
                    return $v;
                }
            }
            return false;
        }

        if (gettype($str) === 'array') {
            foreach ($str as $k => $v) {
                if (isset(input()[$v]) && !empty(input()[$v]) && input()[$v] != 'undefined') {
                    continue;
                } else {
                    return $v;
                }
            }
            return false;
        }

    }
    // Get请求
    /*往微信发送信息，获取唯一标识
        后台调用  避免暴露 信息*/
    function curl_get($url, &$httpCode = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        /*不做证书效验，部署在linux环境下请改为true*/
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }

    //curl请求
    public function httpRequest($url, $data = '', $method = 'GET', $headerArray = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }


    //加密函数
    function lock_url($txt, $key = 'str')
    {
        $txt = $txt . $key;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return urlencode(base64_encode($ch . $tmp));
    }

    //解密函数
    function unlock_url($txt, $key = 'str')
    {
        $txt = base64_decode(urldecode($txt));
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) $j += 64;
            $tmp .= $chars[$j];
        }
        return trim(base64_decode($tmp), $key);
    }


    /*
    * 上传
    * */
    public function upfile($path = "file")
    {

        $file = request()->file('file');
        $names = explode('.', $_FILES['file']['name']);
        $name = $names[0] . '.' . $names[count($names) - 1];
        $info = $file->move("./file" . '/' . $path);

//        var_dump($path);

        if ($info) {
            $getSaveName = str_replace("\\", "/", $info->getSaveName());
            $new_path = '/file/' . $path . "/" . $getSaveName;
            $chuli_path = '.' . $new_path;


//            if(strpos($name,'jpg') !== false || strpos($name,'png') !== false || strpos($name,'jpeg') !== false){
//                //压缩处理图片
//                $image = Image::open($chuli_path);
//                // 返回图片的宽度
//                $width = $image->width();
//                // 返回图片的高度
//                $height = $image->height();
//                $image->thumb($width, $height)->save($chuli_path);
//            }
            return $new_path;
            return json(['code' => 200, 'data' => ['name' => $name, 'path' => $new_path]]);


        } else {
            return json(['code' => 999, 'msg' => '失败']);
        }
    }



//    public function upfile(){
//        $type = Request::param('msg');
//
//        // 获取表单上传文件 例如上传了001.jpg
//        $file = request()->file('file');
//
//        // 移动到框架应用根目录/uploads/ 目录下
//        $names = explode('.',$_FILES['file']['name']);
//        $name = $names[0].'.'.$names[count($names)-1];
//
//        $info = $file->move( './uploads/');
//        if($info){
//            $getSaveName=str_replace("\\","/",$info->getSaveName());
//            $path[]=[
//                'code' => 200,
//                'msg' => $type,
//                'names'=>$names,
//                'name'=>$_FILES['file']['name'],
//                'path' =>'/uploads/'.$getSaveName,
//            ];
//        }
//
//        session('upfile',$path);
//        return json($path);
//    }


    /**
     * 上传文件
     * @return mixed
     */
    public function WXupload()
    {
        return Uploads::upfile();
    }


    public function upfiles()
    {


        $path = session('upfile');
        $type = Request::param('msg');

        if (!$path || $type == "reset") {
            $path = [];
        }

        $files = request()->file('file');
        if (is_array($files)) {
            foreach ($files as $file) {
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $file->move('./uploads');
                if ($info) {
                    $getSaveName = str_replace("\\", "/", $info->getSaveName());
                    $path[] = [
                        'msg' => $type,
                        'path' => '/uploads/' . $getSaveName,
                    ];
                }
            }
        }
        session('upfile', $path);
        return json($path);
    }
}