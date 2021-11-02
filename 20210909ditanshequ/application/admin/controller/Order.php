<?php

namespace app\admin\controller;

class Order extends Base
{

    protected $name = '订单';         // log
    protected $field = 'id';       // logName


    public function List()
    {
        if (input('page')) {
            return $this->service->getrData();
        }

        return $this->fetch();
    }


    public function Edit()
    {
        $this->ParamVerify->gocheck('id');

        if (IS_POST) {
            return $this->service->Edit();
        }

        $info = $this->service->getById();



        $this->assign([
            'info' => $info,
        ]);


        return $this->fetch();
    }


    public function Add()
    {

        if (IS_POST) {
            return $this->service->Add();
        }


//        $this->assign([
//            'data' => model('Letter')->select(),
//            'zz' =>$this->getModel_s('Article',[['type1','=',1]])
//        ]);
        return $this->fetch();
    }


    public function Del()
    {
        $this->ParamVerify->gocheck('id');
        $id = input('id');


        return $this->service->delDataByIds(CONTROLLER_NAME);
    }


}