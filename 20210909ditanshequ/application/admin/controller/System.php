<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace app\admin\controller;


use data\facade\Rbac;
use data\facade\Sessions;
use data\facade\SystemLogs;
use data\facade\TreeUtil;
use data\facade\Uploads;
use data\model\system\SystemBanner;
use data\model\system\SystemConfig;
use data\model\system\SystemRole;
use data\model\system\SystemUser;
use data\model\Project;
use data\service\MessageService;
use data\service\SystemService;
use data\service\SystemUserService;
use data\validate\AdminVerify;
use data\validate\UserVerify;
use think\facade\Request;

class System extends Base
{


    private  $systemUserService;
    // 初始化
    protected function initialize(){
        $this->systemUserService = new SystemUserService();
    }

    /**
     * 保持登录
     */
    public function keepLogin(){}


    /**
     * 编辑发送消息
     * @return mixed
     */
    public function Msg(){
        if(Request::isPost()){

            $msg = $this->getModel_s('SystemUser',[['idd','=',input('idd')]],2)->find()['msg'];

            // 合并数组
//            $arr = array_merge(explode(',',$msg),explode(',',input('data')));
//            var_dump($arr);

            $pd = $this->getModel_s('SystemUser',[['idd','=',input('idd')]],2)->update([
                'msg'=> input('data')
            ]);

            if (!empty($pd)){
                return json(['code'=>1,'message'=>'成功']);
            }else {
                return json(['code'=>-1,'message'=>'失败']);
            }
        }
        $id = Request::param('idd');
        if(empty($id)){
            $id = Sessions::getUserInfo('uid');
        }

        $this->assign('Exclusive',$this->getModel_s('Exclusive',[['type','=',1],['is_public','=',1]]));


        $msg = $this->getModel_s('SystemUser',[['idd','=',input('idd')]],2)->find()['msg'];

        $this->assign('data',explode(',',$msg));
        return $this->fetch('system/user/system_user_msg');
    }


    /**
     * 编辑前台会员信息
     * @return mixed
     * @throws \data\execption\ParameterException
     */
    public function Edit()
    {
        if (Request::isPost()) {
            $user = $this->getModel('SystemUser')->where('idd', input('idd'))->update([
                'user_status'=>input('user_status'),
                'degnji'=>input('degnji'),
                'jifen'=>input('jifen'),
                'user_headimg' => input('user_headimg'),
                'nick_name' => input('nick_name'),
                'real_name' => input('real_name'),
                'user_sex' => input('user_sex'),
                'user_tel' => input('user_tel'),
                'invit_code' => input('invit_code'),
                'des' => input('des'),
                'user_birthday' => input('user_birthday'),
            ]);

            return json(['code'=>1,'message'=>'修改成功']);


//            (new ParamVerify())->gocheck('id');
//            return (new SystemService())->memberEdit();
        }


//        (new ParamVerify())->gocheck('id');
        $this->assign([
            'info' => $this->getModel('SystemUser')->where('idd', input('idd'))->find(),
        ]);
        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function logout(){
        Sessions::clear();
        $this->redirect(LOGIN);
    }

    /**
     * 导出日志
     */
    public function excelLogs(){
        (new SystemService())->excelLogs();
    }

    /**
     * 导入日志
     */
    public function excelLogsIn(){
        return (new SystemService())->excelLogsIn();
    }

    /**
     * 登录页面
     * @return mixed
     */
    public function login(){
        if(Request::isPost()){ //登录验证
            (new UserVerify())->gocheck('login');
            $username = Request::param('username');
            $password = Request::param('password');
            /*登录验证  返回ajax 数据*/
            $res = ajaxRuturn($this->systemUserService->login($username,$password));
            if($res['code'] == 1){
                SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."登录系统",true);
            }
            return $res;
        }

        $this->assign([
            'config'=> (new SystemConfig())->getConfigs()
        ]);

        return $this->fetch();
    }


    /**
     * 根据用户 账号获取用户权限 菜单
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function index(){
        $this->assign([
            'menus'=>Rbac::getMenusByMethodId(),
            'uname'=>Sessions::getUserInfo('real_name')
        ]);

        return $this->fetch();
    }

    /**
     * 首页
     * @return mixed
     */
    public function welcome(){
        // $string = '123';
        // echo password_hash($string,1);
        // return json(Sessions::getUserRole());
        $this->assign('info',(new SystemService())->indexData());
        return $this->fetch();
    }

