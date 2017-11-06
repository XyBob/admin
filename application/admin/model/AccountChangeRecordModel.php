<?php
namespace app\admin\model;

use think\Model;

class AccountChangeRecordModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_account_change_record';


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