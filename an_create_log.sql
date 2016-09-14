/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : analytics

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2016-08-30 10:36:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `an_create_log`
-- ----------------------------
DROP TABLE IF EXISTS `an_create_log`;
CREATE TABLE `an_create_log` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) DEFAULT NULL,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `due_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`,`created_time`,`due_time`),
  UNIQUE KEY `time` (`created_time`,`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (year(created_time))
(PARTITION p1 VALUES LESS THAN (2016) ENGINE = MyISAM,
 PARTITION p2 VALUES LESS THAN (2017) ENGINE = MyISAM,
 PARTITION p3 VALUES LESS THAN (2018) ENGINE = MyISAM,
 PARTITION p4 VALUES LESS THAN (2019) ENGINE = MyISAM,
 PARTITION p5 VALUES LESS THAN (2020) ENGINE = MyISAM,
 PARTITION p6 VALUES LESS THAN MAXVALUE ENGINE = MyISAM) */;

-- ----------------------------
-- Records of an_create_log
-- ----------------------------

-- ----------------------------
-- Table structure for `an_due_log`
-- ----------------------------
DROP TABLE IF EXISTS `an_due_log`;
CREATE TABLE `an_due_log` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) DEFAULT NULL,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`,`created_time`),
  UNIQUE KEY `time` (`created_time`,`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (year(created_time))
(PARTITION p1 VALUES LESS THAN (2016) ENGINE = MyISAM,
 PARTITION p2 VALUES LESS THAN (2017) ENGINE = MyISAM,
 PARTITION p3 VALUES LESS THAN (2018) ENGINE = MyISAM,
 PARTITION p4 VALUES LESS THAN (2019) ENGINE = MyISAM,
 PARTITION p5 VALUES LESS THAN (2020) ENGINE = MyISAM,
 PARTITION p6 VALUES LESS THAN MAXVALUE ENGINE = MyISAM) */;

-- ----------------------------
-- Records of an_due_log
-- ----------------------------
INSERT INTO `an_due_log` VALUES ('58', '56', '2016-07-11 00:00:00');
INSERT INTO `an_due_log` VALUES ('59', '56', '2016-07-23 00:00:00');
INSERT INTO `an_due_log` VALUES ('60', '57', '2016-07-28 00:00:00');
INSERT INTO `an_due_log` VALUES ('61', '58', '2016-07-18 00:00:00');
INSERT INTO `an_due_log` VALUES ('62', '59', '2016-08-28 00:00:00');
INSERT INTO `an_due_log` VALUES ('63', '1', '2016-07-11 00:00:00');

-- ----------------------------
-- Table structure for `an_extension_info`
-- ----------------------------
DROP TABLE IF EXISTS `an_extension_info`;
CREATE TABLE `an_extension_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ext_id` varchar(120) DEFAULT NULL COMMENT '推广商id,关联web_info表中的extension_id',
  `extension_name` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ext_id` (`ext_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='推广商信息表';

-- ----------------------------
-- Records of an_extension_info
-- ----------------------------

-- ----------------------------
-- Table structure for `an_fee_info`
-- ----------------------------
DROP TABLE IF EXISTS `an_fee_info`;
CREATE TABLE `an_fee_info` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(255) DEFAULT NULL,
  `t-mobile` varchar(255) DEFAULT NULL COMMENT '运营商',
  `price` decimal(10,5) unsigned DEFAULT NULL COMMENT '这种国家所收的单价',
  `rate` float(10,5) unsigned DEFAULT NULL COMMENT '这种运营商打的折扣',
  `get_fee` decimal(10,5) DEFAULT NULL COMMENT 'gadmobe得到的费用,用总人数乘以rate乘以单价即可',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='运营商扣费表';

-- ----------------------------
-- Records of an_fee_info
-- ----------------------------
INSERT INTO `an_fee_info` VALUES ('1', 'uk', 'Vodafone', '4.50000', '73.60000', '2.76000');
INSERT INTO `an_fee_info` VALUES ('2', 'uk', 'O2', '4.50000', '69.60000', '2.61000');
INSERT INTO `an_fee_info` VALUES ('3', 'uk', 'Orange', '4.50000', '67.73000', '2.54000');
INSERT INTO `an_fee_info` VALUES ('4', 'uk', 'T-mobile', '4.50000', '67.73000', '2.54000');
INSERT INTO `an_fee_info` VALUES ('5', 'uk', 'Three', '4.50000', '70.93000', '2.66000');
INSERT INTO `an_fee_info` VALUES ('6', 'uk', 'Virgin', '4.50000', '35.20000', '1.32000');

-- ----------------------------
-- Table structure for `an_member`
-- ----------------------------
DROP TABLE IF EXISTS `an_member`;
CREATE TABLE `an_member` (
  `id` int(80) NOT NULL AUTO_INCREMENT,
  `user_id` int(80) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `tracking_code` int(80) DEFAULT NULL,
  `phone` int(80) DEFAULT NULL,
  `extension_id` varchar(80) DEFAULT NULL,
  `click_id` varchar(80) DEFAULT NULL,
  `country` varchar(80) DEFAULT NULL,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`,`created_time`),
  UNIQUE KEY `a` (`user_id`,`created_time`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (year(created_time))
(PARTITION p0 VALUES LESS THAN (2016) ENGINE = MyISAM,
 PARTITION p1 VALUES LESS THAN (2017) ENGINE = MyISAM,
 PARTITION p2 VALUES LESS THAN (2018) ENGINE = MyISAM,
 PARTITION p3 VALUES LESS THAN (2019) ENGINE = MyISAM,
 PARTITION p4 VALUES LESS THAN (2020) ENGINE = MyISAM,
 PARTITION p5 VALUES LESS THAN MAXVALUE ENGINE = MyISAM) */;

-- ----------------------------
-- Records of an_member
-- ----------------------------
INSERT INTO `an_member` VALUES ('27', '55', '3426431@qq.com', '3426431', '2147483647', '9999', null, 'uk', '2016-05-17 22:47:36');
INSERT INTO `an_member` VALUES ('28', '56', '3436996@qq.com', '3436996', '2147483647', '9999', null, 'uk', '2016-05-17 22:57:12');
INSERT INTO `an_member` VALUES ('29', '57', '2453036296@qq.com', '22187462', '2147483647', '9999', null, 'uk', '2016-05-17 23:17:43');
INSERT INTO `an_member` VALUES ('30', '58', '4553201@qq.com', '4553201', '2147483647', '0006_casino', null, 'uk', '2016-06-12 20:06:57');
INSERT INTO `an_member` VALUES ('31', '59', 'mobiles.mailtext@gmail.com', '4925443', '2147483647', '0006_puzzle', null, 'uk', '2016-06-14 17:14:02');
INSERT INTO `an_member` VALUES ('32', '70', '15268@qq.com', '1078045', '0', '9999', null, 'uk', '2016-04-08 00:00:00');

-- ----------------------------
-- Table structure for `an_renew_log`
-- ----------------------------
DROP TABLE IF EXISTS `an_renew_log`;
CREATE TABLE `an_renew_log` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `due_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `web_id` tinyint not NULL,
  PRIMARY KEY (`id`,`created_time`,`due_time`),
  UNIQUE KEY `time` (`created_time`,`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (year(created_time))
(PARTITION p1 VALUES LESS THAN (2016) ENGINE = MyISAM,
 PARTITION p2 VALUES LESS THAN (2017) ENGINE = MyISAM,
 PARTITION p3 VALUES LESS THAN (2018) ENGINE = MyISAM,
 PARTITION p4 VALUES LESS THAN (2019) ENGINE = MyISAM,
 PARTITION p5 VALUES LESS THAN (2020) ENGINE = MyISAM,
 PARTITION p6 VALUES LESS THAN MAXVALUE ENGINE = MyISAM) */;

-- ----------------------------
-- Records of an_renew_log
-- ----------------------------
INSERT INTO `an_renew_log` VALUES ('64', '55', '2016-07-18 00:00:00', '2016-07-25 00:00:00');
INSERT INTO `an_renew_log` VALUES ('65', '57', '2016-07-20 00:00:00', '2016-07-27 00:00:00');
INSERT INTO `an_renew_log` VALUES ('66', '58', '2016-07-28 00:00:00', '2016-08-04 00:00:00');
INSERT INTO `an_renew_log` VALUES ('67', '55', '2016-08-10 00:00:00', '2016-08-17 00:00:00');
INSERT INTO `an_renew_log` VALUES ('68', '59', '2016-08-18 00:00:00', '2016-08-25 00:00:00');
INSERT INTO `an_renew_log` VALUES ('69', '70', '2016-04-15 00:00:00', '1970-01-01 08:00:00');
INSERT INTO `an_renew_log` VALUES ('70', '70', '2016-04-09 00:00:00', '1970-01-01 08:00:00');
INSERT INTO `an_renew_log` VALUES ('71', '70', '2016-08-09 00:00:00', '1970-01-01 08:00:00');
INSERT INTO `an_renew_log` VALUES ('72', '55', '2016-05-25 00:00:00', '1970-01-01 08:00:00');

-- ----------------------------
-- Table structure for `an_users`
-- ----------------------------
DROP TABLE IF EXISTS `an_users`;
CREATE TABLE `an_users` (
  `userid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` char(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `level` int(11) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of an_users
-- ----------------------------
INSERT INTO `an_users` VALUES ('1', 'admin', '10', '9ad1049472de186ecda9d84cdea617e090ce092f');

-- ----------------------------
-- Table structure for `an_web_info`
-- ----------------------------
DROP TABLE IF EXISTS `an_web_info`;
CREATE TABLE `an_web_info` (
  `id` smallint(10) unsigned NOT NULL AUTO_INCREMENT,
  `web_name` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `web_name` (`web_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='网站基本信息表';

-- ----------------------------
-- Records of an_web_info
-- ----------------------------

-- ----------------------------
-- Table structure for `change_info`
-- ----------------------------
DROP TABLE IF EXISTS `change_info`;
CREATE TABLE `change_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `export_id` int(11) NOT NULL COMMENT '关联export表',
  `first` tinyint(4) DEFAULT NULL COMMENT '第一个月的变化数据',
  `second` tinyint(4) DEFAULT NULL,
  `third` tinyint(4) DEFAULT NULL,
  `fourth` tinyint(4) DEFAULT NULL,
  `fifth` tinyint(4) DEFAULT NULL,
  `sixth` tinyint(4) DEFAULT NULL,
  `seventh` tinyint(4) DEFAULT NULL,
  `eighth` tinyint(4) DEFAULT NULL,
  `ninth` tinyint(4) DEFAULT NULL,
  `tenth` tinyint(4) DEFAULT NULL,
  `eleventh·` tinyint(4) DEFAULT NULL,
  `twelvth` tinyint(4) DEFAULT NULL,
  `thirteenth` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='每个月数据一年的变化情况表';

-- ----------------------------
-- Records of change_info
-- ----------------------------

-- ----------------------------
-- Table structure for `export_info`
-- ----------------------------
DROP TABLE IF EXISTS `export_info`;
CREATE TABLE `export_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `web_id` smallint(5) unsigned NOT NULL,
  `export_time` int(10) unsigned DEFAULT '0' COMMENT '导出时间',
  `is_exported` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有导出过，默认没有',
  `year` tinyint(4) DEFAULT NULL COMMENT '用户支付成功的年份',
  `month` tinyint(4) DEFAULT NULL COMMENT '用户支付成功的月份',
  `people` int(11) DEFAULT NULL COMMENT '每个web_id在对应的年月份的初始支付人数',
  `country_id` int(10) DEFAULT NULL COMMENT '国家id，关联fee_info表',
  PRIMARY KEY (`id`),
  KEY `web_id` (`web_id`),
  KEY `export_time` (`export_time`),
  KEY `is_exported` (`is_exported`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='导出数据生成对应报表的表';

-- ----------------------------
-- Records of export_info
-- ----------------------------
