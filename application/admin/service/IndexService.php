<?php
namespace app\admin\service;

use app\admin\model\AdminOperationRecordModel;
use app\admin\model\GameUserModel;
use app\admin\model\NodeModel;
use app\admin\model\UserModel;

class IndexService
{
    static function getAdminRecord(){
        $param = input('param.');

        $limit = $param['pageSize'];
        $offset = ($param['pageNumber'] - 1) * $limit;
        $where = self::filterWhere();
        $UerModel = new UserModel();
        $NodeModel = new NodeModel();
        $AdminOperationRecord = new AdminOperationRecordModel();
        $selectResult = $AdminOperationRecord->getRecordByWhere($where, $offset, $limit, 'create_time desc');
        $selectResult = array_map(function($arr) use($UerModel,$NodeModel){
            $user = $UerModel->getOneUser($arr['user_id']);
            $arr['user'] = $user['user_name'];
            $before_vars = json_decode($arr['before_vars'],true);
            $after_vars = json_decode($arr['after_vars'],true);
            if($arr['type'] ==1){
                $arr['before_vars'] = json_encode(array_diff_assoc($before_vars,$after_vars),JSON_UNESCAPED_UNICODE );
                $arr['after_vars'] = json_encode(array_diff_assoc($after_vars,$before_vars),JSON_UNESCAPED_UNICODE);
            }else{
                $arr['before_vars'] = json_encode($before_vars,JSON_UNESCAPED_UNICODE );
                $arr['after_vars'] = json_encode($after_vars,JSON_UNESCAPED_UNICODE);
            }
            $arr['type'] = config('operationRecord.'.$arr['type']);
            $arr['create_time'] = date('Y-m-d H:i:s',$arr['create_time']);

            $info = $NodeModel->getOneNodeInfo(['control_name'=>$arr['action_name'],'action_name'=>$arr['method']]);
            $arr['behaviour'] = $info['node_name'];
            return $arr;
        },$selectResult);
        $return['total'] = $AdminOperationRecord->getAllRecord($where);  // 总数据
        $return['rows'] = $selectResult;
        return $return;
    }

    static private function filterWhere(){
        $param = input('param.');
        $where = [];
        if (!empty($param['dateStart'])) {
            $where['create_time'] = ['egt', strtotime($param['dateStart'])];
        }
        if (!empty($param['dateEnd'])) {
            $where['create_time'] = ['elt', strtotime($param['dateEnd'])];
        }
        if (!empty($param['dateStart']) && !empty($param['dateEnd'])) {
            $where['create_time'] = [['egt', strtotime($param['dateStart'])], ['elt', strtotime($param['dateEnd'])]];
        }
        return $where;
    }
}