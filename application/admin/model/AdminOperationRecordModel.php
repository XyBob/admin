<?php
namespace app\admin\model;

use think\Model;

class AdminOperationRecordModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_admin_operation_record';


    public function getRecordByWhere($where, $offset, $limit,$orderBy)
    {
        return $this->where($where)->limit($offset, $limit)->order($orderBy)->select();
    }


    public function getAllRecord($where)
    {
        return $this->where($where)->count();
    }

    public function addRecord($param)
    {

        return  $this->save($param);

    }


}