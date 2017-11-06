<?php
namespace app\admin\model;

use think\Model;

class ExchangeRecordModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_exchange_record';


    /**
     * 查询记录
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getExchangeRecordByWhere($where, $offset, $limit,$orderBy='')
    {
        return $this->where($where)->limit($offset, $limit)->order($orderBy)->select();
    }

    public function getAllExchangeRecord($where = '')
    {
        return $this->where($where)->count();
    }

}