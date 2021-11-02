<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;


use data\model\BaseModel;

class SystemMethod extends BaseModel
{
    public function roles(){
        return $this->hasMany('SystemRoleMethod','method_id','id');
    }
}