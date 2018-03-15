/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50718
 Source Host           : localhost
 Source Database       : lara_itf

 Target Server Type    : MySQL
 Target Server Version : 50718
 File Encoding         : utf-8

 Date: 03/16/2018 02:25:01 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录名',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '管理员头像',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '管理员邮箱',
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '管理员手机号码',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='后台管理员表';

-- ----------------------------
--  Records of `admin`
-- ----------------------------
BEGIN;
INSERT INTO `admin` VALUES ('1', 'admin', '超级管理员', 'files/avatar/201711041223371509769417.317824.jpeg', 'qqucx@163.com', '13925185624', '$2y$10$GY5sl8Oe2Oa0iYSOVI7wJeUV/gp2TWeQ5O1ZIqb1MyDgknosqH.Qe', 'IQFfrhITxxdjv5zZSJSEJo3kQkH3FGChxHvCDIzQRWqLGhGwqrMp97fWnzkd', '2017-02-20 07:46:45', '2017-11-05 12:47:32'), ('4', 'redbo', '何红波', 'files/avatar/201710200004361508429076.8794535.jpeg', '', '13925185624', '$2y$10$KMHFDyAbIiWWF1dYDa0pHeIQwKEC8drQWSLeIDQqv8kZbdSQQTye.', 'ZoDl5g9WoKftCGi5hd4D7YIy5FXkULQv3AllSg9bmQ7g0Du507mUi301NTWK', '2017-10-15 23:32:15', '2017-10-20 00:09:04'), ('7', 'libinlan', '彬兰', 'files/avatar/201801071618031515313083.1456379.jpeg', '', '13925185624', '$2y$10$lOGTZNRNMQAogSC3QNjFz.CRHXQwClzim/BWvFYucGmXrLb/b7WWm', null, '2017-10-15 23:54:52', '2018-01-07 16:18:15'), ('8', 'yin123', '尹虎林师范', 'files/avatar/201803152010461521115846.6784804.jpeg', '', '13431950479', '$2y$10$uo19p4h2ZpWeSCWwf6nWNu1tKSOchdLxwi3Wk.GNz4TEWIuMqvGge', null, '2018-03-15 20:11:00', '2018-03-15 20:11:00');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
