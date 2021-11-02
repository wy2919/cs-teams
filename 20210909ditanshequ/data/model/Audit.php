<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model;


class Audit extends BaseModel
{
    // 一对一关联
    public function types(){
        return $this->hasOne('type','id','types');
    }

    // 一对一关联
    public function user(){
        return $this->hasOne('Member','id','mid');
    }

}