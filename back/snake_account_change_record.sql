SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `snake_account_change_record`;
CREATE TABLE `snake_account_change_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_record_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL COMMENT '变更类型',
  `change_before_vars` varchar(255) NOT NULL DEFAULT '' COMMENT '变更前参数',
  `change_after_vars` varchar(255) NOT NULL DEFAULT '' COMMENT '变更后参数',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账号变更记录表';

