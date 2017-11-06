<?php
namespace app\admin\model;

use think\Model;

class GamblingRecordModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_gambling_record';

    public function __construct($tableSuffix)
    {
        $this->table = $tableSuffix ? $this->table.'_'.$tableSuffix : $this->table;
    }

    /**
     * 查询记录
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getGamblingRecordByWhere($where, $offset, $limit,$orderBy='')
    {
        return $this->where($where)->limit($offset, $limit)->order($orderBy)->select();
    }

    public function getAllGamblingRecord($where = '')
    {
        return $this->where($where)->count();
    }

}