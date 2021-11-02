<?php
/**
 * Created by yongjiapei.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 16:46
 * PhpStorm
 */

namespace data\execption;


use think\Exception;

class BaseExecption extends Exception {
    //HTTP 状态码
    public $code = 400;
    //自定义错误码
    public $errorCode = 10000;

    //错误具体信息
    public $message = '参数错误';

    //是否错误
    public $success = true;


    public function __construct($params = []) {
        if(!is_array($params)){
            return;
//            throw new Exception('参数是数组');
        }
        /*判断有没有 code 键*/
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('message',$params)){
            $this->message = $params['message'];
        }
        if(array_key_exists('errorCode',$params)){
            $this->errorCode = $params['errorCode'];
        }
        if(array_key_exists('success',$params)){
            $this->success = $params['success'];
        }
    }
}