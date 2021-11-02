<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\http;


use data\execption\ParameterException;
use data\facade\OnlyLogin;
use data\facade\Rbac;
use data\facade\Sessions;
use think\facade\Request;
use traits\controller\Jump;

class AuthMiddleware
{
    use Jump;  //引入trait
    public function handle($request, \Closure $next){
//        throw new Exception('你还没有登录或在登录后没有获取权限缓存'); ParameterException

        if (Rbac::checkWhite()) { // 判断用户是否访问的url是在白名单
            return $next($request);
        }

        if (OnlyLogin::onlyCheck()) { // 唯一登入校验
            if(!Sessions::getLogin()){
                return redirect(LOGIN);
            }else  if (Rbac::check()) { // 权限校验
                    return $next($request);
            } else {
                // 如果没有权限就跳转到登入界面 return redirect('你没有权限访问');
//                return response('没有权限');
//                return $this->error('没有权限');//,url('/home/Signin')
                //return redirect($request->module().'/login/login');
                $e = new ParameterException([ 'errorCode'=>-999, 'message' => '没有访问权限' ]);
                throw $e;
            }
        } else {
            return redirect(LOGIN);
        }
    }
}