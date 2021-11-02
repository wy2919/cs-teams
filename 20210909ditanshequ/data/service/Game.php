<?php

namespace data\service;


class Game extends BaseService
{
    /**
     * 获取列表
     * @return array
     */
    public function getrData()
    {

        $where = [];


        $data = $this->data;
        if (!empty($data['title'])) {
            $where[] = ['title', 'like', '%' . $data['title'] . '%'];
        }




        $limit = $this->getLimit($data['limit']);
        $tablelist = $this->model->getTableList(['user'], '', $limit, [], $where);

        // 遍历获取分类
        for ($i = 0; $i < count($tablelist['data']); $i++) {
            $arr = model_s('Type', [['id', 'in',explode(',', $tablelist['data'][$i]['type'])]]);
            $str = [];
            foreach ($arr as $item) {
                array_push($str,$item['name']);
            }
            $tablelist['data'][$i]['type'] = join($str,'   ');
        }


        return json(['code' => 0, 'msg' => "", "count" => $tablelist['total'], 'data' => $tablelist['data']]);

    }


    /**
     * 添加
     * @return mixed
     */
    public function Add()
    {
        $data = input();



        if (empty($data['type'])){
            $data['type'] = '';
        }else{
            $data['type'] = join($data['type'], ',');
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


        if (empty($data['type'])){
            $data['type'] = '';
        }else{
            $data['type'] = join($data['type'], ',');
        }

        $result = $this->model->update($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改" . NAMECRUD . "：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改" . NAMECRUD . "：" . $data[FIELD] . "失败", $result);
    }


}