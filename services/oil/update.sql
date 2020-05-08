## RageFrame 2.0

新增的表

### 前言
CREATE TABLE rf_oil_stations (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
        `gasId` varchar(30) NOT NULL COMMENT '油站 id',
        `gasName` varchar(250) NOT NULL COMMENT '油站名称',
        `channelId` int(3) UNSIGNED DEFAULT '0' COMMENT '渠道:0 团油，1 小桔',
        `gasType` int(11) UNSIGNED DEFAULT '0' COMMENT '油站类型:1 中石油，2 中石化，3 壳牌，4 其他',
        `gasLogoBig` varchar(250) DEFAULT '' COMMENT '大 logo 图片链接',
        `gasLogoSmall` varchar(250) DEFAULT '' COMMENT '小 logo 图片链接',
        `gasAddress` varchar(250) DEFAULT '' COMMENT '加油站详情地址',
        `gasAddressLongitude` decimal(10,6) DEFAULT '0' COMMENT '油站经度',
        `gasAddressLatitude` decimal(10,6) DEFAULT '0' COMMENT '油站纬度',
        `provinceCode` int(11) UNSIGNED DEFAULT '0' COMMENT '省编码',
        `cityCode` int(11) UNSIGNED DEFAULT '0' COMMENT '市编码',
        `countyCode` int(11) UNSIGNED DEFAULT '0' COMMENT '县编码',
        `provinceName` varchar(15) DEFAULT '' COMMENT '省份',
        `cityName` varchar(15) DEFAULT '' COMMENT '城市',
        `countyName` varchar(15) DEFAULT '' COMMENT '区县',
        `isInvoice` int(3) UNSIGNED DEFAULT '0' COMMENT '是否能开发票 0 不能开 1 能开',
        `companyId` int(11) UNSIGNED DEFAULT '0' COMMENT '公司代码',
        `created_at` int(11) UNSIGNED DEFAULT '0' COMMENT '创建时间',
        `status` int(3) UNSIGNED DEFAULT '0' COMMENT '状态',
        PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='油站表';


ALTER TABLE `rf_member` ADD `oil_token_time` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT 'tk有效期';
ALTER TABLE `rf_member` ADD `oil_token` varchar(60) DEFAULT '' COMMENT '团油身份';
