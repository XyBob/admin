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
namespace app\admin\controller;
use app\admin\service\RecordService;
use app\admin\model\GamblingRecordModel;
use app\admin\model\LoginRecordsModel;
use app\admin\model\ExchangeRecordModel;
use app\admin\model\RechargeRecordModel;
class Record extends Base
{    
    /**
     * 账号变更记录
     * @return json
     */
    public function accountRecord()
    {
        if(request()->isAjax()){
            $param = input('param.');
            $data = RecordService::getAccountRecord($param);
            return json($data);
        }

        return $this->fetch();
    }

    /**
     * 登录记录
     * @return json
     */
    public function loginRecord()
    {
         if(request()->isAjax()){
            $param = input('param.');
            $data = RecordService::getLoginRecord($param);
            return json($data);
        }

        return $this->fetch();
    }

    /**
     * 游戏记录
     * @return json
     */
    public function gameRecord()
    {
        if(request()->isAjax()){
            $param = input('param.');
            $data = RecordService::getGameRecord($param);
            return json($data);
        }

        return $this->fetch();
    }

    /**
     * 牌局记录
     * @return array
     */
    public function gamblingRecord()
    {
        if(request()->isAjax()){
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = RecordService::filterGamblingWhere();
            $date = input('param.date');
            $date = $date?date('Y_m_d',strtotime($date)):date('Y_m_d');
            $GamblingRecordModel = new GamblingRecordModel($date);
            $selectResult = $GamblingRecordModel->getGamblingRecordByWhere($where, $offset, $limit,'time desc');
            $selectResult = array_map(function($arr){
                $arr['time'] = date('Y-m-d H:i:s',$arr['time']);
                return $arr;
            },$selectResult);
            $return['total'] = $GamblingRecordModel->getAllGamblingRecord($where);  // 总数据
            $return['rows'] = $selectResult;
            return json($return);
        }
        $gambling_id = input('param.gambling_id');
        $date = input('param.date');
        $this->assign([
            'gambling_id' => $gambling_id,
            'date' =>$date?$date:'',
        ]);
        return $this->fetch();
    }

    /**
     * 兑换记录
     * @return array
     */
    public function exchange()
    {
        if(request()->isAjax()){
            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = RecordService::filterExchangeWhere();
            $ExchangeRecordModel = new ExchangeRecordModel();
            $selectResult = $ExchangeRecordModel->getExchangeRecordByWhere($where, $offset, $limit);
            $selectResult = array_map(function($arr){
                $arr['time'] = date('Y-m-d H:i:s',$arr['time']);
                $arr['ip'] = long2ip($arr['ip']);
                $arr['operate'] = showOperate(RecordService::makeDetailButton($arr['user_id']));
                return $arr;
            },$selectResult);
            $return['total'] = $ExchangeRecordModel->getAllExchangeRecord($where);
            $return['rows'] = $selectResult;
            return json($return);

        }
        $user_id = input('param.user_id');
        $this->assign([
            'user_id' => $user_id
        ]);
        return $this->fetch();
    }

    /**
     * 充值记录
     * @return array
     */
    public function rechargeRecord(){
        if(request()->isAjax()){
            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = RecordService::filterRechargeWhere();
            $RechargeRecordModel = new RechargeRecordModel();
            $selectResult = $RechargeRecordModel->getRechargeRecordByWhere($where, $offset, $limit);
            $selectResult = array_map(function($arr){
                $arr['time'] = date('Y-m-d H:i:s',$arr['time']);
                $arr['ip'] = long2ip($arr['ip']);
                $arr['operate'] = showOperate(RecordService::makeDetailButton($arr['user_id']));
                return $arr;
            },$selectResult);
            $return['total'] = $RechargeRecordModel->getAllRechargeRecord($where);
            $return['rows'] = $selectResult;
            return json($return);

        }
        $user_id = input('param.user_id');
        $this->assign([
            'user_id' => $user_id
        ]);
        return $this->fetch();
    }


}