    /**
     * 日志列表
     * @return mixed
     */
    public function logList(){
        if(Request::param('page')){
            return (new SystemService())->LogList();
        }
        return $this->fetch();
    }


    /**
     * 日志列表
     * @return mixed
     */
    public function systemConfig(){
        if(Request::param('page')){
            return (new SystemService())->configList();
        }
        return $this->fetch('system/config/system_config');
    }

    /**
     * 添加系统配置
     * @return mixed
     */
    public function systemConfigAdd(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_config_add');
            return (new SystemService())->systemConfigAdd();
        }
        return $this->fetch('system/config/system_config_add');
    }

    /**
     * 修改系统配置
     * @return mixed|\think\response\Json
     */
    public function systemConfigEdit(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_config_edit');
            return (new SystemService())->systemConfigEdit();
        }

        (new AdminVerify())->gocheck('id');
        $this->assign('sysinfo',(new SystemService())->getSystemConfigByid());
        return $this->fetch('system/config/system_config_edit');
    }

    /**
     * 删除配置
     */
    public function systemConfigDel(){
        (new AdminVerify())->gocheck('id');
        return (new SystemService())->systemConfigDel();
    }


    /**
     * 权限管理
     * @return mixed
     */
    public function systemAction(){
        if(Request::param('page')){
            return (new SystemService())->systemActionList();
        }
        return $this->fetch('system/action/system_action');
    }

    /**
     * 添加权限
     * @return mixed|\think\response\Json
     */
    public function systemActionAdd(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_methoed_add');
            return (new SystemService())->systemActionAdd();
        }
        $this->assign('actions',(new SystemService())->getSystemMethodById()['data']);
        return $this->fetch('system/action/system_action_add');
    }

    /**
     * 权限的下拉树 数据
     * @return mixed
     */
    public function getMethodsArrayData(){
        return TreeUtil::recursive_make_tree((new SystemService())->getSystemMethodById('allData')['data'],'id','pid','children');
    }

    /**
     * 获取权限
     * @return \think\response\Json
     * @throws \data\execption\ParameterException
     */
    public function getSystemMethodByPid(){
        (new AdminVerify())->gocheck('pid');
        return json((new SystemService())->getSystemMethodById('pid',Request::param('pid')));
    }

    /**
     * 修改权限
     * @return mixed|\think\response\Json
     */
    public function systemActionEdit(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_methoed_edit');
            return (new SystemService())->systemActionEdit();
        }

        (new AdminVerify())->gocheck('id');
        $action = (new SystemService())->getSystemMethodById('id',Request::param('id'))['data'][0];
        $pids = explode('-',$action['pathsort']);
        $this->assign([
            'action1' => (new SystemService())->getSystemMethodById()['data'],
            'action2' => isset($pids[0])?(new SystemService())->getSystemMethodById('pid',$pids[0])['data']:[],
            'action3' => isset($pids[1])?(new SystemService())->getSystemMethodById('pid',$pids[1])['data']:[],
            'pids' => $pids,
            'action' => $action
        ]);
        return $this->fetch('system/action/system_action_edit');
    }

    /**
     * 删除权限
     * @return \think\response\Json
     */
    public function systemActionDel(){
        (new AdminVerify())->gocheck('id');
        return (new SystemService())->delDataById('data\model\system\SystemMethod','删除权限','module_name');
    }

    /**
     * 角色管理
     * @return mixed
     */
    public function systemRole(){
        if(Request::param('page')){
            return (new SystemService())->systemRoleList();
        }
        return $this->fetch('system/role/system_role');
    }

    /**
     * 添加角色
     * @return mixed|\think\response\Json
     */
    public function systemRoleAdd(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_role_add');
            return (new SystemService())->systemRoleAdd();
        }

        $this->assign([
            'methods'=>Rbac::getMethods()
        ]);
        return $this->fetch('system/role/system_role_add');
    }

    /**
     * 编辑角色
     * @return mixed|\think\response\Json
     */
    public function systemRoleEdit(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_role_edit');
            return (new SystemService())->systemRoleEdit();
        }

        (new AdminVerify())->gocheck('id');

        $this->assign([
            'methods'=>Rbac::getMethods(),
            'role_method'=>Rbac::getRoleMethods(Request::param('id')),
            'role' => SystemRole::find(Request::param('id'))
        ]);

        return $this->fetch('system/role/system_role_edit');
    }

    /**
     * 删除角色
     */
    public function systemRoleDel(){
        (new AdminVerify())->gocheck('id');
        return (new SystemService())->systemRoleDel();
    }


    /**
     * 管理员管理
     * @return mixed
     */
    public function systemUser(){
        if(Request::param('page')){
            return (new SystemService())->systemUserList();
        }
        return $this->fetch('system/user/system_user');
    }


    /**
     * 添加管理员
     * @return mixed|\think\response\Json
     */
    public function systemUserAdd(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_user_info');
            return (new SystemService())->systemUserAdd();
        }

        return $this->fetch('system/user/system_user_add');
    }

    /**
     * 编辑管理员
     * @return mixed
     */
    public function systemUserEdit(){
        if(Request::isPost()){
            (new AdminVerify())->gocheck('system_user_info');
            return (new SystemService())->systemUserEdit();
        }
        $id = Request::param('id');
        if(empty($id)){
            $id = Sessions::getUserInfo('uid');
        }
//        (new AdminVerify())->gocheck('id');

        $this->assign('user',SystemUser::find($id));
        $this->assign('user_roles',Rbac::getUserRoles($id));

        return $this->fetch('system/user/system_user_edit');
    }

    /**
     * 删除管理员
     */
    public function systemUserDel(){
        (new AdminVerify())->gocheck('id');
        return (new SystemService())->systemUserDel();
    }

    /**
     * 判断管理员是不是重名
     * @return \think\response\Json
     */
    public function is_account(){
        (new AdminVerify())->gocheck('system_user_account');
        $res=SystemUser::where('id',Request::param('account'))->find();
        if($res){
            return json(['code'=>1,'message'=>"账号重名"]);
        }else{
            return json(['code'=>1,'message'=>"账号可以使用"]);
        }
    }

    /**
     * 修改密码
     * @return \think\response\Json
     */
    public function changepwd(){
        (new AdminVerify())->gocheck('system_user_password');
        return (new SystemService())->setUserPassword();
    }

    /**
     * 修改用户登录状态
     * @return \think\response\Json
     * @throws \data\execption\ParameterException
     */
    public function Limitlogin(){
        (new AdminVerify())->gocheck('system_user_status');
        return (new SystemService())->setUserStatus();
    }

    /**
     * 轮播图列表
     * @return mixed
     */
    public function bannerlist(){
        $this->assign('sellist',config('myconfig.bannerType'));
        return $this->fetch('system/banner/bannerlist');
    }
    /*轮播图列表*/
    public function getBannerData(){
        return (new SystemService())->bannerList();

    }
    public function banneradd(){
        if(Request::isPost()){
//            (new AdminVerify())->gocheck('banner_add');
            return (new SystemService())->bannerAdd();
        }
        $this->assign('sellist',config('myconfig.bannerType'));
        return $this->fetch('system/banner/banneradd');
    }

    public  function banneredit(){
        if(Request::isPost()){
//            (new AdminVerify())->gocheck('banner_edit');
            return (new SystemService())->banneredit();

        }
        (new AdminVerify())->gocheck('id');
        $this->assign([
            'sellist'=> config('myconfig.bannerType'),
            'banner'=> SystemBanner::find(Request::param('id')),
            'news'=> $this->getModel_s('Exclusive',[['type', '=', 1]])
        ]);
        return $this->fetch('system/banner/banneredit');
    }


    /*删除轮播图轮播图*/
    public function bannerldel(){
        (new AdminVerify())->gocheck('id');
        return (new SystemService())->delDataById('data\model\system\SystemBanner','删除轮播图','title',['bannersrc']);
    }
    /*修改轮播图状态*/
    public function bannerlstatus(){
        (new AdminVerify())->gocheck('id');
        $data = Request::param();
        $data['isshow'] = $data['status'] == 1 ? '0':'1';
        SystemBanner::update($data);
    }

}