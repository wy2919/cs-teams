<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model;


class Order extends BaseModel
{


    // 一对一关联
    public function user(){
        return $this->hasOne('Member','id','mid');
    }


    // 一对一关联
    public function commodity(){
        return $this->hasOne('Commodity','id','cid');
    }

}