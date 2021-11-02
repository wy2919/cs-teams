<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/*定义命名空间  app命名空间定义：think.php  initialize方法
// 注册应用命名空间    app             ../application
Loader::addNamespace($this->namespace, $this->appPath);
*/
use think\Loader;
Loader::addNamespace('data', Loader::getRootPath() . 'data' . DIRECTORY_SEPARATOR);


/**
 * @Notes: 验证字符串
 * @param $str 可以为字符串或者是数组
 * @return false|mixed|string
 * @author: 洋 <2069644919@qq.com>
 * @Time: 2021/8/6 11:21
 */
function yz($str)
{
    if (gettype($str) === 'string'){
        $arr = (explode(",",$str));
        foreach ($arr as $k=>$v){
            if (isset(input()[$v]) && !empty(input()[$v] && input()[$v] != 'undefined')){
                continue;
            }else{
                return $v;
            }
        }
        return false;
    }

    if (gettype($str) === 'array'){
        foreach ($str as $k=>$v){
            if (isset(input()[$v]) && !empty(input()[$v]) && input()[$v] != 'undefined' ){
                continue;
            }else{
                return $v;
            }
        }
        return false;
    }

}




/**
 * @Notes: 判断是否包含(1,2,3)
 * @param $str  被包含
 * @param $arr  1,2,3
 * @param $s   用做分割的字符串
 * @return bool
 * @author: 洋 <2069644919@qq.com>
 * @Time: 2021/8/4 10:19
 */
function is_isInclude($str,  $arr,$s = ',')
{
    $arr = explode($s,$arr);
    foreach ($arr as $item) {
        if ($item == $str){
            return true;
        }
    }
    return false;
}

/**
 * @Notes: 统一返回接口
 */
function json_s($data = [], $msg = 'success',  $code = 200)
{

    $data = [
        'code'=> $code,
        'msg' => $msg,
        'data'=> $data
    ];
    if ($code != 200){
        $data = [
            'code'=> $code,
            'msg' => $msg,
        ];
        return json($data);
    }

    return json($data);

}

// 加密函数
function lock_url($txt,$key='str'){


    $txt = join($txt, ',');

    $txt = $txt.$key;
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    $nh = rand(0,64);
    $ch = $chars[$nh];
    $mdKey = md5($key.$ch);
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);
    $txt = base64_encode($txt);
    $tmp = '';
    $i=0;$j=0;$k = 0;
    for ($i=0; $i<strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
        $tmp .= $chars[$j];
    }
    return urlencode(base64_encode($ch.$tmp));
}
//解密函数
function unlock_url($txt,$key='str'){
    $txt = base64_decode(urldecode($txt));
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    $ch = $txt[0];
    $nh = strpos($chars,$ch);
    $mdKey = md5($key.$ch);
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);
    $txt = substr($txt,1);
    $tmp = '';
    $i=0;$j=0; $k = 0;
    for ($i=0; $i<strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
        while ($j<0) $j+=64;
        $tmp .= $chars[$j];
    }
    return trim(base64_decode($tmp),$key);
}

/**
 * @Notes:
 * @param $name   查询的Model
 * @param array $map  where条件，默认查询所有
 * @param int $pd     1查询  2返回对象
 * @return mixed
 * @author: 洋 <2069644919@qq.com>
 * @Time: 2021/8/4 16:02
 */
function model_s($name,$map = [],$pd = 1,$order = 0)
{


    if ($pd == 1){
        if ($order){
            return model($name)->where($map)->order('id desc')->select();
        }
        return model($name)->where($map)->select();
    }

    if ($pd == 2){
        if ($order){
            return model($name)->where($map)->order('id desc');
        }
        return model($name)->where($map);
    }
}


/**
 * @Notes: 获取别的Model对象
 * @param $name  Model控制器名字 首字母大写
 * @return mixed
 * @author: 洋 <2069644919@qq.com>
 * @Time: 2021/8/4 11:01
 */
function model($name,$pd = 0)
{
    $path = '\data\model\\';

    if ($pd == 1){
        $str =  $path.$name;;
        return new $str;
    }

    if (empty($GLOBALS['Model_Array'])){
        $GLOBALS['Model_Array'] = [];
    }

    foreach ($GLOBALS['Model_Array'] as $v){
        foreach ($v as $k=>$vv){
            if ($k == $name){
                return $vv;
            }
        }
    }

    $str =  $path.$name;;
    $obj = new $str;
    array_push($GLOBALS['Model_Array'], [$name=>$obj]) ;

    return $obj;
}


//
///**
// * @Notes: 获取别的Model对象
// * @param $name  Model控制器名字 首字母大写
// * @return mixed
// * @author: 洋 <2069644919@qq.com>
// * @Time: 2021/8/4 11:01
// */
//function model($name,$pd = 0)
//{
//
//    if ($pd == 1){
//        $str = '\data\model\\'.$name;
//        return new $str;
//    }
//
//    if (empty($GLOBALS['Model_Array'])){
//        $GLOBALS['Model_Array'] = [];
//    }
//
//    foreach ($GLOBALS['Model_Array'] as $v){
//        foreach ($v as $k=>$vv){
//            if ($k == $name){
//                return $vv;
//            }
//        }
//    }
//
//    $str = '\data\model\\'.$name;
//    $obj = new $str;
//    array_push($GLOBALS['Model_Array'], [$name=>$obj]) ;
//
//    return $obj;
//}




/**
 * 规定回复信息的格式的方法
 * @param  integer $code 信息码
 * @param  array  $data [description]
 * @return [type]       [description]
 */
