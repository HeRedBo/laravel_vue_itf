

### 给学生卡券表增加备注字段
ALTER TABLE `student_card` ADD COLUMN `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注信息' AFTER `status`;
ALTER TABLE `admin_student_card_data_operate_logger` ADD COLUMN `card_name` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '卡券名称' AFTER `student_card_id`;

### rename

### 修改表名
ALTER TABLE  `admin_venues` RENAME TO `venues`;
ALTER TABLE  `students` RENAME TO `admin_students`;
ALTER TABLE  `student_number_card` RENAME TO `admin_student_number_card`;
ALTER TABLE  `student_contacts` RENAME TO `admin_student_contacts`;
ALTER TABLE  `student_card` RENAME TO `admin_student_card`;
ALTER TABLE  `student_class` RENAME TO `admin_student_class`;


ALTER TABLE  `card_snap` RENAME TO `admin_card_snap`;
ALTER TABLE  `cards` RENAME TO `admin_cards`;
ALTER TABLE  `classes` RENAME TO `admin_classes`;
ALTER TABLE  `contacts` RENAME TO `admin_contacts`;


ALTER TABLE  `relation_name` RENAME TO `admin_relation_name`;
ALTER TABLE  `role_permission` RENAME TO `admin_role_permission`;

ALTER TABLE  `venue_bill` RENAME TO `admin_venue_bill`;
ALTER TABLE  `venue_bill_data_type` RENAME TO `admin_venue_bill_data_type`;
ALTER TABLE  `venue_bill_log` RENAME TO `admin_venue_bill_log`;


