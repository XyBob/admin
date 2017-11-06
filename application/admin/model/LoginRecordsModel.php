<?php
namespace app\admin\model;

use think\Model;

class LoginRecordsModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_login_record';

    public function __construct($tableSuffix = '')
    {
        $this->table = $tableSuffix ? $this->table.'_'.$tableSuffix : $this->table;
    }

    /**
     * 查询记录
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getAccountChangeRecordByWhere($where, $offset, $limit,$orderBy='')
    {
        $data = $this->where($where)->limit($offset, $limit)->order($orderBy)->select();
        $count = $this->where($where)->count();
        return [$data,$count];
    }

}