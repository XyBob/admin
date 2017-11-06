<?php
namespace app\admin\service;

use app\admin\model\GameUserModel;

class GameUserService
{
    /**
     * 获取用户信息,用于展示
     * @param $id
     * @return array
     */
    static function getUserInfo($id)
    {
        if (empty($id)) {
            return;
        }
        $game_user = new GameUserModel();
        $user_info = $game_user->getOneUser($id);
        if(empty($user_info)){
            return array();
        }
        $user_info['reg_time'] = date('Y-m-d H:i:s', $user_info['reg_time']);
        $user_info['last_login_time'] = date('Y-m-d H:i:s', $user_info['last_login_time']);
        $user_info['reg_ip'] = long2ip($user_info['reg_ip']);
        //中间穿插请求接口查询状态
        return $user_info;
    }

    /**
     * 获取用户列表信息,用于展示
     * @param $id
     * @return array
     */
    static function getUserListInfo()
    {

        $param = input('param.');

        $limit = $param['pageSize'];
        $offset = ($param['pageNumber'] - 1) * $limit;

        $where = self::filterWhere();
        $order = self::filterOrder();
        if (!empty($param['userId'])) {
            $where['user_id'] = $param['userId'];
        }
        if (!empty($param['nickname'])) {
            $where['nickname'] = ['like', '%' . $param['nickname'] . '%'];
        }
        if (!empty($param['alipay'])) {
            $where['alipay'] = $param['alipay'];
        }
        if (!empty($param['machineCode'])) {
            $where['machine_code'] = $param['machineCode'];
        }
        if (!empty($param['ip'])) {
            $where['ip'] = 'inet_aton(' . $param['ip'] . ')';
        }

        $GameUser = new GameUserModel();
        $selectResult = $GameUser->getGameUserByWhere($where, $offset, $limit, $order);
        foreach ($selectResult as $key => $vo) {
            $selectResult[$key]['reg_time'] = date('Y-m -d H:i:s', $vo['reg_time']);
            $selectResult[$key]['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
            $selectResult[$key]['operate'] = showOperate(self::makeButton($vo['user_id']));
        }
        $return['total'] = $GameUser->getAllGameUser($where);  // 总数据
        $return['rows'] = $selectResult;
        return $return;
    }

    /**
     * 保存用户信息
     * @param $param
     * @return array
     */
    static function editUserInfo($id, $param,$desc='')
    {
        if (!is_array($param)) {
            return false;
        }
        if (isset($param['reg_time'])) {
            $param['reg_time'] = strtotime($param['reg_time']);
        }
        if (isset($param['last_login_time'])) {
            $param['last_login_time'] = strtotime($param['last_login_time']);
        }

        $game_user = new GameUserModel();
        $param_before = $game_user->getOneUser($id);
        $result = $game_user->editUserInfo($id, $param);
        $last_sql = $game_user->getLastSql();
        $param_after = $game_user->getOneUser($id);
        saveOperationLog(1,$param_before,$param_after,$desc,$last_sql);
        return $result;
    }


    /**
     * 筛选post请求,并组成where数组
     * @return array
     */
    static function filterWhere()
    {
        if (!request()->isAjax()) {
            return '非法请求';
        }
        $param = input('param.');
        $where = [];
        //是否在线
        if (!empty($param['status'])) {
            $where['is_online'] = $param['status'];
        }

        //vip等级
        if (!empty($param['vipRankStart'])) {
            $where['vip_rank'] = ['egt', $param['vipRankStart']];
        }
        if (!empty($param['vipRankEnd'])) {
            $where['vip_rank'] = ['elt', $param['vipRankEnd']];
        }
        if (!empty($param['vipRankEnd']) && !empty($param['vipRankStart'])) {
            $where['vip_rank'] = [['egt', $param['vipRankStart']], ['elt', $param['vipRankEnd']]];
        }

        //金币
        if (!empty($param['coinStart'])) {
            $where['coin'] = ['egt', $param['coinStart']];
        }
        if (!empty($param['coinEnd'])) {
            $where['coin'] = ['elt', $param['coinEnd']];
        }
        if (!empty($param['coinEnd']) && !empty($param['coinStart'])) {
            $where['coin'] = [['egt', $param['coinStart']], ['elt', $param['coinEnd']]];
        }

        //总成绩赢
        if (!empty($param['achievementWinStart'])) {
            $where['total_win'] = ['egt', $param['achievementWinStart']];
        }
        if (!empty($param['achievementWinEnd'])) {
            $where['total_win'] = ['elt', $param['achievementWinEnd']];
        }
        if (!empty($param['achievementWinEnd']) && !empty($param['achievementWinStart'])) {
            $where['total_win'] = [['egt', $param['achievementWinStart']], ['elt', $param['achievementWinEnd']]];
        }

        //总成绩输
        if (!empty($param['achievementLoseStart'])) {
            $where['total_lose'] = ['egt', $param['achievementLoseStart']];
        }
        if (!empty($param['achievementLoseEnd'])) {
            $where['total_lose'] = ['elt', $param['achievementLoseEnd']];
        }
        if (!empty($param['achievementLoseStart']) && !empty($param['achievementLoseStart'])) {
            $where['total_win'] = [['egt', $param['achievementLoseStart']], ['elt', $param['achievementLoseStart']]];
        }

        //总充值
        if (!empty($param['rechargeStart'])) {
            $where['total_recharge'] = ['egt', $param['rechargeStart']];
        }
        if (!empty($param['rechargeEnd'])) {
            $where['total_recharge'] = ['elt', $param['rechargeEnd']];
        }
        if (!empty($param['rechargeStart']) && !empty($param['rechargeEnd'])) {
            $where['total_recharge'] = [['egt', $param['rechargeStart']], ['elt', $param['rechargeEnd']]];
        }

        //总兑换
        if (!empty($param['exchangeStart'])) {
            $where['total_exchange'] = ['egt', $param['exchangeStart']];
        }
        if (!empty($param['exchangeEnd'])) {
            $where['total_exchange'] = ['elt', $param['exchangeEnd']];
        }
        if (!empty($param['exchangeStart']) && !empty($param['exchangeEnd'])) {
            $where['total_exchange'] = [['egt', $param['exchangeStart']], ['elt', $param['exchangeEnd']]];
        }

        //总福利
        if (!empty($param['welfareStart'])) {
            $where['total_welfare'] = ['egt', $param['welfareStart']];
        }
        if (!empty($param['welfareEnd'])) {
            $where['total_welfare'] = ['elt', $param['welfareEnd']];
        }
        if (!empty($param['welfareStart']) && !empty($param['welfareEnd'])) {
            $where['total_welfare'] = [['egt', $param['welfareStart']], ['elt', $param['welfareEnd']]];
        }

        //账号类型
        if (!empty($param['user_type'])) {
            $where['type'] = $param['user_type'];
        }

        //等级
        if (!empty($param['RankStart'])) {
            $where['rank'] = ['egt', $param['RankStart']];
        }
        if (!empty($param['RankEnd'])) {
            $where['rank'] = ['elt', $param['RankEnd']];
        }
        if (!empty($param['RankStart']) && !empty($param['RankEnd'])) {
            $where['rank'] = [['egt', $param['RankStart']], ['elt', $param['RankEnd']]];
        }

        //注册时间
        if (!empty($param['dateStart'])) {
            $where['reg_time'] = ['egt', strtotime($param['dateStart'])];
        }
        if (!empty($param['dateEnd'])) {
            $where['reg_time'] = ['elt', strtotime($param['dateEnd'])];
        }
        if (!empty($param['dateStart']) && !empty($param['dateEnd'])) {
            $where['reg_time'] = [['egt', strtotime($param['dateStart'])], ['elt', strtotime($param['dateEnd'])]];
        }
        return $where;
    }

    /**
     * 筛选post请求,返回排序规则
     * @return array
     */
    static function filterOrder()
    {
        $param = input('param.');
        switch ($param['search']) {
            case 'vip':
                return 'vip_rank desc';
                break;
            case 'coin':
                return 'coin desc';
                break;
            case 'achievement_win':
                return 'total_win desc';
                break;
            case 'achievement_lose':
                return 'total_lose desc';
                break;
            case 'recharge':
                return 'total_recharge desc';
                break;
            case 'exchange desc':
                return 'total_exchange desc';
                break;
            case 'welfare':
                return 'total_recharge desc';
                break;
            case 'type':
                return 'type desc';
                break;
            case 'grade':
                return 'rank desc';
                break;
            case 'reg_time':
                return 'reg_time desc';
                break;
            default:
                return 'coin desc';
                break;
        }
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    static function makeButton($id)
    {
        return [
            '详情' => [
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
}