<?php
namespace app\index\controller;

use data\model\system\SystemBanner;
use data\model\zhaobiao\ZbClass;
use data\model\zhaobiao\ZbOrders;
use data\model\zhaobiao\ZbQuote;
use data\model\zhaobiao\ZbSupplier;
use data\model\zhaobiao\ZbSupplierCategory;
use data\service\IndexService;
use data\service\ProjectService;
use data\validate\ParamVerify;
use think\facade\Request;
use think\facade\Session;

class Index extends Base
{
    /**
     * 首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $this->assign([
            'banners'=>SystemBanner::where('banner_type','=','招投标系统')->limit(3)->select()->toArray(),
            'proinfo'=>(new ProjectService())->getData(),
            'cates'=>ZbClass::where('level','=','1')->select()->toArray(),
            'orders'=>ZbOrders::with('project,quote')->order('endtime desc')->select()->toArray(),
            'quote'=>ZbQuote::with('order')->order('create_time')->select()->toArray(),
        ]);
        return $this->fetch();
    }

    /*条件筛选*/
    public function getorders(){
        $data = Request::param();

        /*根据类目获取所有的商品类目订单*/
        $where = [];
        if(!empty($data['kwd'])){
            $where[] = ['title','like','%'.$data['kwd'].'%'];
        }

        if(!empty($data['projectid'])){
            $where[] = ['pid','=',$data['projectid']];
        }

        if(!empty($data['catid'])){
            $where[] = ['cid','=',$data['catid']];
        }

        if(!empty($data['endtime'])){
            $where[] = ['endtime','<',strtotime($data['endtime'])];
        }

        $Order = ZbOrders::with('pro,quote')->where($where)->order('create_time desc')->select();

        return json($Order);

    }


    /**
     * 登录
     * @return mixed
     * @throws \data\execption\ParameterException
     */
    public function login(){
        if(Request::isPost()){
            (new ParamVerify())->gocheck('index_login');
            echo (new IndexService())->login();die;
        }
        return $this->fetch();
    }

    /**
     * 注册
     * @return mixed
     */
    public function register(){
        if(Request::isPost()){
            (new ParamVerify())->gocheck('index_login');
            echo (new IndexService())->register();die;
        }
        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function signout(){
        Session::clear(SESSIONINDEX);
        $this->redirect('index');
    }


    /*投标详情*/
    public function zbinfo(){

        if(Request::isPost()){
            echo (new IndexService())->zbinfo($this->account);die;
        }

        (new ParamVerify())->gocheck('id');
        $Order = Request::param('id');
        $OrderDetails = ZbOrders::with(['details','uinfo','catinfo'])->find($Order);
        if(!$OrderDetails){
            $this->error('该订单已删除!','index');
        }

        /*重复投标*/
        $quoteinfo = ZbQuote::where([
            ['order_id','=',$Order],
            ['supplier_id','=',$this->account],
        ])->find();

        $this->assign('istb','no'); //未投标
        $this->assign('quoteinfo',['id'=>'','reply'=>'','price'=>'']);
        if($quoteinfo){
            $this->assign('istb','yes'); //已投标
            $this->assign('quoteinfo',$quoteinfo); //已投标
        }

        $this->assign('orderinfo',$OrderDetails);

        return $this->fetch();
    }

    /**
     * 获取供应商信息
     * @return \think\response\Json
     */
    public function getSup(){

        $data = Request::param();
        $orderInfo = ZbOrders::find($data['order_id']);
        /*判断过期时间*/
        if(strtotime($orderInfo['endtime']) < time()){
            return json(['msg'=>'timeout']);
        }
        //  只能投标自己的类目
        $supinfo = ZbSupplierCategory::where('catid','=',$orderInfo['cid'])->select();
        $flag = false;
        foreach ($supinfo as $sup){
            if($sup['supid'] == $this->account){
                $flag = true;
                break;
            }
        }
        if(!$flag){
            return json(['msg'=>'nocatid']);
        }

        $user = ZbSupplier::find($this->account);
        return json(['msg'=>$user]);
    }


}
