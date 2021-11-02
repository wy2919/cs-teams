<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;


use data\model\BaseModel;

class SystemRoleMethod extends BaseModel
{

    public function Roles(){
        return $this->hasOne('SystemRole','id','role_id');
    }

    public function methods(){
        return $this->hasOne('SystemMethod','id','method_id');
    }

}