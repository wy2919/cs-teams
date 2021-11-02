<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model;

use data\model\BaseModel;

class Report extends BaseModel
{
    // 用户
    public function user(){
        return $this->hasOne('Member','id','mid');
    }

    // 商品
    public function commodity(){
        return $this->hasOne('Commodity','id','cid');
    }


}