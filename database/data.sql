

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

insert into `admin` ( `username`, `name`, `picture`, `email`, `phone`, `password`, `remember_token`, `created_at`, `updated_at`) values ( 'admin', '超级管理员', 'files/avatar/201711041223371509769417.317824.jpeg', 'qqucx@163.com', '13925185624', '$2y$10$GY5sl8Oe2Oa0iYSOVI7wJeUV/gp2TWeQ5O1ZIqb1MyDgknosqH.Qe', 'IQFfrhITxxdjv5zZSJSEJo3kQkH3FGChxHvCDIzQRWqLGhGwqrMp97fWnzkd', '2017-02-20 07:46:45', '2017-11-05 12:47:32');


