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

CREATE TABLE rf_oil_order (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `status` int(3) UNSIGNED DEFAULT '0' COMMENT '状态',
  `from` int(3) UNSIGNED DEFAULT '0' COMMENT '渠道',
    `orderId` varchar(60) DEFAULT '' COMMENT '订单号',
    `paySn` varchar(50) DEFAULT '' COMMENT '支付号',
    `phone` varchar(20) DEFAULT '' COMMENT '手机号',
    `orderTime` varchar(30) DEFAULT '' COMMENT '生成时间',
    `payTime` varchar(30) DEFAULT '' COMMENT '支付时间',
    `refundTime` varchar(30) DEFAULT '' COMMENT '退款时间',
    `gasName` varchar(150) DEFAULT '' COMMENT '油站名称',
    `province` varchar(15) DEFAULT '' COMMENT '省名称',
    `city` varchar(15) DEFAULT '' COMMENT '市名称',
    `county` varchar(15) DEFAULT '' COMMENT '县名称',
    `gunNo` int(10) UNSIGNED DEFAULT '0' COMMENT '枪号',
    `oilNo` varchar(15) DEFAULT '' COMMENT '油号',
    `amountPay` decimal(6,2) DEFAULT '0' COMMENT '实付',
    `amountGun` decimal(6,2) DEFAULT '0' COMMENT '总金额',
    `amountDiscounts` decimal(6,2) DEFAULT '0' COMMENT '优惠金额',
    `orderStatusName` varchar(15) DEFAULT '' COMMENT '订单状态',
    `couponMoney` decimal(6,2) DEFAULT '0' COMMENT '优惠券金额',
    `couponId` int(10) UNSIGNED DEFAULT '0' COMMENT '优惠券编号',
    `couponCode` varchar(30) DEFAULT '' COMMENT '优惠券Code',
    `litre` varchar(20) DEFAULT '' COMMENT '升数',
    `payType` varchar(10) DEFAULT '' COMMENT '支付方式',
    `priceUnit` varchar(15) DEFAULT '' COMMENT '最终单价',
    `priceOfficial` varchar(15) DEFAULT '' COMMENT '国标价',
    `priceGun` varchar(15) DEFAULT '' COMMENT '枪价',
    `orderSource` varchar(50) DEFAULT '' COMMENT '渠道编码',
    `qrCode4PetroChina` varchar(50) DEFAULT '' COMMENT '渠道编码',
    `gasId` varchar(50) DEFAULT '' COMMENT '油站ID',
    `created_at` int(11) UNSIGNED DEFAULT '0' COMMENT '同步时间',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='加油订单';

CREATE TABLE rf_oil_card (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `status` int(3) UNSIGNED DEFAULT '0' COMMENT '状态',
  `type` int(3) UNSIGNED DEFAULT '0' COMMENT '类型',
  `member_id` int(11) UNSIGNED DEFAULT '0' COMMENT '持有人',
  `user` int(11) UNSIGNED DEFAULT '0' COMMENT '使用者',
  `cardNo` int(11) UNSIGNED DEFAULT '0' COMMENT '卡号',
  `code` varchar(10) DEFAULT '' COMMENT '密码',
  `created_at` int(11) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `end_at` int(11) UNSIGNED DEFAULT '0' COMMENT '使用时间',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='卡片';

