<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;


use data\model\BaseModel;

class SystemLog extends BaseModel
{
    public function user(){
        return $this->hasOne('SystemUser','id','uid');
    }
}