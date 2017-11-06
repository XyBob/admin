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

class UserModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_user';

    /**
     * 根据搜索条件获取用户列表信息
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getUsersByWhere($where, $offset, $limit)
    {
        return $this->field($this->table . '.*,role_name')
            ->join('snake_role', $this->table . '.role_id = ' . 'snake_role.id')
            ->where($where)->limit($offset, $limit)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的用户数量
     * @param $where
     */
    public function getAllUsers($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入管理员信息
     * @param $param
     */
    public function insertUser($param)
    {
        try{

            $result =  $this->validate('UserValidate')->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{
                //添加操作日志
                $param_after = $param;
                saveOperationLog(2,null,$param_after,'',$this->getLastSql());
                return msg(1, url('user/index'), '添加用户成功');
            }
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑管理员信息
     * @param $param
     */
    public function editUser($param)
    {
        try{
            $param_before = $this->getOneUser($param['id']);
            $result =  $this->validate('UserValidate')->save($param, ['id' => $param['id']]);
            $lastSql = $this->getLastSql();
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{
                //添加操作日志
                $param_after = $this->getOneUser($param['id']);
                saveOperationLog(1,$param_before,$param_after,'',$lastSql);
                return msg(1, url('user/index'), '编辑用户成功');
            }
        }catch(PDOException $e){
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 根据管理员id获取角色信息
     * @param $id
     */
    public function getOneUser($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除管理员
     * @param $id
     */
    public function delUser($id)
    {
        try{
            //添加操作日志
            $param_before = $this->getOneUser($id);
            $this->where('id', $id)->delete();
            saveOperationLog(3,$param_before,null,'',$this->getLastSql());
            return msg(1, url('user/index'), '添加用户成功');
            return msg(1, '', '删除管理员成功');

        }catch( PDOException $e){
            return msg(-1, '', $e->getMessage());
        }
    }

    /**
     * 根据用户名获取管理员信息
     * @param $name
     */
    public function findUserByName($name)
    {
        return $this->where('user_name', $name)->find();
    }

    /**
     * 更新管理员状态
     * @param array $param
     */
    public function updateStatus($param = [], $uid)
    {
        try{

            $this->where('id', $uid)->update($param);
            return msg(1, '', 'ok');
        }catch (\Exception $e){

            return msg(-1, '', $e->getMessage());
        }
    }
}