<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model\system;

use data\model\BaseModel;

class SystemUser extends BaseModel
{

    public function roles(){
        return $this->hasMany('SystemUserRole','user_id','id');
    }

//    public function depart(){
//        return $this->hasOne('data\model\oa\OaDepartment','id','depart_id');
//    }

//    public function roles(){
//        return $this->belongsToMany('SystemRole','SystemUserRole','role_id','id');
//    }
}