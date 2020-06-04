## RageFrame 2.0

新增的表

CREATE TABLE rf_gas_stations (
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

CREATE TABLE rf_gas_card (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `status` int(3) UNSIGNED DEFAULT '0' COMMENT '状态',
  `type` int(3) UNSIGNED DEFAULT '0' COMMENT '类型',
  `print` int(3) UNSIGNED DEFAULT '0' COMMENT '是否打印',
  `member_id` int(11) UNSIGNED DEFAULT '0' COMMENT '持有人',
  `user` int(11) UNSIGNED DEFAULT '0' COMMENT '使用者',
  `cardNo` int(11) UNSIGNED DEFAULT '0' COMMENT '卡号',
  `code` varchar(10) DEFAULT '' COMMENT '密码',
  `img` varchar(250) DEFAULT '' COMMENT '二维码',
  `created_at` int(11) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `end_at` int(11) UNSIGNED DEFAULT '0' COMMENT '使用时间',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='油卡表';

CREATE TABLE rf_gas_order (
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

























------------------------以下为老数据------------------



### 前言
-- 团油身份
ALTER TABLE `rf_member` ADD `oil_token_time` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT 'tk有效期';
ALTER TABLE `rf_member` ADD `oil_token` varchar(60) DEFAULT '' COMMENT '团油身份';
ALTER TABLE `rf_member` ADD `province_agent` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '代理省';
ALTER TABLE `rf_member` ADD `city_agent` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '代理市';
ALTER TABLE `rf_member` ADD `area_agent` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '代理区';
ALTER TABLE `rf_member` ADD `is_agent` INT( 3 ) UNSIGNED DEFAULT '0' COMMENT '区代级别';
ALTER TABLE `rf_member` ADD `area_send` INT( 3 ) UNSIGNED DEFAULT '0' COMMENT '区代发放';
--老系统资料
ALTER TABLE `rf_member` ADD `old_card` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '老卡片数';
ALTER TABLE `rf_member` ADD `old_id` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '老系统ID';
ALTER TABLE `rf_member` ADD `agentid` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '老推荐人';
ALTER TABLE `rf_member` ADD `credit1` decimal(10,2) DEFAULT '0' COMMENT '老优惠金';
ALTER TABLE `rf_member` ADD `credit2` decimal(10,2) DEFAULT '0' COMMENT '老余额';
--分润参数
ALTER TABLE `rf_addon_shop_product` ADD `is_card` INT( 3 ) UNSIGNED DEFAULT '0' COMMENT '是否油卡';
ALTER TABLE `rf_member_level` ADD `invit` INT( 11 ) UNSIGNED DEFAULT '0' COMMENT '直推人数';
ALTER TABLE `rf_member_level` ADD `commission_shop` decimal(10,4) DEFAULT '0' COMMENT '消费分润';
ALTER TABLE `rf_member_level` ADD `commission_oil` decimal(10,4) DEFAULT '0' COMMENT '加油分润';





CREATE TABLE rf_oil_delivery (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `status` int(3) UNSIGNED DEFAULT '0' COMMENT '状态',
  `type` int(3) UNSIGNED DEFAULT '0' COMMENT '类型',
  `member_id` int(11) UNSIGNED DEFAULT '0' COMMENT '持有人',
  `cardNo` int(11) UNSIGNED DEFAULT '0' COMMENT '起始卡号',
  `cardNum` int(11) UNSIGNED DEFAULT '0' COMMENT '数量',
  `name` varchar(10) DEFAULT '' COMMENT '姓名',
  `reply` varchar(20) DEFAULT '' COMMENT '快递单号',
  `mobile` varchar(20) DEFAULT '' COMMENT '电话',
  `address` varchar(250) DEFAULT '' COMMENT '收货地址',
  `created_at` int(11) UNSIGNED DEFAULT '0' COMMENT '创建时间',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='卡片';

CREATE TABLE rf_member_tixian (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `status` int(3) UNSIGNED DEFAULT '0' COMMENT '状态',
  `type` int(3) UNSIGNED DEFAULT '0' COMMENT '平台类型',
  `member_id` int(11) UNSIGNED DEFAULT '0' COMMENT '用户',
  `money` decimal(6,2) UNSIGNED DEFAULT '0' COMMENT '金额',
  `fee` decimal(6,2) UNSIGNED DEFAULT '0' COMMENT '手续费',
  `account` varchar(250) DEFAULT '' COMMENT '收款账户',
  `account_img` varchar(250) DEFAULT '' COMMENT '收款码',
  `name` varchar(10) DEFAULT '' COMMENT '开户姓名',
  `bank_name` varchar(60) DEFAULT '' COMMENT '开户行',
  `mobile` varchar(20) DEFAULT '' COMMENT '联系电话',
  `remark` varchar(250) DEFAULT '' COMMENT '驳回原因',
  `created_at` int(11) UNSIGNED DEFAULT '0' COMMENT '创建时间',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='提现';

