# Host: localhost  (Version 5.5.53)
# Date: 2018-07-13 15:01:48
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "auto_model"
#

CREATE TABLE `auto_model` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '型号id',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '型号名称',
  `first_letter` varchar(2) NOT NULL DEFAULT '' COMMENT '名称首字母',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='车辆型号表';
