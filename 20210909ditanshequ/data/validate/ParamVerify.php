<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\validate;


class ParamVerify extends BaseValidate
{
    protected $batch = true;

    protected $rule = [
        'id'=>'require',
        'pid'=>'require',
        'bid'=>'require',
        'name'=>'require',
        'account'=>'require',
        'password'=>'require',
        'companyname'=>'require',
        'business_license'=>'require',
        'title'=>'require',
        'user'=>'require',
        'bnum'=>'require',
        'bunit_num'=>'require',
        'bfloor'=>'require',
        'bhouse'=>'require',
        'unit_num'=>'require',
        'rnum'=>'require',
        'floor'=>'require',
        'mianji'=>'require',
        'price'=>'require',
        'price_sum'=>'require',
        'pnum'=>'require',
        'huxing'=>'require',
        'huxingtype'=>'require',
        'file_path'=>'require',
        'xs_id'=>'require',
        'is_shenhe'=>'require',
    ];

    protected $message = [
        'id'=>[ 'require'=>['code'=>999,"message"=>'id不能为空'] ],
        'pid'=>[ 'require'=>['code'=>999,"message"=>'父级id不能为空'] ],
        'bid'=>[ 'require'=>['code'=>999,"message"=>'楼id不能为空'] ],
        'name'=>[ 'require'=>['code'=>999,"message"=>'名称不能为空']],
        'account'=>[ 'require'=>['code'=>999,"message"=>'供应商账号不能为空']],
        'password'=>[ 'require'=>['code'=>999,"message"=>'密码不能为空不能为空']],
        'companyname'=>[ 'require'=>['code'=>999,"message"=>'供应商公司名称不能为空']],
        'business_license'=>[ 'require'=>['code'=>999,"message"=>'供应商公司应用执照不能为空']],
        'title'=>[ 'require'=>['code'=>999,"message"=>'招标公告标题不能为空']],
        'user'=>[ 'require'=>['code'=>999,"message"=>'招标公告发布人不能为空']],
        'bnum'=>[ 'require'=>['code'=>999,"message"=>'楼号不能为空']],
        'bunit_num'=>[ 'require'=>['code'=>999,"message"=>'单元数不能为空']],
        'bfloor'=>[ 'require'=>['code'=>999,"message"=>'楼层不能为空']],
        'bhouse'=>[ 'require'=>['code'=>999,"message"=>'每层住户数量不能为空']],
        'unit_num'=>[ 'require'=>['code'=>999,"message"=>'单元不能为空']],
        'rnum'=>[ 'require'=>['code'=>999,"message"=>'储藏室编号不能为空']],
        'floor'=>[ 'require'=>['code'=>999,"message"=>'储藏室楼层不能为空']],
        'mianji'=>[ 'require'=>['code'=>999,"message"=>'储藏室面积不能为空']],
        'price'=>[ 'require'=>['code'=>999,"message"=>'储藏室单价不能为空']],
        'price_sum'=>[ 'require'=>['code'=>999,"message"=>'储藏室总价不能为空']],
        'pnum'=>[ 'require'=>['code'=>999,"message"=>'车位编号不能为空']],
        'huxing'=>[ 'require'=>['code'=>999,"message"=>'户型不能为空']],
        'huxingtype'=>[ 'require'=>['code'=>999,"message"=>'户型名称不能为空']],
        'file_path'=>[ 'require'=>['code'=>999,"message"=>'文件不能为空']],
        'xs_id'=>[ 'require'=>['code'=>999,"message"=>'销售id不能为空']],
        'is_shenhe'=>[ 'require'=>['code'=>999,"message"=>'审核结果不能为空']],
    ];

    /*场景*/
    protected $scene = [
        'id'  =>  ['id'],
        'pid'  =>  ['pid'],
        'ids'  =>  ['id','pid'],
        'project_add'  =>  ['pid','name'],
        'project_edit'  =>  ['id','pid','name'],
        'company_add'  =>  ['account','name','companyname','business_license'],
        'company_account'  =>  ['account'],
        'class_add'  =>  ['account'],
        'orders_add'  =>  ['pid','title','user'],
        'orders_edit'  =>  ['id','pid','title','user'],
        'index_login' => ['account','password'],
        'index_user_info' => ['account','name','companyname','address','catids'],
        'build_add' => ['pid','bnum','bunit_num','bfloor','bhouse'],
        'build_edit' => ['id','pid','bnum','bunit_num','bfloor','bhouse'],
        'build_info_edit' => ['pid','floor','bunit','bhouse','bnum'],
        'room_add' => ['pid','bnum','bid','unit_num','rnum','floor','mianji','price','price_sum'],
        'room_edit' => ['id','pid','bnum','bid','unit_num','rnum','floor','mianji','price','price_sum'],
        'park_add' => ['pid','pnum','price','price_sum'],
        'park_edit' => ['id','pid','pnum','price','price_sum'],
        'huxing_add' => ['pid','huxing','huxingtype'],
        'huxing_edit' => ['id','pid','huxing','huxingtype'],
        'source_add' => ['pid','file_path'],
        'source_edit' => ['id','pid','file_path'],
        'getHouseByBidUnitNum' => ['bid','unit_num'],
        'change_xiaoshou' => ['id','xs_id'],
        'change_shenhe' => ['id','is_shenhe'],
        'show_shenhe_info' => ['id','pid','bid'],
    ];
}