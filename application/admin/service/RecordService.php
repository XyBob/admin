<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\service;

use app\admin\model\AccountChangeRecordModel;
use app\admin\model\GameRecordModel;
use app\admin\model\LoginRecordsModel;

class RecordService
{

    static function filterGamblingWhere()
    {
        $param = input('param.');
        $where = [];
        if (!empty($param['gamblingId'])) {
            $where['gambling_id'] = $param['gamblingId'];
        }
        return $where;

    }

    static function filterExchangeWhere()
    {
        $param = input('param.');
        $where = [];
        if (!empty($param['userId'])) {
            $where['user_id'] = $param['userId'];
        }
        return $where;

    }

    static function filterRechargeWhere()
    {
        $param = input('param.');
        $where = [];
        if (!empty($param['userId'])) {
            $where['user_id'] = $param['userId'];
        }
        return $where;

    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    static function makeDetailButton($id)
    {
        return [
            '用户详情' => [
                'auth' => 'gameUser/edit',
                'href' => url('gameUser/edit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            /* '删除' => [
                 'auth' => 'articles/articledel',
                 'href' => "javascript:articleDel(" . $id . ")",
                 'btnStyle' => 'danger',
                 'icon' => 'fa fa-trash-o'
             ]*/
        ];
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    static function makeGamblingDetailButton($id,$gambling_id,$date)
    {
        return [
            '用户详情' => [
                'auth' => 'gameUser/edit',
                'href' => url('gameUser/edit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
             '牌局详情' => [
                 'auth' => 'record/gamblingrecord',
                 'href' => url('record/gamblingrecord', ['gambling_id' => $gambling_id,'date'=>$date]),
                 'btnStyle' => 'primary',
                 'icon' => 'fa fa-paste',
             ]
        ];
    }

    public static function getAccountRecord($param)
    {
        $where = [];
        if (!empty($param['userId'])) {
            $where['user_id'] = $param['userId'];
        }
        $date = (isset($param['date']) && !empty($param['date'])) ? date('Y_m_d',strtotime($param['date'])) : date('Y_m_d');

        $limit = ($param['pageSize'] && $param['pageSize'] > 0) ? $param['pageSize'] : 20;
        $param['pageNumber'] = ($param['pageNumber'] && $param['pageNumber'] > 0) ? $param['pageNumber'] : 1;
        $offset = ($param['pageNumber'] - 1) * $limit;

        $accountChangeRecordModel = new AccountChangeRecordModel($date);
        $selectResult = $accountChangeRecordModel->getAccountChangeRecordByWhere($where, $offset, $limit);
        foreach($selectResult[0] as $key=>$value) {
            $selectResult[0][$key]['time'] = date('Y-m-d H:i:s',$value['create_time']);
            $selectResult[0][$key]['ip'] = long2ip($value['ip']);
            $selectResult[0][$key]['operate'] = showOperate(self::makeDetailButton($value['user_id']));
        }
        return ['rows'=>$selectResult[0],'total'=>$selectResult[1]];
    }

    public static function getGameRecord($param)
    {
        $where = [];
        if (!empty($param['userId'])) {
            $where['user_id'] = $param['userId'];
        }
        $date = (isset($param['date']) && !empty($param['date'])) ? date('Y_m_d',strtotime($param['date'])) : date('Y_m_d');
        $date_format = (isset($param['date']) && !empty($param['date'])) ? date('Y-m-d',strtotime($param['date'])) : date('Y-m-d');
        $limit = ($param['pageSize'] && $param['pageSize'] > 0) ? $param['pageSize'] : 20;
        $param['pageNumber'] = ($param['pageNumber'] && $param['pageNumber'] > 0) ? $param['pageNumber'] : 1;
        $offset = ($param['pageNumber'] - 1) * $limit;

        $gameRecordModel = new GameRecordModel($date);
        $selectResult = $gameRecordModel->getAccountChangeRecordByWhere($where, $offset, $limit);
        foreach($selectResult[0] as $key=>$value) {
            $selectResult[0][$key]['time'] = date('Y-m-d H:i:s',$value['create_time']);
            $selectResult[0][$key]['ip'] = long2ip($value['ip']);
            $selectResult[0][$key]['operate'] = showOperate(self::makeGamblingDetailButton($value['user_id'],$value['gambling_id'],$date_format));
        }
        return ['rows'=>$selectResult[0],'total'=>$selectResult[1]];
    }

    public static function getLoginRecord($param)
    {
        $where = [];
        if (!empty($param['userId'])) {
            $where['user_id'] = $param['userId'];
        }
        $date = (isset($param['date']) && !empty($param['date'])) ? date('Y_m_d',strtotime($param['date'])) : date('Y_m_d');

        $limit = ($param['pageSize'] && $param['pageSize'] > 0) ? $param['pageSize'] : 20;
        $param['pageNumber'] = ($param['pageNumber'] && $param['pageNumber'] > 0) ? $param['pageNumber'] : 1;
        $offset = ($param['pageNumber'] - 1) * $limit;

        $loginRecordsModel = new LoginRecordsModel();
        $selectResult = $loginRecordsModel->getAccountChangeRecordByWhere($where, $offset, $limit);
        foreach($selectResult[0] as $key=>$value) {
            $selectResult[0][$key]['time'] = date('Y-m-d H:i:s',$value['create_time']);
            $selectResult[0][$key]['ip'] = long2ip($value['ip']);
            $selectResult[0][$key]['operate'] = showOperate(self::makeDetailButton($value['user_id']));
        }
        return ['rows'=>$selectResult[0],'total'=>$selectResult[1]];
    }
}
