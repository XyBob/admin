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

use app\admin\service\GameUserService;
use think\Exception;

class GameUser extends Base
{
    function index(){
        if(request()->isAjax()){
            $return = GameUserService::getUserListInfo();
            return json($return);
        }

        $type = input('param.search')?input('param.search'):'index';
        return $this->fetch($type);
    }


    function edit()
    {
        if(request()->isPost()){
            $param = input('post.');
            $id = $param['userId'];
            unset($param['userId']);
            try{
               if(GameUserService::editUserInfo($id,$param)){
                   return json(msg(1, '', '修改成功'));
               }else{
                   throw new Exception('无变动项', 1);
               }

            }catch (Exception $e){
                return json(msg(-1, '', '修改失败,原因:'.$e->getMessage()));
            }

        }

        $user_info = GameUserService::getUserInfo(input('param.id'));
        $this->assign([
            'user_info' => $user_info
        ]);
        return $this->fetch();
    }



}
