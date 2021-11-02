<?php

namespace data\service;


class Pinglun extends BaseService
{
    /**
     * 获取列表
     * @return array
     */
    public function getrData()
    {

        $where = [];


        $data = $this->data;
        if (!empty($data['content'])) {
            $where[] = ['content', 'like', '%' . $data['content'] . '%'];
        }


        $where[] = ['aid', '=', $data['aid'] ];
        $where[] = ['type', '=', $data['type'] ];


        $limit = $this->getLimit($data['limit']);
        $tablelist = $this->model->getTableList(['user'], '', $limit, [], $where);

        return json(['code' => 0, 'msg' => "", "count" => $tablelist['total'], 'data' => $tablelist['data']]);

    }


    /**
     * 添加
     * @return mixed
     */
    public function Add()
    {
        $data = input();

        if (!empty($data['imgs'])) {
            $data['imgs'] = join($data['imgs'], ',');
        } else {
            $data['imgs'] = '';
        }

        $result = $this->model->allowField(true)->save($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "添加" . NAMECRUD . "：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "添加" . NAMECRUD . "：" . $data[FIELD] . "失败", $result);
    }

    /**
     * 修改
     * @return mixed
     */
    public function Edit()
    {
        $data = input();

        if (!empty($data['imgs'])) {
            $data['imgs'] = join($data['imgs'], ',');
        } else {
            $data['imgs'] = '';
        }

        $result = $this->model->update($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改" . NAMECRUD . "：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改" . NAMECRUD . "：" . $data[FIELD] . "失败", $result);
    }


}