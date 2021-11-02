<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\provider;

use data\facade\Sessions;
use data\facade\TreeUtil;
use data\model\system\SystemMethod;
use data\model\system\SystemRole;
use data\model\system\SystemRoleMethod;
use data\model\system\SystemUser;
use data\model\system\SystemUserRole;
use think\facade\Config;

class Rbac
{
    private $module;
    private $controller;
    private $method;

    private $authModule = [
        'admin',
        'platform',
        // 其他添加的模块
        // ..
    ];
    /**
     * 在Rbac初始化的时候，获取用户请求的权限 URL
     * 模块名 module
     * 控制器 controller
     * 方法名 action
     */
    public function __construct(){
        $this->module     = strtolower(request()->module());
        $this->controller = strtolower(request()->controller());
        $this->method     = strtolower(request()->action());
    }
    /**
     * 做权限校验
     */
    public function check() {
        // 校验用户是否登入成功
//        if (!Sessions::getLogin()) {
//            return false;
//        }
        if (in_array($this->module, $this->authModule)) {
            // 判断是否为系统的管理用户
            if (!Sessions::getIsSystem()) {
                return false;
            }
            return $this->checkModule();//通过url判断是否需要权限
        }
        return true; // 因为访问的是非管理平台
    }

    /*检查白名单*/
    public function checkWhite(){
        $white = Config::get('white.');// 获取所有不需要校验的URL名单
        if (empty($white[$this->module]) || empty($white[$this->module][$this->controller])) { // 先判断访问地址是否白名单中
            return false; //需要权限校验
        }
        if (in_array($this->method, $white[$this->module][$this->controller])) { //判断方法是否在白名单列表中
            return true;
        } else {
            return false; //需要权限校验
        }
    }

    /**
     * 判断是否有权限
     */
    public function checkModule()
    {
        $whereModul = [
            ['module',     '=', $this->module],
            ['controller', '=', $this->controller],
            ['method',     '=', $this->method],
        ];

        // 获取权限信息
        $module = ($this->getModuleGroup($whereModul));

        // 避免没有在tp_module 表中
        if (empty($module) ) {
            return true;
        }
        //120,121,122,123,129,126,127,144,360,128,133,149,139,169,151,171,172,210,334,478,516,517,179,180,186,187,195,196,197,198,199,200,201,202,203,446,469,184,185,190,194,189,191,192,487,533,534,471,472,528,529,530,218,418,474,678,679,680,10006,409,403,405,454,457,459,462,463,460,684,726,727,729,730,732,467,515
        // 2. 用户权限
        $roleModule = explode(',', Sessions::getUserRole());
        // 3. 用户权限校验
        return in_array($module[0]['id'], $roleModule); // booleam
    }
    /**
     * 获取权限信息
     * @param [type] $whereModul [description]
     */
    public function getModuleGroup($where = '1 = 1', $field='*', $whereOr = null){
        if ($whereOr) { // 存在 'pid,sort'
            return SystemMethod::where([$where])->field($field)->whereOr([$whereOr])->order('pid,sort')->select()->toArray();
        } else { //
            return SystemMethod::where($where)->field($field)->order('pid,sort')->select()->toArray();
        }
    }

    /**
     * 根据用户id 获取所有角色id
     * @param $user_id
     * @return bool|string
     */
    public function getRoles($user_id){
        $list = SystemUserRole::where('user_id','=',$user_id)->select()->toArray();
        $roleids = '';
        foreach ($list as $roles){
                $roleids .= $roles['role_id'].',';
        }
        $roleids = substr($roleids,0,-1);
        return $roleids;
    }


    // 2. 根据用户id获取角色的权限 id
    public function getRoleModule($user_id)
    {
        $list = SystemUserRole::with(['roles','roles.methods'])->where('user_id','=',$user_id)->select()->toArray();
       
        $methods = '';
        foreach ($list as $roles){
            if($roles['roles']){
               foreach ($roles['roles']['methods'] as $method){
                    $methods .= $method['method_id'].',';
                } 
            }
            
        }
 
        if($methods != ''){
            $methods = substr($methods,0,-1);
        }
        
        return $methods;
//        $role = SystemUserRole::with([
//            'userGroup' => function ($userGroup){
//                $userGroup->field('id,module_id_array')->where('group_status', 1);
//            }
//        ])->where([
//            ['id','=', $role_id],
//            ['role_status','=', 1]
//        ])->find()->toArray();
//        return $role['user_group']['module_id_array'];
    }


