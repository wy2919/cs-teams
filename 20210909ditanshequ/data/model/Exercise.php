<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model;


class Exercise extends BaseModel
{
    // 一对一关联
    public function types(){
        return $this->hasOne('type','id','types');
    }

}