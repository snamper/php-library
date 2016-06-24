DROP TABLE IF EXISTS `onethink_member_sdk`;
CREATE TABLE `onethink_member_sdk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户UID',
  `type_uid` varchar(255) NOT NULL COMMENT '授权登陆用户名 第三方分配的appid',
  `type` char(80) NOT NULL COMMENT '登陆类型 qq|sina',
  `oauth_token` varchar(150) DEFAULT NULL COMMENT '授权账号',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;