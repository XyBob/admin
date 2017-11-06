<?php
namespace app\admin\model;

use think\Model;

class RechargeRecordModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_recharge_record';


    /**
     * 查询记录
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getRechargeRecordByWhere($where, $offset, $limit,$orderBy='')
    {
        return $this->where($where)->limit($offset, $limit)->order($orderBy)->select();
    }

    public function getAllRechargeRecord($where = '')
    {
        return $this->where($where)->count();
    }

}