<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\service;


use data\facade\Excels;
use data\facade\Rbac;
use data\facade\Sessions;
use data\facade\SystemLogs;
use data\model\system\SystemBanner;
use data\model\system\SystemConfig;
use data\model\system\SystemLog;
use data\model\system\SystemMethod;
use data\model\system\SystemRole;
use data\model\system\SystemRoleMethod;
use data\model\system\SystemUser;
use data\model\system\SystemUserRole;
use data\model\Member;
use think\facade\Request;

class SystemService extends BaseService
{


    /**
     * 修改会员
     * @return mixed
     */
    public function memberEdit()
    {
        $data = Request::param();

        $result = (new \data\model\system\SystemUser)::update($data);

        if ($result) {
            return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改会员：" . $data['nick_name'] . " 成功", $result);
        }
        return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改会员：" . $data['nick_name'] . "失败", $result);
    }
    /**
     * 根据id 获取信息
     * @return array
     */
    public function getMemberById()
    {
        $id = Request::param('id');
        return SystemUser::find($id)->toArray();
    }


    /**
     * 返回首页统计信息
     * @return array
     */
    public function indexData(){
        return [
            'userName'=>Sessions::getUserInfo('real_name'),
            'userNum' => count(Member::select()->toArray()),
            'projectNum'=>123,
            'buildNum'=>123,
        ];
    }

    /**
     * 导出示例
     */
    public function excelLogs(){
        $list = $this->returnExcelData($this->LogList());
        $xlsName  = "日志表";
        $xlsData = [];
        foreach ($list as $v){
            $xlsData[] = $v;
        }
        $xlsCell = [
            ['uid','账号'],
            ['content','操作内容'],
            ['ip','IP'],
            ['location','地址'],
            ['browser','浏览器'],
            ['os','系统'],
            ['create_time','操作时间'],
        ];

        SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."导出日志 成功",1);
        Excels::exportExcels($xlsName,$xlsCell,$xlsData);
    }
    /**
     * 导入 示例
     */
    public function excelLogsIn(){
        $logList = Excels::imorder(); //返回导入数据
//        return json($logList);
        $data = [];
        foreach ($logList as $k => $v) { //组织入表数据
            $data[] = ['uid'=>$v[0],'content'=>$v[1],'ip'=>$v[2],'location'=>$v[3],'browser'=>$v[4],'os'=>$v[5]];
        }
        $res = (new SystemLog())->allowField(true)->saveAll($data);//插入表格
        if($res){
            return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."导入日志 成功",1);
        }
        return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."导入日志 失败",1);
    }

    /**
     * 移入baseServicec了
     * 获取日志数据 处理json数据  方便导入
     * @return array
     */
