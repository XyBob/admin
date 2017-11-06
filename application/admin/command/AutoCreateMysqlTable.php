<?php
namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class AutoCreateMysqlTable extends Command
{
    protected function configure()
    {
        $this->setName('AutoCreateMysqlTable')->setDescription('每天自动创建数据表');
    }

    protected function execute(Input $input, Output $output)
    {
        //创建数据库连接
        $db_instance = Db::connect(config('database'));

        // 检测数据库连接
        try{
            $db_instance->execute('select version()');
        }catch(\Exception $e){
            $this->error('数据库连接失败，请检查数据库配置！');
        }

        // 创建数据表
        $sql1 = $this->gamblingRecordSqlTemplate();
        $sql2 = $this->gameRecordSqlTemplate();
        $sql3 = $this->loginRecordSqlTemplate();
        $result = '';
        $error = '';
        try{
            $db_instance->execute($sql1);
            $result .= '牌局记录表创建成功|time:'.date('Y-m-d H:i:s')."\n";
        }catch(\Exception $e){
            $result .= '牌局记录表创建失败|time:'.date('Y-m-d H:i:s')."|原因:".$e->getMessage()."\n";
            $error .= '牌局记录表创建失败|time:'.date('Y-m-d H:i:s')."|原因:".$e->getMessage()."<br/>";
        };

        try{
            $db_instance->execute($sql2);
            $result .= '游戏记录表创建成功|time:'.date('Y-m-d H:i:s')."\n";
        }catch(\Exception $e){
            $result .= '游戏记录表创建失败|time:'.date('Y-m-d H:i:s')."|原因:".$e->getMessage()."\n";
            $error .= '游戏记录表创建失败|time:'.date('Y-m-d H:i:s')."|原因:".$e->getMessage()."<br/>";
        };

        try{
            $db_instance->execute($sql3);
            $result .= '登录记录表创建成功|time:'.date('Y-m-d H:i:s')."\n";
        }catch(\Exception $e){
            $result .= '登录记录表创建失败|time:'.date('Y-m-d H:i:s')."|原因:".$e->getMessage()."\n";
            $error .= '登录记录表创建失败|time:'.date('Y-m-d H:i:s')."|原因:".$e->getMessage()."<br/>";
        };
        if($error) {
            try {
                \app\admin\lib\Mail::to(['1014555105@qq.com'])->from('xuyong782043356 <xuyong782043356@163.com>')->title('FBI WARNING')->content($error)->send();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        echo $result;

    }

    /**
     *牌局记录表
     */
    protected function gamblingRecordSqlTemplate()
    {
        // $sql = "CREATE TABLE IF NOT EXISTS `snake_gambling_record_".date('Y_m_d',strtotime('+1 day'))."` (
        //   `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
        //   `gambling_id` int(11) NOT NULL COMMENT '牌局id',
        //   `gambling_time` int(11) NOT NULL COMMENT '牌局时间',
        //   `game` varchar(255) NOT NULL COMMENT '游戏',
        //   `room` varchar(255) NOT NULL COMMENT '房间',
        //   `desk` varchar(255) DEFAULT NULL COMMENT '桌子',
        //   `param1` varchar(255) NOT NULL COMMENT '牌局参数1',
        //   `param2` varchar(255) NOT NULL COMMENT '牌局参数2',
        //   `param3` varchar(255) NOT NULL COMMENT '牌局参数3',
        //   `user_array` varchar(255) NOT NULL COMMENT '玩家信息数组',
        //   PRIMARY KEY (`id`)
        // ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='牌局记录表'";

        $sql = "CREATE TABLE IF NOT EXISTS `snake_gambling_record_".date('Y_m_d',strtotime('+1 day'))."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
          `gambling_id` int(11) NOT NULL,
          `game` varchar(255) NOT NULL COMMENT '游戏',
          `room` varchar(255) NOT NULL COMMENT '房间',
          `desk` varchar(255) NOT NULL COMMENT '桌子',
          `param1` varchar(255) NOT NULL COMMENT '牌局参数1',
          `param2` varchar(255) NOT NULL COMMENT '牌局参数2',
          `param3` varchar(255) NOT NULL COMMENT '牌局参数3',
          `user_array` varchar(255) NOT NULL COMMENT '玩家信息数组',
          `time` int(11) NOT NULL COMMENT '时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='牌局记录表'";

        return $sql;
    }

    /**
     *游戏记录表
     */
    protected function gameRecordSqlTemplate()
    {
        // $sql = "CREATE TABLE IF NOT EXISTS `snake_game_record_".date('Y_m_d',strtotime('+1 day'))."` (
        //   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        //   `login_record_id` int(10) unsigned NOT NULL,
        //   `game_id` int(10) unsigned NOT NULL COMMENT '游戏id',
        //   `room_id` int(10) unsigned NOT NULL COMMENT '房间id',
        //   `desk_id` int(10) unsigned NOT NULL COMMENT '桌子id',
        //   `gambling_id` int(10) unsigned NOT NULL COMMENT '牌局id',
        //   `gold_coin_before` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏前金币',
        //   `gold_coin_after` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏后金币',
        //   `achievement` int(10) unsigned NOT NULL COMMENT '游戏成绩',
        //   `param1` varchar(255) NOT NULL DEFAULT '' COMMENT '牌局参数1',
        //   `param2` varchar(255) NOT NULL DEFAULT '' COMMENT '牌局参数2',
        //   `param3` varchar(255) NOT NULL DEFAULT '' COMMENT '牌局参数3',
        //   `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
        //   PRIMARY KEY (`id`)
        // ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏记录表'";

        $sql = "CREATE TABLE IF NOT EXISTS `snake_game_record_".date('Y_m_d',strtotime('+1 day'))."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `game_id` int(10) unsigned NOT NULL COMMENT '游戏id',
          `room_id` int(10) unsigned NOT NULL COMMENT '房间id',
          `desk_id` int(10) unsigned NOT NULL COMMENT '桌子id',
          `gambling_id` int(10) unsigned NOT NULL COMMENT '牌局id',
          `gold_coin_before` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏前金币',
          `gold_coin_after` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏后金币',
          `achievement` int(10) unsigned NOT NULL COMMENT '游戏成绩',
          `uuid` varchar(68) NOT NULL DEFAULT '' COMMENT '机器码',
          `ip` int(10) unsigned NOT NULL COMMENT '登录ip',
          `param1` varchar(255) NOT NULL DEFAULT '' COMMENT '牌局参数1',
          `param2` varchar(255) NOT NULL DEFAULT '' COMMENT '牌局参数2',
          `param3` varchar(255) NOT NULL DEFAULT '' COMMENT '牌局参数3',
          `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏记录表'";

        return $sql;
    }

    /**
     *登录记录表
     */
    protected function loginRecordSqlTemplate()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `snake_login_record_".date('Y_m_d',strtotime('+1 day'))."` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned NOT NULL,
          `account` varchar(20) NOT NULL DEFAULT '' COMMENT '账号',
          `nickname` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
          `uuid` varchar(68) NOT NULL DEFAULT '' COMMENT '机器码',
          `ip` int(10) unsigned NOT NULL COMMENT '登录ip',
          `account_type` tinyint(3) unsigned NOT NULL COMMENT '账号类型',
          `vip` tinyint(3) unsigned NOT NULL COMMENT '是否vip',
          `level` tinyint(3) unsigned NOT NULL COMMENT '等级',
          `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='登录记录表'";

        return $sql;
    }
}
