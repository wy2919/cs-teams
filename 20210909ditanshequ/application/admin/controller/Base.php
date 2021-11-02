<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\admin\controller;

use data\validate\ParamVerify;
use data\facade\Rbac;
use data\facade\Uploads;
use data\model\system\SystemConfig;
use data\service\SystemService;
use data\service\WechatService;
use log_class\Agent;
use log_class\Ip;
use think\App;
use think\Controller;
use think\facade\Request;

class Base extends Controller
{


    protected $model;       // 模型
    protected $service;       // 模型
    protected $session;
    protected $SystemLogs;
    protected $Request;
    protected $data;
    protected $ParamVerify;


    public function __construct(App $app = null)
    {
        parent::__construct($app);


        $this->session = (new \data\util\Sessions);
        $this->SystemLogs = (new \data\facade\SystemLogs);
        $this->Request = (new \think\facade\Request);
        $this->ParamVerify = (new ParamVerify);

        $this->data = $this->Request::param();

        if(!empty($this->name)){
            defined('NAMECRUD') or define('NAMECRUD', $this->name); // 定义log
        }

        if(!empty($this->name)){
            defined('FIELD') or define('FIELD', $this->field); // 定义log
        }



        defined('IS_GET') or define('IS_GET', $this->request->isGet());             // 定义是否GET请求
        defined('IS_POST') or define('IS_POST', $this->request->isPost());          // 定义是否POST请求


        $where = [
            ['module',     '=', strtolower(request()->module())],
            ['controller', '=', strtolower(request()->controller())],
            ['method',     '=', strtolower(request()->action())],
        ];
        $navMenu = Rbac::getModuleGroup($where,'menu_text');
        if (!empty($navMenu) ) {
            $this->assign('navMenu',$navMenu[0]['menu_text']);
        }else{
            $this->assign('navMenu','');
        }

        // 初始化模型、服务、验证器
        $this->initMVS();

        $this->assign([
            'config'=>(new SystemConfig())->getConfigs(),//获取系统配置
        ]);
    }

    public function bindWx(){
        WechatService::getLoginQr();
    }

    public function getRoles(){
        return $this->fetch('system/rolelist/system_role');
    }


    /**
     * @Notes:
     * @param $name   查询的Model
     * @param array $map  where条件，默认查询所有
     * @param int $pd     1查询  2返回对象
     * @return mixed
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/4 16:02
     */
    public function getModel_s($name,$map = [],$pd = 1)
    {
        if ($pd == 1){
            return $this->getModel($name)->where($map)->select();
        }

        if ($pd == 2){
            return $this->getModel($name)->where($map);
        }
    }





    /**
     * @Notes: 获取别的Model对象
     * @param $name  Model控制器名字 首字母大写
     * @return mixed
     * @author: 洋 <2069644919@qq.com>
     * @Time: 2021/8/4 11:01
     */
    public function getModel($name,$pd = 0)
    {

        if ($pd == 1){
            $str = '\data\model\\'.$name;
            return new $str;
        }

        if (empty($GLOBALS['Model_Array'])){
            $GLOBALS['Model_Array'] = [];
        }

        foreach ($GLOBALS['Model_Array'] as $v){
            foreach ($v as $k=>$vv){
                if ($k == $name){
                    return $vv;
                }
            }
        }

        $str = '\data\model\\'.$name;
        $obj = new $str;
        array_push($GLOBALS['Model_Array'], [$name=>$obj]) ;

        return $obj;
    }

    /**
     * 初始化模型、服务、验证器
     */
    public function initMVS()
    {

        $controller_name = $this->request->controller();        // 控制器名称

        // 判断是否是win还是Linux
        $os_name=PHP_OS;
        if(strpos($os_name,"Linux")!==false){
            // Linux 就不转换为小写
            defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $controller_name);  // 定义控制器名
        }else if(strpos($os_name,"WIN")!==false){
            // win 就转换为小写
            defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', strtolower($controller_name));  // 定义控制器名
        }



        // 获取当前请求控制器名
        $name =  CONTROLLER_NAME;
        $arr = [
            'model' => '\data\model\\',
            'service' => '\data\service\\',
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
     * 获取下拉多选角色数组数据
     * @return mixed
     */
    public function getRoleArrayData(){
        return (new SystemService())->getAllRoleList();
    }


    /**
     * 上传文件
     * @return mixed
     */
    public function upload() {
        return Uploads::upfile();
    }

    /**
     * 根据 路径删除文件
     */
    public function delFiles(){
        $file = Request::param('file');
        if(!empty($file)){
            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$file) && is_file($_SERVER['DOCUMENT_ROOT'].'/'.$file)) {
                unlink( $_SERVER['DOCUMENT_ROOT'].'/'.$file);
            }
        }
    }


    public function curl_get($url,&$httpCode = 0){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        /*不做证书效验，部署在linux环境下请改为true*/
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }


}