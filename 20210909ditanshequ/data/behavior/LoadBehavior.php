<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\behavior;


use think\Facade;
use think\facade\Config;
use think\Loader;

class LoadBehavior
{
    public function run(){
        // 如果不知道 请看think\base.php对于核心类库文件的代理和别名注册
        // facade注册
        Facade::bind(Config::get('facade.facade'));
        // 别名注册
        Loader::addClassAlias(Config::get('facade.alias'));
    }
}