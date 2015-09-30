/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : 127.0.0.1:3306
Source Database       : persimon_github

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-09-30 17:28:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for persimmon_attribute
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_attribute`;
CREATE TABLE `persimmon_attribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for persimmon_config
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_config`;
CREATE TABLE `persimmon_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '配置名',
  `value` varchar(255) DEFAULT NULL COMMENT '值',
  `title` varchar(255) DEFAULT NULL,
  `tips` varchar(255) DEFAULT NULL,
  `type` int(5) unsigned DEFAULT NULL COMMENT '字段类型:0:数字1:字符2:文本3:数组4:枚举',
  `config` varchar(255) DEFAULT NULL,
  `group` int(5) DEFAULT NULL,
  `sort` int(5) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '0',
  `create_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_config
-- ----------------------------
INSERT INTO `persimmon_config` VALUES ('1', 'WEB_NAME', 'Mr.Cong', '网站名称', '网站名称', '2', '', '1', '1', '1', '1441639782');
INSERT INTO `persimmon_config` VALUES ('2', 'KEYWORDS', 'Mr.柿子,Mr.Cong,Cong5,PHP', '网站关键词', '网站关键词', '3', '', '1', '2', '0', '1441680686');
INSERT INTO `persimmon_config` VALUES ('3', 'DESCRIPTION', 'Mr.Cong的一些涂鸦!', '网站描述', '网站描述', '3', null, '1', '3', '0', '1441635309');
INSERT INTO `persimmon_config` VALUES ('4', 'SHOW_PAGE_TRACE', '1', '是否开启Trace', '是否开启网站的Trace模式', '5', '0:关闭\n1:开启', '1', '4', '0', '1441635988');

-- ----------------------------
-- Table structure for persimmon_favorites
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_favorites`;
CREATE TABLE `persimmon_favorites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL COMMENT '用户ID',
  `title` text COMMENT '标题',
  `source` varchar(255) DEFAULT NULL COMMENT 'URL地址',
  `tags` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `snapshot` varchar(255) DEFAULT NULL COMMENT '快照',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_favorites
-- ----------------------------

-- ----------------------------
-- Table structure for persimmon_login_logs
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_login_logs`;
CREATE TABLE `persimmon_login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL COMMENT '登陆的用户名',
  `login_ip` varchar(50) NOT NULL COMMENT '登陆IP',
  `login_time` int(11) NOT NULL COMMENT '登陆时间',
  `method` varchar(50) NOT NULL COMMENT '登陆方式',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户UA',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_login_logs
-- ----------------------------

-- ----------------------------
-- Table structure for persimmon_notes
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_notes`;
CREATE TABLE `persimmon_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL COMMENT '用户ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `tags` varchar(255) DEFAULT NULL COMMENT '标签',
  `content` text COMMENT '内容',
  `delete` tinyint(1) unsigned DEFAULT '0' COMMENT '删除状态',
  `client_ip` varchar(50) DEFAULT NULL COMMENT '客户端IP',
  `remind` tinyint(1) DEFAULT '0' COMMENT '是否提醒',
  `category` varchar(100) DEFAULT '0' COMMENT '类别:0:Inbox 1:Note 2:Knowledge',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `category` (`category`) USING BTREE,
  KEY `userid` (`userid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_notes
-- ----------------------------

-- ----------------------------
-- Table structure for persimmon_picture
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_picture`;
CREATE TABLE `persimmon_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ext` varchar(255) DEFAULT NULL COMMENT '文件后缀',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_picture
-- ----------------------------

-- ----------------------------
-- Table structure for persimmon_todo
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_todo`;
CREATE TABLE `persimmon_todo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `todo` text COMMENT 'Todo 内容',
  `begin_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态。0未开始，1进行中，2已完成',
  `remind` tinyint(1) DEFAULT '0' COMMENT '是否提醒',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_todo
-- ----------------------------

-- ----------------------------
-- Table structure for persimmon_users
-- ----------------------------
DROP TABLE IF EXISTS `persimmon_users`;
CREATE TABLE `persimmon_users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '8b37dd9a758a4c59a37e1f61655d2585' COMMENT '密码',
  `salt` varchar(6) DEFAULT NULL,
  `ticket` varchar(35) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL COMMENT '用户描述',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `wechat_openid` varchar(50) DEFAULT NULL COMMENT '微信OpenID',
  `weibo_id` int(11) unsigned DEFAULT NULL COMMENT '新浪微博ID',
  `weibo_domain` varchar(50) DEFAULT NULL COMMENT '新浪微博域名',
  `weibo_avatar` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '0 禁用，1正常，-1删除',
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of persimmon_users
-- ----------------------------
