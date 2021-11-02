<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\util;

use ali_sms\api_demo\SmsDemo;

class Sms
{

    /**
     * 发送 供应商招标变更提醒
     * [ 'phone'=>$supid['supid']  ]
     * @param $arr
     */
    public function sendSupMsg($arr,$type){
        SmsDemo::sendSms2($arr,$type);
    }

    /**
     * 发送验证码
     * @param $arr
     */
    public function sendCode($arr){
        return SmsDemo::sendSms($arr);
    }
}