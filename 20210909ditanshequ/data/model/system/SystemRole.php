<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;


use data\model\BaseModel;

class SystemRole extends BaseModel
{
    public function users(){
        return $this->hasMany('SystemUserRole','role_id','id');
    }

    public function methods(){
        return $this->hasMany('SystemRoleMethod','role_id','id');
    }
}