<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;


use data\model\BaseModel;

class SystemConfig extends BaseModel
{

    /**
     * 获取系统设置的键值对
     * @return array
     */
    public function getConfigs(){
        $list = self::select()->toArray();
        $configArr = [];
        foreach ($list as $v){
            $configArr[$v['info']] = $v['value'];
        }
        return $configArr;
    }
}