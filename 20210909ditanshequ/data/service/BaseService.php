<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\service;


class BaseService
{
    protected $where = [];

    protected $model;       // 模型
    protected $session;
    protected $SystemLogs;
    protected $Request;
    protected $data;

    public function __construct()
    {
        $this->session = (new \data\util\Sessions);
        $this->SystemLogs = (new \data\facade\SystemLogs);
        $this->Request = (new \think\facade\Request);

        $this->data = $this->Request::param();


        if($this->session->getUserInfo('uid') != 'admin'){
            $where = ['pid','in',$this->session->getUserProject()];
        }

        // 初始化模型、服务、验证器
        $this->initMVS();
    }




    /**
     * 初始化模型、服务、验证器
     */
    public function initMVS()
    {

        $name = CONTROLLER_NAME;
        $arr = [
            'model' => '\data\model\\',
//            'service' => '\data\service\\',
//            'validate'=> '\app\admin\validate\\'
        ];
        foreach ($arr as $k => $v) {
            $this->$k = $v . ucfirst($name);
            if (class_exists($this->$k)) {
                $this->$k = (new $this->$k);
            }
        }


    }


    /**
     * 获取分页条数
     * @param $pagesize
     * @return mixed
     */
    public function getLimit($pagesize){
        $limit = $this->session->getConfig('SYSTEM_CONFIG_SESSION')['cfg_pagenum'];
        if (!empty($pagesize)) {
            $limit = $pagesize;
        }
        return $limit;
    }

    /**
     * 公共删除方法
     * @param $dbname  表名
     * @param array $fileField  附件路径字段
     * @param $field  日志显示字段
     * @return mixed
     */
    public function delDataById($dbname,$content='操作',$field,$fileField=[]){
        $id = $this->Request::param('id');

        $list = $dbname::find($id); //如果是文件就删除
        foreach ($fileField as $v){
            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$list[$v]) && is_file($_SERVER['DOCUMENT_ROOT'].'/'.$list[$v])) {
                unlink( $_SERVER['DOCUMENT_ROOT'].'/'.$list[$v]);
            }
        }
        $result = $dbname::destroy($id);
        if($result){
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name')."(".$this->session->getUserInfo('uid').")".$content.$list[$field]." 成功",$result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name')."(".$this->session->getUserInfo('uid').")".$content.$list[$field]." 失败",$result);
    }



    /**
     * 公共删除方法2
     * @param $dbname  表名
     * @param array $fileField  附件路径字段
     * @param $field  日志显示字段
     * @return mixed
     */
    public function delDataByIds($dbname,$field=FIELD,$content='删除'.NAMECRUD.'：',$fileField=[]){
        $id = $this->Request::param('id');


        if (!empty($this->Models)){
            $list = $this->Models->find($id); //如果是文件就删除
        }else{
            $list = $this->model->find($id); //如果是文件就删除
        }


        foreach ($fileField as $v){
            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$list[$v]) && is_file($_SERVER['DOCUMENT_ROOT'].'/'.$list[$v])) {
                unlink( $_SERVER['DOCUMENT_ROOT'].'/'.$list[$v]);
            }
        }

        if (!empty($this->Models)){
            $result = $this->Models->destroy($id);
        }else{
            $result = $this->model->destroy($id);
        }

        if($result){
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name')."(".$this->session->getUserInfo('uid').")".$content.$list[$field]." 成功",$result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name')."(".$this->session->getUserInfo('uid').")".$content.$list[$field]." 失败",$result);
    }

    /**
     * 公共返回数据
     * @param $tablelist
     * @return \think\response\Json
     */
    public function returnListData($tablelist){
        return json(['code'=>0,'message'=>"","count"=>$tablelist['total'],'data'=>$tablelist['data']]);
    }

    /**
     * 获取日志数据 处理json数据  方便导入
     * @return array
     */
    public function returnExcelData($json){
        $list = $json->getContent();
        $list = json_decode($list,true);
        return $list['data'];
    }

    /**
     * 二维数据 根据某个键的值去重
     * @param $arr
     * @param $key
     * @return array
     */
    public function assoc_unique($arr, $key) {

        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        foreach ($tmp_arr as $k=>$v){
            $tmp_arr[$k] = [$key=>$v];
        }
        return $tmp_arr;
    }

    /**
     * 根据id 获取信息
     * @return array
     */
    public function getById()
    {
        $id = input('id');
        if (!empty($this->Models)){
            return $this->Models->find($id)->toArray();
        }else{
            return $this->model->find($id)->toArray();
        }

    }



}