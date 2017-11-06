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

use app\admin\model\NodeModel;

class Node extends Base
{
    // 节点列表
    public function index()
    {
        if(request()->isAjax()){

            $node = new NodeModel();
            $nodes = $node->getNodeList();

            $nodes = getTree(objToArray($nodes), false);
            return json(msg(1, $nodes, 'ok'));
        }

        return $this->fetch();
    }

    // 添加节点
    public function nodeAdd()
    {
        $param = input('post.');

        $node = new NodeModel();
        $flag = $node->insertNode($param);
        $last_sql = $node->getLastSql();
        $id = $node->getLastInsID();
        $after_param = $node->where(['id'=>$id])->find();
        saveOperationLog(2,'',$after_param,'',$last_sql);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 编辑节点
    public function nodeEdit()
    {
        $param = input('post.');

        $node = new NodeModel();
        $before_param = $node->where(['id'=>$param['id']])->find();
        $flag = $node->editNode($param);
        $last_sql = $node->getLastSql();
        $after_param = $node->where(['id'=>$param['id']])->find();
        saveOperationLog(1,$before_param,$after_param,'',$last_sql);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 删除节点
    public function nodeDel()
    {
        $id = input('param.id');

        $role = new NodeModel();
        $before_param = $role->where(['id'=>$id])->find();
        $flag = $role->delNode($id);
        $last_sql = $role->getLastSql();
        saveOperationLog(3,$before_param,'','',$last_sql);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }
}