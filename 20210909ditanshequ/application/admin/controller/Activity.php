<?php

namespace app\admin\controller;

class Activity extends Base
{

    protected $name = '社区活动';         // log
    protected $field = 'name';       // logName


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

        return $this->fetch();
    }


    public function Del()
    {
        $this->ParamVerify->gocheck('id');
        $id = input('id');

        return $this->service->delDataByIds(CONTROLLER_NAME);
    }


}