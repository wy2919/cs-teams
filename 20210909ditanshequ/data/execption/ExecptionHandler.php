<?php
/**
 * Created by yongjiapei.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 16:43
 * PhpStorm
 */

namespace data\execption;



use Exception;
use think\exception\Handle;
use think\facade\Log;
use think\facade\Request;

class ExecptionHandler extends Handle {

    private $code;
    private $errorCode;
    private $msg;
    private $success;
    //还需要返回客户端当前请求的url

    public function render(Exception $e) {
        $request = Request::instance();
        if($e instanceof BaseExecption){  //返回到客户端
            //如果是自定义的异常
            $this->code = $e->code;
            $this->errorCode = $e->errorCode;
            $this->msg = $e->message;
            $this->success = $e->success;
        }else{  //服务器错误
            if(config('app_debug')){
                return parent::render($e);
            }else{
                /*不返回错误信息  记录日志*/
                $this->code = 500;
                $this->errorCode = -999;
                $this->msg = '服务器内部错误';
                $this->success = false;

                /*记录日志*/
                $this->recordErrorLog($e);

            }
        }

//        获取url
        $result = [
            'message' => $this->msg,
            'code' => $this->errorCode,
            'request_url' => $request->url(),
            'data' => []
        ];
        // 请求异常
        if ( request()->isAjax()) {
            return json($result , $this->code);
        }else{
            return response($this->msg, $this->code);
        }
    }

    private function recordErrorLog(Exception $e){
        Log::record($e->getMessage(),'error');
    }

}