    /*根据权限id 获取菜单*/
    public function getMenusByMethodId(){
        $where = [
            ['id','in',Sessions::getUserRole()],
            ['is_menu','=',1]
        ];
        $list = Rbac::getModuleGroup($where,'id,pid,pathsort,module_name,is_menu,url,left_icon');

//        var_dump($list);

        return TreeUtil::recursive_make_tree($list,'id');
    }

    /**
     * 获取所有的权限信息  返回树状结构
     * @return mixed
     */
    public function getMethods(){
        $list = Rbac::getModuleGroup('','id,pid,module_name');
        return TreeUtil::recursive_make_tree($list,'id');
    }


    /**
     * 根据角色id 获取 所有的权限id
     * @param $role_id
     * @return array
     */
    public function getRoleMethods($role_id){
        $list = SystemRoleMethod::field('method_id')->where('role_id','=',$role_id)->select()->toArray();
        $RoleMethodArr = [];
        foreach ($list as $v){
            $RoleMethodArr[] = $v['method_id'];
        }
        return $RoleMethodArr;
    }

    /*根据项目 id 获取所有用户信息*/
    public function getUserByProjectId($pid){
        /*根据项目id获取角色号*/
        $roles = SystemRole::where("FIND_IN_SET($pid,projects) ")->field('id')->select()->toArray();
        $roleIds = getIdsStr($roles,'id');
        /*根据角色ids  获取用户id*/
        $uidsArr = SystemUserRole::where('role_id','in',$roleIds)->field('user_id')->select()->toArray();
        $uids = getIdsStr($uidsArr,'user_id');
        /*根据用户id 获取用户信息*/
        return SystemUser::where('id','in',$uids)->select()->toArray();
    }

    public function getUsersByRoleId($roleid){
        $uidsArr = SystemUserRole::where('role_id','in',$roleid)->field('user_id')->select()->toArray();
        $uids = getIdsStr($uidsArr,'user_id');
        /*根据用户id 获取用户信息*/
        return SystemUser::where('id','in',$uids)->select()->toArray();
    }





    /**
     * 根据用户id 获取 所有的角色id
     * @param $role_id
     * @return array
     */
    public function getUserRoles($user_id){
        $list = SystemUserRole::field('role_id')->where('user_id','=',$user_id)->select()->toArray();
        $userRoleArr = [];
        foreach ($list as $v){
            $userRoleArr[] = $v['role_id'];
        }
        return $userRoleArr;
    }

    /**
     * 保存角色权限到中间表
     * @param $role_id
     * @param $methods
     * @return \think\Collection
     * @throws \Exception
     */
    public function setSystemRoleMethod($role_id,$methods){
        /*保存到中间表*/
        $roleMethod = [];
        foreach ($methods as $v) {
            $roleMethod[] = ['role_id'=>$role_id,'method_id'=>$v];
        }
        SystemRoleMethod::where('role_id','=',$role_id)->delete();/*全部删除*/
        return (new SystemRoleMethod())->allowField(true)->saveAll($roleMethod);/*重新添加*/
    }

    /**
     * 保存用户角色中间表
     * @param $user_id
     * @param $roles
     * @return \think\Collection
     * @throws \Exception
     */
    public function setSystemUserRole($user_id,$roles){
        /*保存到中间表*/
        $userRole = [];
        foreach ($roles as $v) {
            $userRole[] = ['user_id'=>$user_id,'role_id'=>$v];
        }
        SystemUserRole::where('user_id','=',$user_id)->delete();/*全部删除*/
        return (new SystemUserRole())->allowField(true)->saveAll($userRole);/*重新添加*/
    }





}