function ajaxRuturn($code, $data = []){
    $result = ['code' => $code, 'message' => getMessage($code)];
    $result = (!empty($data)) ? $result['data'] = $data : $result;
    return $result;
}
/**
 * 获取信息码的稍息
 * @param  integer  $code信息码
 * @return string   信息对应的消息
 */
function getMessage($code){
    $info = config('message.');
    return (array_key_exists($code, $info)) ? $info[$code]['msg'] : '操作失败';
}

/**
 * 获取字符串
 * @param array $list
 * @param string $field
 * @return bool|string
 */
function getIdsStr($list=[],$field='id'){
    $Ids = '';
    foreach ($list as $v){
        $Ids .= $v[$field].',';
    }
    if($Ids != ''){
        $Ids = substr($Ids,0,-1);
    }
    return $Ids;
}


/**
 * 创建子节点树形数组
 * 参数
 * $ar 数组，邻接列表方式组织的数据
 * $id 数组中作为主键的下标或关联键名
 * $pid 数组中作为父键的下标或关联键名
 * 返回 多维数组
 **/
function find_child($ar, $id='id', $pid='pid') {

    foreach($ar as $v){
        $tree[$v[$id]] = $v;
        if(empty($tree[$v[$id]]['permission']) ){
            unset($tree[$v[$id]]['permission']);
        }
    }
    foreach ($tree as $k => $item){
        if($item[$pid]) {
            $tree[$item[$pid]]['child'][$item[$id]] =$tree[$k];
            unset($tree[$k]);
        }
    }
    return $tree;
}


/*截取事件格式的 年月日*/
function getDateTime($v){
    return substr($v,0,10);
}

/*判断投标供应商的状态*/
function getOrderStatus($v){
    switch ($v){
        case '通过':
            return '<a href="javascript:">已通过</a>';
            break;
        case '淘汰':
            return '<a href="javascript:">被淘汰</a>';
            break;
        case '待定':
            return '<a href="javascript:" class="colorindex clicktc_order">待定修改</a>';
            break;
        default:
            return '<a href="javascript:">已投</a>';
            break;
    }
}

/*判断审批人的的状态*/
function getShenpiDetailStatus($v){
    switch ($v){
        case 1:
            return '<span class="layui-badge layui-bg-blue">待我审批</span>';
            break;
        case 2:
            return '<span class="layui-badge layui-bg-orange">待审批</span>';
            break;
        case 3:
            return '<span class="layui-badge layui-bg-green">通过</span>';
            break;
        case 4:
            return '<span class="layui-badge">驳回</span>';
            break;
    }
}


/*
 * 判断是否手机端（网上找的方法）
 * @auhor Mr.Lv 3063306168@qq.com
 */
function checkWap(){
    if(isset($_SERVER['HTTP_VIA'])){
        // 先检查是否为wap代理，准确度高
        if(stristr($_SERVER['HTTP_VIA'],"wap")){
            return true;
        }
        // 检查浏览器是否接受 WML.
        elseif(strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0){
            return true;
        }
        //检查USER_AGENT
        elseif(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
        else{
            return false;
        }
    }else{
        if(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
        else{
            return false;
        }
    }
}


/**
 * 二维数据 根据某个键的值去重
 * @param $arr
 * @param $key
 * @return array
 */
function assoc_unique($arr, $key) {

    $tmp_arr = array();
    foreach ($arr as $k => $v) {
        if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            unset($arr[$k]);
        } else {
            $tmp_arr[] = $v[$key];
        }
    }
    foreach ($tmp_arr as $k=>$v){
        $tmp_arr[$k] = [$key=>$v];
    }
    return $tmp_arr;
}


/*随机数字和字母组合*/
function getRandomString($len, $chars=null){
    if (is_null($chars)){
        $chars = "0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

/**
 * 单选按钮组件
 * @param string $t 标题
 * @param string $n 字段
 * @param string $v 事件
 * @param string $val 结果
 * @param string $a 附加内容
 * @return string 返回结果
 */
function make_input($t, $n, $val = "", $v = 'required', $a = '')
{

    $str = '<div class="layui-form-item">
                    <label class="layui-form-label">'. $t .'：</label>
                    <div class="layui-input-block">';
    $str .= '<input type="text" id="'. $n .'" name="'. $n .'" class="layui-input" autocomplete="off" lay-verify="'. $v
        .'" value="'. $val .'" '. $a .' placeholder="请输入'. $t .'">';
    $str .= '</div>
                </div>';
    return $str;
}


/**
 * 上传文件组件
 * @param string $remark 组件名称
 * @param string $field 字段名
 * @param string|array $path 文件路径
 * @return string 返回结果
 */
function make_uploadFile($remark, $field = "img", $path = [])
{
    $str = "";
    $val = "";
    if(!empty($path)){
        if(!is_array($path)) $path = explode(',', $path);

        foreach($path as $v){
            $str .= "<img src='". $v ."' width='200px'>";
            $val .= $v . ",";
        }
        $val = substr($val, 0, strlen($val)-1);
    }
    return '
            <div class="layui-form-item">
                <label class="layui-form-label">上传'. $remark .'：</label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                        <input type="hidden" name="'. $field .'" id="'. $field .'" value="'. $val .'">
                        <input type="hidden" name="filename" id="filename" value="'. $val .'">
                        <button type="button" class="layui-btn '. ($val ? 'layui-btn-normal' : '') .' uploadFile" data-id="'. $field .'">'. ($val ? '已上传,点击重传' : $remark) .'</button>
                    </div>
                </div>
            </div>
        ';
}
