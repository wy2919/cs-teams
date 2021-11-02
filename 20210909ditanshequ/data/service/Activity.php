<?php

namespace data\service;


class Activity extends BaseService
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


        if (!empty($data['site'])) {
            $where[] = ['site', 'like', '%' . $data['site'] . '%'];
        }


        if (!empty($data['state'])) {

            if ($data['site'] == 1) {
              $where[] = ['begin', '<',  time()];
              $where[] = ['finish', '>',  time()];
            }

            if ($data['site'] == 2) {
                $where[] = ['begin', '>',  time()];
                $where[] = ['finish', '<',  time()];
            }
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