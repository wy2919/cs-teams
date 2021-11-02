<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//Route::group('home',function (){
//    Route::get('/signin', 'home/signin/index');
//});

//Route::domain('fy.huidemanage.com','fangyuan');  // m.class.com 	qx1194fy.nocdn.flyingsteed.com

Route::rule(LOGIN, 'admin/system/login'); //不使用中间件

Route::rule('admin/:controller/:action', 'admin/:controller/:action')->middleware(\app\http\AuthMiddleware::class); //使用中间件  将app\middleware.php 中的注释掉就行
// 特殊路由
Route::rule('admin', 'admin/system/index')->middleware(\app\http\AuthMiddleware::class);

// 后台隐藏index.php路由
Route::rule('/', '/index.php/admin.html');


//Route::group('home',function (){
//    Route::get('/index', 'home/index/index'); //后台页面
//    Route::get('/signin', 'home/Signin/index');//登录页面
//    Route::post('/login', 'home/Signin/login');//登录处理
//    Route::get('/captcha', 'home/Signin/captcha');//验证码
//    Route::get('/setting', 'home/setting/index'); //设置首页
//
//});
//
//
///*管理员管理*/
//Route::group('admin', function () {
//    Route::get('/index','home/admin/index'); //权限首页
//    Route::post('/read','home/admin/read'); //读取用户
//    Route::get('/add','home/admin/add'); //用户添加
//    Route::post('/save','home/admin/save'); //用户添加
//});
//
//
//Route::group('role',function (){
//    Route::get('/index', 'home/Role/index'); //为角色分配权限
//    Route::get('/edit', 'home/Role/edit'); //角色分配权限展示
//    Route::post('/assign', 'home/Role/assignPermission'); //角色分配权限编辑
//
//});