//    public function LogData(){
//        $list = $this->LogList()->getContent();
//        $list = json_decode($list,true);
//        return $list['data'];
//    }
    /**
     * 返回日志列表
     * @param $data
     * @return array
     */
    public function LogList(){

        $data = Request::param();
        $where = [];
        if(!empty($data['uid'])){
            $where[] = ['uid','like','%'.$data['uid'].'%'];
        }
        if(!empty($data['startTime'])){
            $where[] = ['create_time','>',strtotime($data['startTime'])];
        }
        if(!empty($data['endTime'])){
            $where[] = ['create_time','<',strtotime($data['endTime'])];
        }

        $limit = $this->getLimit($data['limit']);
        $tablelist = (new SystemLog())->getTableList(['user'],'',$limit,[],$where);

        $list = $tablelist['data'];
        foreach ($list as $k=>$v){
            $list[$k]['name'] = isset($v['user']['real_name']) ? $v['user']['real_name'] : '已被刪除';
        }

        return json(['code'=>0,'msg'=>"","count"=>$tablelist['total'],'data'=>$list]);
    }

    /**
     * 返回系统配置列表
     * @param $data
     * @return array
     */
    public function configList(){
        $data = Request::param();


        $limit = $this->getLimit($data['limit']);
        $tablelist = (new SystemConfig())->getTableList([],'',$limit);
        $list = $tablelist['data'];
        return json(['code'=>0,'message'=>"","count"=>$tablelist['total'],'data'=>$list]);
    }

    /**
     * 添加系统配置
     * @return mixed
     */
    public function systemConfigAdd(){
        $data = Request::param();
        $result = (new SystemConfig())->allowField(true)->save($data);
        if($result){
            return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."添加配置：".$data['varname']." 成功",$result);
        }
        return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."添加配置：".$data['varname']." 失败",$result);
    }

    /**
     * 获取配置信息
     * @return SystemConfig
     */
     public function getSystemConfigByid(){
         $data = Request::param();
         return SystemConfig::find($data['id']);
     }

    /**
     * 编辑 系统配置
     * @return mixed
     */
     public function systemConfigEdit(){
         $data = Request::param();
         $result = SystemConfig::update($data);
         if($result){
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."修改配置：".$data['varname']." 成功",$result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."修改配置：".$data['varname']." 失败",$result);
     }

    /**
     * 删除系统配置文件
     * @return mixed
     */
     public function systemConfigDel(){
         $varname = Request::param('id');

         $list = SystemConfig::find($varname); //如果是文件就删除
         if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$list['value']) && is_file($_SERVER['DOCUMENT_ROOT'].'/'.$list['value'])) {
             unlink( $_SERVER['DOCUMENT_ROOT'].'/'.$list['value']);
         }
         $result = SystemConfig::destroy($varname);
         if($result){
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."删除配置：".$list['varname']." 成功",$result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."删除配置：".$list['varname']." 失败",$result);
     }


    /**
     * 获取权限列表
     * @return \think\response\Json
     */
     public function systemActionList(){
         $data = Request::param();
         $where = [];
         if(!empty($data['name'])){
             $where[] = ['module_name','like','%'.$data['name'].'%'];
         }
         $limit = $this->getLimit($data['limit']);
         $tablelist = (new SystemMethod())->getTableList([],'',$limit,'pathsort',$where);
         return json(['code'=>0,'msg'=>"","count"=>$tablelist['total'],'data'=>$tablelist['data']]);
     }

    /**
     * 根据pid 查询权限
     * @param int $id
     * @return \think\response\Json
     */
     public function getSystemMethodById($field='pid',$pid=0){
         $where = [];
         if($field != 'allData'){
             $where = [
                 [$field,'=',$pid]
             ];
         }

         $tablelist = (new SystemMethod())->getTableAll([],"id,pid,pathsort,module_name,module,controller,method,sort,is_menu,left_icon,menu_text,level",[],$where);
         foreach ($tablelist as $k=>$v){
             $tablelist[$k]['name'] = $v['module_name'];
         }
         return ['code'=>1,'message'=>"",'data'=>$tablelist];
     }



    /**
     * 处理权限数据
     * @param $data
     * @return mixed
     */
     public function setMethodFields($data){
         /*拼接路由  将模块小写*/
         $data['url'] = '';
         if($data['is_menu'] == 1 && !empty($data['module']) && !empty($data['contro']) && !empty($data['method'])){
             $data['url'] = $data['module'].'/'.$data['contro'].'/'.$data['method'];
         }
         $data['module']    = strtolower($data['module']);
         $data['controller'] = strtolower($data['contro']);
         $data['method']    = strtolower($data['method']);
         return $data;
     }



    /**
     * 添加权限
     * @return mixed
     */
     public function systemActionAdd(){
         $data = Request::param();
         $data = $this->setMethodFields($data);

         $systemMethod = new SystemMethod();
         $res = $systemMethod->allowField(true)->save($data);

         if($res){
             $insertId = $systemMethod->id;
            if($data['level'] == 0){
                $pathsort = ['id' =>$insertId,'pathsort'=>$insertId];;
            }else{
                $method = SystemMethod::find($data['pid']);
                $pathsort = ['id' =>$insertId,'pathsort'=>$method['pathsort'].'-'.$insertId];;
            }
             $result = SystemMethod::update($pathsort);
             if($result){
                 return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."添加权限：".$data['module_name']." 成功",$result);
             }
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."添加权限：".$data['module_name']."成功但是修改排序值失败",$result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."添加权限：".$data['module_name']." 失败",$res);
     }

    /**
     * 修改权限问题
     * @return \think\response\Json
     */
     public function systemActionEdit(){
         $data = Request::param();
         $data = $this->setMethodFields($data);

         if($data['level'] == 0){
             $data['pathsort'] = $data['id'];
         }else{
             $method = SystemMethod::find($data['pid']);
             $data['pathsort'] = $method['pathsort'].'-'.$data['id'];
         }
         $result = SystemMethod::update($data);
         if($result){
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."编辑权限：".$data['module_name']." 成功",$result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name')."(".Sessions::getUserInfo('uid').")"."编辑权限：".$data['module_name']."失败",$result);
     }

    /**
     * 返回角色列表
     * @return \think\response\Json
     */
    public function systemRoleList(){
        $data = Request::param();
        $where = [];
        $limit = $this->getLimit($data['limit']);
        $tablelist = (new SystemRole())->getTableList([],'',$limit,[],$where);
        return json(['code'=>0,'message'=>"","count"=>$tablelist['total'],'data'=>$tablelist['data']]);
    }

    /**
     * 获取多选角色 数据
     * @return array
     */
    public function getAllRoleList(){ //where('id','<>','1')->
        return SystemRole::field('id,role_name')->select()->toArray();
    }

    /**
     * 添加角色
     * @return mixed
     * @throws \Exception
     */
     public function systemRoleAdd(){
         $data = Request::param();
         if(!empty($data['projects'])){
             $data['projects'] = implode(',',$data['projects']);
         }


         $role = new SystemRole();
         $result = $role->allowField(true)->save($data);

         if($result) {
             if(!empty($data['method'])){
                 Rbac::setSystemRoleMethod($role->id,$data['method']);
             }
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加角色：" . $data['role_name'] . "成功", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加角色：" . $data['role_name'] . "失败", $result);
     }

    /**
     * 编辑角色
     * @return \think\response\Json
     */
     public function systemRoleEdit(){
         $data = Request::param();
         if(!empty($data['projects'])){
             $data['projects'] = implode(',',$data['projects']);
         }
         $result = SystemRole::update($data);
         if($result) {
             if(!empty($data['method'])) {
                 $result = Rbac::setSystemRoleMethod($data['id'], $data['method']);
             }

             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改角色：" . $data['role_name'] . "成功", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改角色：" . $data['role_name'] . "失败", $result);
     }

    /**
     * 删除角色
     * @return mixed
     * @throws \Exception
     */
     public function systemRoleDel(){
         $id = Request::param('id');
         $data = SystemRole::find($id);
         SystemRoleMethod::where('role_id','=',$id)->delete();
         $result = SystemRole::destroy($id);
         if ($result) {
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "删除角色：" . $data['role_name'] . " 成功", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "删除角色：" . $data['role_name'] . "失败", $result);
     }

    /**
     * 获取用户列表
     * @return \think\response\Json
     */
     public function systemUserList(){
         $data = Request::param();
         $where = [];
         if(!empty($data['account'])){
             $where[] = ['id','like','%'.$data['account'].'%'];
         }
         if(!empty($data['realname'])){
             $where[] = ['real_name','like','%'.$data['realname'].'%'];
         }
         if(!empty($data['nick_name'])){
             $where[] = ['nick_name','like','%'.$data['nick_name'].'%'];
         }
         if(!empty($data['depart_id'])){
             $where[] = ['depart_id','=',$data['depart_id']];
         }
         if(!empty($data['projectid_id'])){
             $where[] = ['id','in',getIdsStr(Rbac::getUserByProjectId($data['projectid_id']),'id')];
         }
         if(!empty($data['state'])){
             $where[] = ['user_status','=',$data['state']];
         }

         if(!empty($data['is_system'])){
             $where[] = ['is_system','=',$data['is_system']];
         }


         $limit = $this->getLimit($data['limit']);
         $tablelist = (new SystemUser())->getTableList([],'',$limit,[],$where);

         return json(['code'=>0,'message'=>"","count"=>$tablelist['total'],'data'=>$tablelist['data']]);
     }

    /**
     * 修改用户登录状态
     * @return mixed
     */
     public function setUserStatus(){
         $id = Request::param('id');
         $state = Request::param('user_status');
         $result = SystemUser::update(['id'=>$id,'user_status'=>$state]);
         if ($result) {
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $id . " 登录状态 成功", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $id . " 登录状态 失败", $result);
     }

    /**
     * 修改用户密码
     * @return mixed
     */
     public function setUserPassword(){
         $data = Request::param();
         if($data['id'] == 'myid'){
             $data['id'] = Sessions::getUserInfo('uid');
         }
        //  $data['user_password'] = md5($data['password']);
        $data['user_password'] = password_hash($data['password'],1);
         $result = SystemUser::update($data);
         if ($result) {
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $data['id'] . " 密码 成功", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $data['id'] . " 密码 失败", $result);
     }

    /**
     * 添加用户
     * @return mixed
     */
     public function systemUserAdd(){
         $data = Request::param();

         /*默认密码123456*/
         $user = new SystemUser();
         $result = $user->allowField(true)->save($data);
         if ($result) {
             /*添加到中间表*/
             $result = Rbac::setSystemUserRole($data['id'],$data['roles']);
             if ($result) {
                 return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加用户：" . $data['id'] . " 成功", $result);
             }
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加用户：" . $data['id'] . "失败", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加用户：" . $data['id'] . "失败", $result);
     }

    /**
     * 修改用户信息
     * @return mixed
     */
     public function systemUserEdit(){
         $data = Request::param();
         $res = SystemUser::update($data);
         if($res){
             /*添加到中间表*/
             $result = Rbac::setSystemUserRole($data['id'],$data['roles']);
             if ($result) {
                 return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $data['id'] . " 成功", $result);
             }
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $data['id'] . "失败", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改用户：" . $data['id'] . "失败", $result);
     }

    /**
     * 删除用户
     * @return mixed
     * @throws \Exception
     */
     public function systemUserDel(){
         $id = Request::param('id');
         $data = SystemUser::find($id);
         SystemUserRole::where('user_id','=',$id)->delete();
         $result = SystemUser::destroy($id);
         if ($result) {
             return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "删除用户：" . $data['id'] . " 成功", $result);
         }
         return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "删除用户：" . $data['id'] . "失败", $result);
     }

    /**
     * 返回轮播图列表
     * @return \think\response\Json
     */
     public function bannerList(){
         $where = [];
         $data = Request::param();
         if(!empty($data['title'])){
             $where[] = ['title','like','%'.$data['title'].'%'];
         }
         if(!empty($data['banner_type'])){
             $where[] = ['banner_type','=',$data['banner_type']];
         }

         $limit = $this->getLimit($data['limit']);
         $tablelist = (new SystemBanner())->getTableList([],'',$limit,[],$where);
         return json(['code'=>0,'message'=>"","count"=>$tablelist['total'],'data'=>$tablelist['data']]);
     }

    /**
     * 添加轮播图
     * @return mixed
     */
      public function bannerAdd(){
          $data = Request::param();

          $result = (new SystemBanner())->allowField(true)->save($data);
          if ($result) {
              return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加图片：" . $data['title'] . " 成功", $result);
          }
          return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "添加图片：" . $data['title'] . "失败", $result);

      }

    /**
     * 修改轮播图
     * @return mixed
     */
      public function banneredit(){
          $data = Request::param();

          $result = SystemBanner::update($data);
          if ($result) {
              return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改图片：" . $data['title'] . " 成功", $result);
          }
          return SystemLogs::logwrite(Sessions::getUserInfo('real_name') . "(" . Sessions::getUserInfo('uid') . ")" . "修改图片：" . $data['title'] . "失败", $result);

      }


}