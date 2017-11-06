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
namespace app\admin\model;

use think\Model;

class RoleModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_role';

    /**
     * 根据搜索条件获取角色列表信息
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getRoleByWhere($where, $offset, $limit)
    {

        return $this->where($where)->limit($offset, $limit)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的角色数量
     * @param $where
     */
    public function getAllRole($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入角色信息
     * @param $param
     */
    public function insertRole($param)
    {
        try{

            $result =  $this->validate('RoleValidate')->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{
                //添加操作日志
                $param_after = $param;
                saveOperationLog(2,null,$param_after,'',$this->getLastSql());
                return msg(1, url('role/index'), '添加角色成功');
            }
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑角色信息
     * @param $param
     */
    public function editRole($param)
    {
        try{
            $param_before = $this->getOneRole($param['id']);
            $result = $this->validate('RoleValidate')->save($param, ['id' => $param['id']]);
            $lastSql = $this->getLastSql();
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{
                //添加操作日志
                $param_after = $this->getOneRole($param['id']);
                saveOperationLog(1,$param_before,$param_after,'',$lastSql);
                return msg(1, url('role/index'), '编辑角色成功');
            }
        }catch(PDOException $e){
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 根据角色id获取角色信息
     * @param $id
     */
    public function getOneRole($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除角色
     * @param $id
     */
    public function delRole($id)
    {
        try{
            //添加操作日志
            $param_before = $this->getOneRole($id);
            $this->where('id', $id)->delete();
            saveOperationLog(3,$param_before,null,'',$this->getLastSql());
            return msg(1, '', '删除角色成功');

        }catch(PDOException $e){
            return msg(-1, '', $e->getMessage());
        }
    }

    // 获取所有的角色信息
    public function getRole()
    {
        return $this->select();
    }

    // 获取角色的权限节点
    public function getRuleById($id)
    {
        $res = $this->field('rule')->where('id', $id)->find();

        return $res['rule'];
    }

    /**
     * 分配权限
     * @param $param
     */
    public function editAccess($param)
    {
        try{
            $param_before = $this->getOneRole($param['id']);
            $this->save($param, ['id' => $param['id']]);
            $lastSql = $this->getLastSql();
            //添加操作日志
            $param_after = $this->getOneRole($param['id']);
            saveOperationLog(1,$param_before,$param_after,'',$lastSql);
            return msg(1, '', '分配权限成功');

        }catch(PDOException $e){
            return msg(-1, '', $e->getMessage());
        }
    }

    /**
     * 获取角色信息
     * @param $id
     */
    public function getRoleInfo($id)
    {
        $result = $this->where('id', $id)->find()->toArray();
        // 超级管理员权限是 *
        if(empty($result['rule'])){
            $result['action'] = '';
            return $result;
        }else if('*' == $result['rule']){
            $where = '';
        }else{
            $where = 'id in(' . $result['rule'] . ')';
        }

        // 查询权限节点
        $nodeModel = new NodeModel();
        $res = $nodeModel->getActions($where);
        foreach($res as $key=>$vo){

            if('#' != $vo['action_name']){
                $result['action'][] = strtolower($vo['control_name']) . '/' . strtolower($vo['action_name']);
            }
        }
        return $result;
    }
}