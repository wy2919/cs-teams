<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;


use data\model\BaseModel;

class SystemUserRole extends BaseModel
{

    public function users(){
        return $this->hasOne('SystemUser','id','user_id');
    }

    public function roles(){
        return $this->hasOne('SystemRole','id','role_id');
    }
}