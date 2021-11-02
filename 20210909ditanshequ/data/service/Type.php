<?php

namespace data\service;


class Type extends BaseService
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

        if (!empty($data['type'])) {
            $where[] = ['type', 'like', '%' . $data['type'] . '%'];
        }


        $limit = $this->getLimit($data['limit']);
        $tablelist = $this->model->getTableList([], '', $limit, [], $where);



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