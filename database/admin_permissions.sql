/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50635
Source Host           : localhost:3306
Source Database       : laravel_itf

Target Server Type    : MYSQL
Target Server Version : 50635
File Encoding         : 65001

Date: 2018-03-15 18:12:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限名称',
  `display_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '显示名称',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色描述',
  `level` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '层级',
  `icon` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'icon 图标',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `order_num` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES ('1', 'admin.permission', '权限设置', '', '0', 'fa-users', '0', '1', '1', '2017-10-15 11:05:00', '2017-10-22 11:07:26');
INSERT INTO `admin_permissions` VALUES ('2', 'admin.permission.index', '权限管理', '', '0', '', '1', '1', '1', '2017-10-15 11:06:54', '2017-10-22 11:07:47');
INSERT INTO `admin_permissions` VALUES ('3', 'admin.role.index', '角色管理', '', '0', '', '1', '1', '4', '2017-10-15 13:22:14', '2017-10-22 11:08:06');
INSERT INTO `admin_permissions` VALUES ('4', 'admin.user.index', '用户管理', '', '0', '', '1', '1', '3', '2017-10-15 13:23:28', '2017-10-22 09:03:47');
INSERT INTO `admin_permissions` VALUES ('5', 'admin.dashboard', '控制面板', '', '0', 'fa-dashboard', '0', '1', '0', '2017-10-20 22:38:22', '2017-10-22 09:01:13');
INSERT INTO `admin_permissions` VALUES ('7', 'admin.permission.create', '权限添加', '', '0', '', '2', '0', '0', '2017-10-22 10:38:29', '2017-10-22 10:38:29');
INSERT INTO `admin_permissions` VALUES ('8', 'admin.permission.update', '权限编辑', '', '0', '', '2', '0', '0', '2017-10-22 10:40:05', '2017-10-22 10:40:05');
INSERT INTO `admin_permissions` VALUES ('9', 'admin.permission.destroy', '权限删除', '', '0', '', '2', '0', '0', '2017-10-22 10:41:13', '2017-10-22 10:41:13');
INSERT INTO `admin_permissions` VALUES ('10', 'admin.permission.edit', '权限信息获取', '', '0', '', '2', '0', '0', '2017-10-22 10:41:53', '2017-10-22 10:41:53');
INSERT INTO `admin_permissions` VALUES ('11', 'admin.user.create', '用户新增', '', '0', '', '4', '0', '0', '2017-10-22 10:43:03', '2017-10-22 10:43:03');
INSERT INTO `admin_permissions` VALUES ('12', 'admin.permission.show', '用户信息查看', '', '0', '', '4', '0', '0', '2017-10-22 10:44:13', '2017-10-22 10:44:13');
INSERT INTO `admin_permissions` VALUES ('13', 'admin.user.update', '用户编辑', '', '0', '', '4', '0', '0', '2017-10-22 10:49:54', '2017-10-22 10:49:54');
INSERT INTO `admin_permissions` VALUES ('14', 'admin.user.destroy', '用户信息删除', '', '0', '', '4', '0', '0', '2017-10-22 10:50:44', '2017-10-22 10:50:44');
INSERT INTO `admin_permissions` VALUES ('15', 'admin.role.create', '角色新增', '', '0', '', '3', '0', '0', '2017-10-22 10:52:49', '2017-10-22 10:52:49');
INSERT INTO `admin_permissions` VALUES ('16', 'admin.role.update', '角色编辑', '', '0', '', '3', '0', '0', '2017-10-22 10:54:27', '2017-10-22 10:54:27');
INSERT INTO `admin_permissions` VALUES ('17', 'admin.role.destroy', '角色删除', '', '0', '', '3', '0', '0', '2017-10-22 10:55:12', '2017-10-22 10:55:12');
INSERT INTO `admin_permissions` VALUES ('18', 'admin.role.setAcl', '角色权限设置', '', '0', '', '3', '0', '0', '2017-10-22 10:56:22', '2017-10-22 10:56:22');
INSERT INTO `admin_permissions` VALUES ('19', 'getAcl', '角色权限获取', '', '0', '', '3', '0', '0', '2017-10-22 10:56:54', '2017-10-22 10:57:31');
INSERT INTO `admin_permissions` VALUES ('20', 'admin.class', '道馆管理', '', '0', 'fas fa-th-large', '0', '1', '0', '2018-03-11 17:04:11', '2018-03-11 17:12:59');
INSERT INTO `admin_permissions` VALUES ('21', 'admin.student.index', '学生管理', '', '0', '', '20', '1', '1', '2018-03-11 17:07:08', '2018-03-15 14:09:58');
INSERT INTO `admin_permissions` VALUES ('22', 'admin.class.index', '班级管理', '', '0', 'fa-users', '20', '1', '2', '2018-03-11 17:13:41', '2018-03-11 17:13:41');
INSERT INTO `admin_permissions` VALUES ('23', 'admin.card.index', '卡券管理', '', '0', '', '20', '1', '3', '2018-03-11 17:15:29', '2018-03-11 17:15:45');
INSERT INTO `admin_permissions` VALUES ('24', 'admin.venueSchedule.index', '课程表管理', '', '0', 'fa-calendar', '20', '1', '4', '2018-03-11 17:17:11', '2018-03-12 01:23:06');
INSERT INTO `admin_permissions` VALUES ('25', 'admin.venueSchedule.schedule', '道馆课程表', '', '0', 'fa-calendar', '20', '1', '4', '2018-03-11 19:51:02', '2018-03-11 19:51:02');
INSERT INTO `admin_permissions` VALUES ('26', 'admin.user.logger', '操作日志', '', '0', '', '1', '1', '4', '2018-03-12 01:03:26', '2018-03-12 01:03:55');
INSERT INTO `admin_permissions` VALUES ('27', 'admin.venue', '基础配置', '', '0', 'fa-gears', '0', '1', '0', '2018-03-12 01:26:27', '2018-03-12 01:26:27');
INSERT INTO `admin_permissions` VALUES ('28', 'admin.venue.index', '道馆管理', '', '0', 'fa-gears', '27', '1', '2', '2018-03-12 01:27:20', '2018-03-12 01:27:20');
INSERT INTO `admin_permissions` VALUES ('29', 'admin.student.store', '学生新增', '', '0', '', '21', '0', '1', '2018-03-15 14:10:24', '2018-03-15 14:10:24');
INSERT INTO `admin_permissions` VALUES ('30', 'admin.student.update', '编辑学生信息', '', '0', '', '21', '0', '2', '2018-03-15 14:12:56', '2018-03-15 14:51:39');
INSERT INTO `admin_permissions` VALUES ('31', 'admin.student.getStudentInfo', '获取学生基本信息', '', '0', '', '21', '0', '1', '2018-03-15 14:51:22', '2018-03-15 14:51:22');
INSERT INTO `admin_permissions` VALUES ('32', 'admin.student.relationOptions', '获取人物关系下拉框', '', '0', '', '21', '1', '3', '2018-03-15 14:52:06', '2018-03-15 14:52:06');
INSERT INTO `admin_permissions` VALUES ('33', 'admin.student.sexOptions', '获取学生性别下拉框', '', '0', '', '21', '0', '4', '2018-03-15 14:52:35', '2018-03-15 14:52:35');
INSERT INTO `admin_permissions` VALUES ('34', 'admin.student.statusOptions', '获取学生状态下拉框', '', '0', '', '21', '0', '5', '2018-03-15 14:52:57', '2018-03-15 14:52:57');
INSERT INTO `admin_permissions` VALUES ('35', 'admin.student.studentCardList', '学生列表', '', '0', '', '21', '0', '0', '2018-03-15 14:53:24', '2018-03-15 14:53:41');
INSERT INTO `admin_permissions` VALUES ('36', 'admin.student.saveStudentCard', '保存学生卡券', '', '0', '', '21', '0', '6', '2018-03-15 14:54:10', '2018-03-15 14:54:10');
INSERT INTO `admin_permissions` VALUES ('37', 'admin.student.sign', '学生签到', '', '0', '', '21', '0', '6', '2018-03-15 14:54:30', '2018-03-15 14:55:18');
INSERT INTO `admin_permissions` VALUES ('38', 'admin.student.getSignCalendar', '学生签到日历', '', '0', '', '21', '0', '7', '2018-03-15 14:55:42', '2018-03-15 14:55:42');
INSERT INTO `admin_permissions` VALUES ('39', 'admin.student.signClassOptions', '签到班级下拉框', '', '0', '', '21', '0', '8', '2018-03-15 14:56:08', '2018-03-15 14:56:08');
INSERT INTO `admin_permissions` VALUES ('40', 'admin.student.changeStudentCardStatus', '修改学生卡券状态', '', '0', '', '21', '0', '9', '2018-03-15 14:56:30', '2018-03-15 14:56:30');
INSERT INTO `admin_permissions` VALUES ('41', 'admin.student.cardLogger', '学生卡券日志', '', '0', '', '21', '0', '9', '2018-03-15 14:56:50', '2018-03-15 14:56:50');
INSERT INTO `admin_permissions` VALUES ('42', 'admin.venueBill.createDataType', '道馆账单数据类型创建', '', '0', '', '21', '0', '12', '2018-03-15 14:57:25', '2018-03-15 14:57:32');
INSERT INTO `admin_permissions` VALUES ('43', 'admin.venueSchedules.index', '课程表列表', '', '0', '', '24', '0', '1', '2018-03-15 14:59:01', '2018-03-15 14:59:01');
INSERT INTO `admin_permissions` VALUES ('44', 'admin.venueSchedules.store', '创建课程表', '', '0', '', '24', '0', '2', '2018-03-15 14:59:58', '2018-03-15 14:59:58');
INSERT INTO `admin_permissions` VALUES ('45', 'admin.venueSchedules.edit', '获取课程表信息', '', '0', '', '24', '0', '3', '2018-03-15 15:00:23', '2018-03-15 15:00:23');
INSERT INTO `admin_permissions` VALUES ('46', 'admin.venueSchedules.update', '编辑课程表', '', '0', '', '24', '0', '4', '2018-03-15 15:00:48', '2018-03-15 15:00:48');
INSERT INTO `admin_permissions` VALUES ('47', 'admin.class.store', '创建班级', '', '0', '', '22', '0', '0', '2018-03-15 15:06:35', '2018-03-15 15:06:35');
INSERT INTO `admin_permissions` VALUES ('48', 'admin.class.edit', '获取班级信息', '', '0', '', '22', '0', '0', '2018-03-15 15:07:02', '2018-03-15 15:07:02');
INSERT INTO `admin_permissions` VALUES ('49', 'admin.class.update', '编辑班级', '', '0', '', '22', '0', '0', '2018-03-15 15:07:23', '2018-03-15 15:07:23');
INSERT INTO `admin_permissions` VALUES ('50', 'admin.class.checkClassName', '校验班级名称是否可用', '', '0', '', '22', '0', '3', '2018-03-15 15:07:53', '2018-03-15 15:07:53');
INSERT INTO `admin_permissions` VALUES ('51', 'admin.class.classOptions', '获取道馆班级下拉框', '', '0', '', '22', '0', '4', '2018-03-15 15:08:12', '2018-03-15 15:08:12');
INSERT INTO `admin_permissions` VALUES ('52', 'admin.card.checkCardName', '校验卡券名称是否可用', '', '0', '', '23', '0', '1', '2018-03-15 15:17:19', '2018-03-15 15:17:19');
INSERT INTO `admin_permissions` VALUES ('53', 'admin.card.cardOptions', '道馆卡券下拉框', '', '0', '', '23', '0', '2', '2018-03-15 15:17:41', '2018-03-15 15:17:41');
INSERT INTO `admin_permissions` VALUES ('54', 'admin.card.cardTypeOptions', '获取卡券类型下拉框', '', '0', '', '23', '0', '3', '2018-03-15 15:18:04', '2018-03-15 15:18:04');
INSERT INTO `admin_permissions` VALUES ('55', 'admin.card.changeStatus', '修改卡券状态', '', '0', '', '23', '0', '4', '2018-03-15 15:18:33', '2018-03-15 15:18:33');
INSERT INTO `admin_permissions` VALUES ('56', 'admin.card.cardLogger', '获取卡券操作日志', '', '0', '', '23', '0', '5', '2018-03-15 15:19:08', '2018-03-15 15:19:08');
INSERT INTO `admin_permissions` VALUES ('57', 'admin.card.studentCardOptions', '获取学生卡券下拉框', '', '0', '', '23', '0', '6', '2018-03-15 15:20:25', '2018-03-15 15:20:25');
INSERT INTO `admin_permissions` VALUES ('58', 'admin.card.store', '创建卡券', '', '0', '', '23', '0', '6', '2018-03-15 15:21:07', '2018-03-15 15:21:07');
INSERT INTO `admin_permissions` VALUES ('59', 'admin.card.edit', '获取卡券信息', '', '0', '', '23', '0', '1', '2018-03-15 15:21:31', '2018-03-15 15:21:31');
INSERT INTO `admin_permissions` VALUES ('60', 'admin.card.update', '编辑卡券', '', '0', '', '23', '0', '0', '2018-03-15 15:22:20', '2018-03-15 15:22:20');
