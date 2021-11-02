<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 刘志淳 <chun@engineer.com>
// +----------------------------------------------------------------------

namespace data\console;

use think\console\command\Make;
use think\console\input\Option;
use think\facade\Config;

class AdminBaseController extends Make
{

    protected $type = "AdminBaseController";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:adminbasecontroller')
            ->addOption('api', null, Option::VALUE_NONE, 'Generate an api adminbasecontroller class.')
            ->addOption('plain', null, Option::VALUE_NONE, 'Generate an empty adminbasecontroller class.')
            ->setDescription('Create a new resource adminbasecontroller class');
    }

    protected function getStub()
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;

        if ($this->input->getOption('api')) {
            return $stubPath . 'adminbasecontroller.api.stub';
        }

        if ($this->input->getOption('plain')) {
            return $stubPath . 'adminbasecontroller.plain.stub';
        }

        return $stubPath . 'adminbasecontroller.stub';
    }

    protected function getClassName($name)
    {
        return parent::getClassName($name) . (Config::get('adminbasecontroller_suffix') ? ucfirst(Config::get('url_adminbasecontroller_layer')) : '');
    }

    protected function getNamespace($appNamespace, $module)
    {
        return parent::getNamespace($appNamespace, $module) . '\basecontroller';
    }

}
