<?php
/**
 * Created by yongjiapei.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 16:43
 * PhpStorm
 */

namespace data\execption;


class ParameterException extends BaseExecption {
    public $code = 200;
    public $errorCode = 10000;
    public $message = '参数错误';
    public $success = true;

}