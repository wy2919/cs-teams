<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\http;

use think\facade\Log;
use think\facade\Session;

/**
 * 规避短时间重复请求
 */
class RepetitionMiddleware
{
    public function handle($request, \Closure $next){

        // 当前时间  - 上次请求的时间 < 3
        // 第一次是没有的 上次请求的时间 最后请求的时间 , 根据当前时间请求的这个时间
        $lastTime = (Session::get('LAST_TIME')) ? Session::get('LAST_TIME') : 0;
        $nowTime = time();

        if ($nowTime - $lastTime < 1) {
            Log::write(' 请求频率了 过多 ');
//            return response(''); //不做任何操作
            return $next($request);
        } else {
            Session::set('LAST_TIME', $nowTime);
            return $next($request);
        }
    }
}