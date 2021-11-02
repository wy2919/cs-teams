<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model;


class Activity extends BaseModel
{
    // 一对一关联
    public function apply(){
        return $this->hasMany('Apply','cid','id');
    }

}