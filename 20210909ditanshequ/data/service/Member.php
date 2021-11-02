<?php

namespace data\service;


class Member extends BaseService
{
    /**
     * 获取列表
     * @return array
     */
    public function getrData()
    {

        $where = [];


        $data = $this->data;
        if (!empty($data['name'])) {
            $where[] = ['name', 'like', '%' . $data['name'] . '%'];
        }

        if (!empty($data['administrator'])) {
            $where[] = ['administrator', '=', $data['administrator']];
        }

        if (!empty($data['forbidden'])) {
            $where[] = ['forbidden', '=', $data['forbidden']];
        }

        if (!empty($data['z_name'])) {
            $where[] = ['z_name', 'like', '%' . $data['z_name'] . '%'];
        }

        if (!empty($data['z_phone'])) {
            $where[] = ['z_phone', 'like', '%' . $data['z_phone'] . '%'];
        }



        $limit = $this->getLimit($data['limit']);
        $tablelist = $this->model->getTableList([], '', $limit, [], $where);

//        for ($i = 0; $i < count($tablelist['data']); $i++) {
//            // 获取浏览量
//            $tablelist['data'][$i]['ll'] = model_s('Dianzan', [['type', '=', 13], ['artid', '=', $tablelist['data'][$i]['id']]], 2)->count();
//        }


        return json(['code' => 0, 'msg' => "", "count" => $tablelist['total'], 'data' => $tablelist['data']]);

    }


    /**
     * 添加
     * @return mixed
     */
    public function Add()
    {
        $data = input();
        $result = $this->model->allowField(true)->save($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "添加".NAMECRUD."：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "添加".NAMECRUD."：" . $data[FIELD] . "失败", $result);
    }

    /**
     * 修改
     * @return mixed
     */
    public function Edit()
    {
        $data = input();


        $result = $this->model->update($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改".NAMECRUD."：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改".NAMECRUD."：" . $data[FIELD] . "失败", $result);
    }


}