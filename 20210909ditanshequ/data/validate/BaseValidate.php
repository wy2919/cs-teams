<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 10:00
 */

namespace data\validate;


use data\execption\ParameterException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate {

    public function gocheck($scene=''){
        //获取http请求的参数
        //对这些参数进行校验
        $request = Request::instance();
        $params = $request->param();
        /*场景验证*/
        if($scene){
            $result =$this->scene($scene)->batch()->check($params);
        }else{
            $result =$this->batch()->check($params);
        }

        if(!$result){
//            $error = $this->error;
//            var_dump($this->getError());
            $e = new ParameterException([
                'errorCode'=>$this->error['code'],
                'message' => $this->error['message']
            ]);
            throw $e;
        }else{
            return true;
        }
    }


    /**
     * 自定义验证规则
     */
    protected function isPostiveInteger($value,$rule='',$data='',$field=''){
        if(is_numeric($value) && is_int($value+0) && ($value+0) > 0){
            return true;
        }else{
            return false;
//            return $field.'必须是正整数';
        }
    }

    /*判断是否为空*/
    protected function isNotEmpty($value,$rule='',$data='',$field=''){
        if(empty($value)){
            return false;
//            return $field.'不允许为空';
        }else{
            return true;
        }
    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * 功能  根据验证规则获取参数  多余的参数不要
     * @param $arrays
     * @return array
     * @throws ParameterException
     */
    public function getDataByRule($arrays){
        if(array_key_exists('user_id',$arrays) | array_key_exists('uid',$arrays)){
            //防止恶意覆盖user_id  和  uid
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数user_id 和 uid'
            ]);
        }

        $newArray = []; // 只要指定的参数
        foreach ($this->rule as $key => $value){
            $newArray[$key] =$arrays[$key];
        }

        return $newArray;
    }

}