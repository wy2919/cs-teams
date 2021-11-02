<?php
/**
 * Created by yongjiapei.
 * User: MSI_PhpStorm
 */

namespace data\model;


use think\Model;

class BaseModel extends Model
{


    /**
     * 查询分页列表
     * @param array $with
     * @param array $where
     * @param array $whereOr
     * @param string $field
     * @param int $pageSize
     * @param array $order
     * @return array
     */
    public function getTableList($with = [],$field='*', $pageSize = 10,  $order = [], $where=[],$whereOr=[]){
        $model = $this->with($with);
        if(!is_array($order)){
            $model = $model->orderRaw($order); //执行排序方法 orderRaw('rand()') 随机调取
        }else if(count($order)>0){
            $model = $model->order($order);
        }else{
            $model = $model->order(['create_time' => 'desc']);
        }
        if(count($where)>0 && count($whereOr)>0){
            return $model->where($where)->where(function ($query) use($whereOr){
                $query->whereOr($whereOr);
            })->field($field)->paginate($pageSize,false,['query' => request()->param()])->toArray();
        }else if(count($where)>0){
            return $model->where($where)->field($field)->paginate($pageSize,false,['query' => request()->param()])->toArray();
        }else if(count($whereOr)>0){
            return $model->whereOr($whereOr)->field($field)->paginate($pageSize,false,['query' => request()->param()])->toArray();
        }else{
            return $model->field($field)->paginate($pageSize,false,['query' => request()->param()])->toArray();
        }
    }

    /**
     * 查询所有信息
     * @param array $with
     * @param string $field
     * @param array $order
     * @param array $where
     * @param array $whereOr
     * @return array
     */
    public function getTableAll($with = [],$field='*',  $order = [], $where=[],$whereOr=[]){
        $model = $this->with($with);
        if(count($order)>0){
            $model = $model->order($order);
        } else{
            $model = $model->order(['create_time' => 'desc']);
        }

        if(count($where)>0 && count($whereOr)>0){
            return $model->where($where)->where(function ($query) use($whereOr){
                $query->whereOr($whereOr);
            })->field($field)->select()->toArray();
        }else if(count($where)>0){
            return $model->where($where)->field($field)->select()->toArray();
        }else if(count($whereOr)>0){
            return $model->whereOr($whereOr)->field($field)->select()->toArray();
        }else{
            return $model->field($field)->select()->toArray();
        }
//
//        if(count($where)>0) $model = $model->where([$where]);
//        if(count($whereOr)>0)  $model = $model->whereOr([$whereOr]);
//        return $model->field($field)->select()->toArray();
    }
}