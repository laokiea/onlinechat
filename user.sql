/*
Navicat MySQL Data Transfer

Source Server         : laokiea
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : data

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-10-06 11:10:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uName` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
  `uPass` varchar(40) NOT NULL COMMENT '密码',
  `isLogin` tinyint(8) NOT NULL DEFAULT 0 COMMENT '登录状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
