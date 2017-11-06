<?php
namespace app\admin\model;

use think\Model;

class GameUserModel extends Model
{
    // 确定链接表名
    protected $table = 'snake_game_user';

    /**
     * 查询用户
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getGameUserByWhere($where, $offset, $limit,$orderBy)
    {
        return $this->where($where)->limit($offset, $limit)->order($orderBy)->select();
    }

    /**
     * 根据搜索条件获取所有的文章数量
     * @param $where
     */
    public function getAllGameUser($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 添加文章
     * @param $param
     */
    public function addArticle($param)
    {
        try{
            $result = $this->validate('ArticleValidate')->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{

                return msg(1, url('articles/index'), '添加文章成功');
            }
        }catch (\Exception $e){
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 修改用户数据
     * @param $where
     */
    public function editUserInfo($id,$param)
    {
       return $this->where(['user_id'=>$id])->update($param);
    }


    /**
     * 编辑文章信息
     * @param $param
     */
    public function editArticle($param)
    {
        try{

            $result = $this->validate('ArticleValidate')->save($param, ['id' => $param['id']]);

            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{

                return msg(1, url('articles/index'), '编辑文章成功');
            }
        }catch(\Exception $e){
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 根据文章的id 获取文章的信息
     * @param $id
     */
    public function getOneUser($id)
    {
        return $this->where('user_id', $id)->find();
    }

}