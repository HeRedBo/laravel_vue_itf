

### 给学生卡券表增加备注字段
ALTER TABLE `student_card` ADD COLUMN `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注信息' AFTER `status`;
ALTER TABLE `admin_student_card_data_operate_logger` ADD COLUMN `card_name` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '卡券名称' AFTER `student_card_id`;

