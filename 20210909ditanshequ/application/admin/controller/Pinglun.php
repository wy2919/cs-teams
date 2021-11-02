<?php

namespace app\admin\controller;

class Pinglun extends Base
{

    protected $name = '评论';         // log
    protected $field = 'content';       // logName


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

        $info['begin'] = date('Y-m-d h:i:s',$info['begin']);
        $info['finish'] = date('Y-m-d h:i:s',$info['finish']);

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