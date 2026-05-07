-- ----------------------------
-- 积分商城-订单表
-- ----------------------------
DROP TABLE IF EXISTS `hema_exchange_order`;
CREATE TABLE IF NOT EXISTS `hema_exchange_order` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `total_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品总价',
  `receipt_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '收货状态(10未收货 20已收货)',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态(10进行中 20取消 30已完成)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='积分商城-订单表';
-- ----------------------------
-- 积分商城-商品表
-- ----------------------------
DROP TABLE IF EXISTS `hema_exchange_goods`;
CREATE TABLE IF NOT EXISTS `hema_exchange_goods` (
  `goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品图片',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券ID',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `selling_point` varchar(255) NOT NULL DEFAULT '' COMMENT '商品卖点',
  `goods_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '商品类型(10实物 20优惠券)',
  `goods_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '换购积分',
  `stock_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前库存数量',
  `content` longtext NOT NULL COMMENT '商品详情',
  `goods_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销量数量',
  `goods_sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '商品排序(数字越小越靠前)',
  `goods_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '商品状态(10上架 20下架)',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='积分商城-商品表';

-- ----------------------------
-- 订单表
-- ----------------------------
DROP TABLE IF EXISTS `hema_order`;
CREATE TABLE IF NOT EXISTS `hema_order` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `source` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单来源(10自助点单 20代客点单)',
  `order_mode` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单类型(10堂食 20外卖)',
  `row_no` varchar(10) NOT NULL DEFAULT '' COMMENT '订单排号',
  `table_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '餐桌/包间id',
  `people` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '就餐人数',
  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '买家留言',
  `flavor` varchar(255) NOT NULL DEFAULT '' COMMENT '口味选项',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总金额',
  `ware_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '餐具茶水费',
  `activity_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠价格',
  `delivery_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '配送费用',
  `pack_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '餐盒费用',
  `divide_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分账金额',
  `pay_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单实付款金额',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '付款状态(10未付款 20已付款)',
  `pay_mode` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式(0未知 1微信 2余额 3现金 4支付宝)',
  `transaction_id` varchar(100) NOT NULL DEFAULT '' COMMENT '微信支付交易号',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `shop_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '商家状态(10未接单 20已接单)',
  `shop_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接单时间',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '配送状态(10未配送 20配送中 30已配送)',
  `delivery_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '配送时间',
  `receipt_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '收货状态(10未收货 20已收货)',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态(10进行中 20取消 30已完成)',
  `refund_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '退款状态(10退款中 20已退款)',
  `refund_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退款时间',
  `refund_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '退款理由',
  `refund_id` varchar(100) NOT NULL DEFAULT '' COMMENT '微信退款单号',
  `is_cmt` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否评价(0待评价 1已评价)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='订单表';
-- ----------------------------
-- 订单商品表
-- ----------------------------
DROP TABLE IF EXISTS `hema_order_goods`;
CREATE TABLE IF NOT EXISTS `hema_order_goods` (
  `order_goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品封面图id',
  `spec_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '规格类型(10单规格 20多规格)',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '' COMMENT '商品sku标识',
  `goods_spec_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格id',
  `goods_attr` varchar(500) NOT NULL DEFAULT '' COMMENT '商品规格信息',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品售价',
  `line_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品划线价',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总价',
  `refund_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退单数量',
  `refund_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退单总价',
  `is_gift` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否赠送',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`order_goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='订单商品表';
-- ----------------------------
-- 订单表配送表
-- ----------------------------
DROP TABLE IF EXISTS `hema_order_delivery`;
CREATE TABLE IF NOT EXISTS `hema_order_delivery` (
  `order_delivery_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号id',
  `company` varchar(10) NOT NULL DEFAULT 'self' COMMENT '配送公司(self=自配 sf=顺丰 dada=达达 uu=UU)',
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '配送单号',
  `distance` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '配送距离',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '配送费用',
  `linkman` varchar(50) NOT NULL DEFAULT '' COMMENT '骑手姓名',
  `avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '骑手头像',
  `gender` varchar(10) NOT NULL DEFAULT '' COMMENT '骑手性别',
  `phone` varchar(50) NOT NULL DEFAULT '' COMMENT '骑手电话',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '配送状态(10待骑手接单 20骑手正赶往商家 30骑手已到店 40骑手开始配送 50骑手已送达)',
  `delivery_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '配送时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态(10进行中 20被取消 30已完成)',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单号',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_delivery_id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='订单表配送表';

-- ----------------------------
-- 订单收货地址表
-- ----------------------------
DROP TABLE IF EXISTS `hema_order_address`;
CREATE TABLE IF NOT EXISTS `hema_order_address` (
  `order_address_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `location` varchar(50) NOT NULL DEFAULT '' COMMENT '位置坐标',
  `province` varchar(20) NOT NULL DEFAULT '' COMMENT '所在省份',
  `city` varchar(20) NOT NULL DEFAULT '' COMMENT '所在城市',
  `district` varchar(20) NOT NULL DEFAULT '' COMMENT '所在区/县',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`order_address_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='订单收货地址表';
-- ----------------------------
-- 订单评价表
-- ----------------------------
DROP TABLE IF EXISTS `hema_comment`;
CREATE TABLE IF NOT EXISTS `hema_comment` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `serve`  FLOAT(3) unsigned NOT NULL DEFAULT '5' COMMENT '服务评分',
  `speed` FLOAT(3) unsigned NOT NULL DEFAULT '5' COMMENT '速度评分',
  `flavor` FLOAT(3) unsigned NOT NULL DEFAULT '5' COMMENT '服务评分',
  `ambient` FLOAT(3) unsigned NOT NULL DEFAULT '5' COMMENT '服务评分',
  `content` varchar(255) NOT NULL DEFAULT '这家伙很懒，什么都没写' COMMENT '详情',
  `reply` varchar(255) NOT NULL DEFAULT '' COMMENT '回复详情',
  `is_show` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示(0隐藏，1显示)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='订单评价表';
-- ----------------------------
-- 店员表
-- ----------------------------
DROP TABLE IF EXISTS `hema_shop_clerk`;
CREATE TABLE IF NOT EXISTS `hema_shop_clerk` (
  `shop_clerk_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wx_open_id` varchar(50) NOT NULL DEFAULT '' COMMENT '微信公众号唯一标识',
  `cid` varchar(50) NOT NULL DEFAULT '' COMMENT 'APP用户唯一标识',
  `linkman` varchar(20) NOT NULL DEFAULT '' COMMENT '店员姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '密码',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '身份(10店员 20店长)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`shop_clerk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8 COMMENT='店员表';
-- ----------------------------
-- 餐桌表
-- ----------------------------
DROP TABLE IF EXISTS `hema_table`;
CREATE TABLE IF NOT EXISTS `hema_table` (
  `table_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `table_name` varchar(50) NOT NULL DEFAULT '' COMMENT '餐桌名称',
  `row_no` varchar(10) NOT NULL DEFAULT '' COMMENT '餐桌编号',
  `sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '排序方式(数字越小越靠前)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='餐桌表';
-- ----------------------------
-- 线上买单记录表
-- ----------------------------
DROP TABLE IF EXISTS `hema_paybill_log`;
CREATE TABLE IF NOT EXISTS `hema_paybill_log` (
  `paybill_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_no` varchar(100) NOT NULL DEFAULT '' COMMENT '订单编号',
  `transaction_id` varchar(100) NOT NULL DEFAULT '' COMMENT '支付平台订单号',
  `pay_mode` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '支付类型(1微信 4支付宝)',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`paybill_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='线上买单记录表';

-- ----------------------------
-- 二维码表
-- ----------------------------
DROP TABLE IF EXISTS `hema_qrcode`;
CREATE TABLE IF NOT EXISTS `hema_qrcode` (
  `qrcode_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '类型(10小程序码 20门店码 30桌码 40wifi码 50买单码 60自定义)',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '跳转位置',
  `table_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '餐桌ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`qrcode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='二维码表';
-- ----------------------------
-- 预约表
-- ----------------------------
DROP TABLE IF EXISTS `hema_pact`;
CREATE TABLE IF NOT EXISTS `hema_pact` (
  `pact_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `linkman` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `pact_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '约定时间',
  `people` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '约定人数',
  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '客户留言',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '状态，10预约中 20已过期 30已守约,40已取消',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`pact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='预约表';
-- ----------------------------
-- 口味选项表
-- ----------------------------
DROP TABLE IF EXISTS `hema_flavor`;
CREATE TABLE IF NOT EXISTS `hema_flavor` (
  `flavor_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '口味名称',
  `sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '排序方式(数字越小越靠前)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`flavor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='口味选项表';
-- ----------------------------
-- 云设备表
-- ----------------------------
DROP TABLE IF EXISTS `hema_device`;
CREATE TABLE IF NOT EXISTS `hema_device` (
  `device_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `dev_type` varchar(50) NOT NULL DEFAULT '' COMMENT '设备类型',
  `dev_name` varchar(50) NOT NULL DEFAULT '' COMMENT '设备名称',
  `dev_id` varchar(50) NOT NULL DEFAULT '' COMMENT '设备编号',
  `dev_key` varchar(50) NOT NULL DEFAULT '' COMMENT '设备密钥',
  `is_open` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '开关(0关闭 1开启)',
  `values` varchar(500) NOT NULL DEFAULT '' COMMENT '自定义设置',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='云设备表'; 
-- ----------------------------
-- 优惠券表
-- ----------------------------
DROP TABLE IF EXISTS `hema_coupon`;
CREATE TABLE IF NOT EXISTS `hema_coupon` (
  `coupon_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `initial_days` int(11) unsigned NOT NULL DEFAULT '30' COMMENT '有效天数',
  `use_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用数量',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '分类（10现金券 20折扣券 30满赠券 40单品券）',
  `order_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额（满足条件）',
  `values` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠参数',
  `explain` varchar(255) NOT NULL DEFAULT '' COMMENT '规则说明',
  `gift` longtext NOT NULL COMMENT '赠送礼物',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='优惠券表';
-- ----------------------------
-- 优惠券群发记录表
-- ----------------------------
DROP TABLE IF EXISTS `hema_coupon_log`;
CREATE TABLE IF NOT EXISTS `hema_coupon_log` (
  `coupon_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送数量',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`coupon_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='优惠券群发记录表';
-- ----------------------------
-- 优惠券用户领取表
-- ----------------------------
DROP TABLE IF EXISTS `hema_coupon_user`;
CREATE TABLE IF NOT EXISTS `hema_coupon_user` (
  `coupon_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '分类（10现金券 20折扣券 30满赠券 40单品券）',
  `order_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额（满足条件）',
  `values` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠参数',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `expire_star` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效起始时间',
  `expire_end` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效结束时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '状态（10正常 20核销）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`coupon_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='优惠券用户领取表';
-- ----------------------------
-- 充值套餐
-- ----------------------------
DROP TABLE IF EXISTS `hema_recharge_plan`;
CREATE TABLE IF NOT EXISTS `hema_recharge_plan` (
  `recharge_plan_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值金额',
  `gift_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '赠品类型（10优惠券，20现金）',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `gift_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '赠送金额',
  `sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '排序方式(数字越小越靠前)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`recharge_plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='充值套餐表';
-- ----------------------------
-- 充值记录表
-- ----------------------------
DROP TABLE IF EXISTS `hema_recharge_log`;
CREATE TABLE IF NOT EXISTS `hema_recharge_log` (
  `recharge_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `mode` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '充值模式（10自助 20后台）',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '充值类型（10余额 20积分）',
  `transaction_id` varchar(100) NOT NULL DEFAULT '' COMMENT '微信支付交易号',
  `pay_mode` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `give_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送金额',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`recharge_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='充值记录表';
-- ----------------------------
-- 签到记录表
-- ----------------------------
DROP TABLE IF EXISTS `hema_sign_log`;
CREATE TABLE IF NOT EXISTS `hema_sign_log` (
  `sign_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号ID',
  `type` varchar(20) NOT NULL DEFAULT 'score' COMMENT '奖励类型(score=积分 money余额)',
  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '奖励数值',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`sign_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='签到记录表';
-- ----------------------------
-- 商品分类表
-- ----------------------------
DROP TABLE IF EXISTS `hema_category`;
CREATE TABLE IF NOT EXISTS `hema_category` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品分类id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序方式(数字越小越靠前)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商品分类表';
-- ----------------------------
-- 商品表
-- ----------------------------
DROP TABLE IF EXISTS `hema_goods`;
CREATE TABLE IF NOT EXISTS `hema_goods` (
  `goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品图片',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `selling_point` varchar(255) NOT NULL DEFAULT '' COMMENT '商品卖点',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类id',
  `spec_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格(10单规格 20多规格)',
  `content` longtext NOT NULL COMMENT '商品详情',
  `start_sale` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '起售数量',
  `goods_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销量数量',
  `goods_sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '商品排序(数字越小越靠前)',
  `goods_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '商品状态(10上架 20下架)',
  `pack_price` decimal(10,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '包装费用',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`goods_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商品表';

-- ----------------------------
-- 商品规格表
-- ----------------------------
DROP TABLE IF EXISTS `hema_goods_spec`;
CREATE TABLE IF NOT EXISTS `hema_goods_spec` (
  `goods_spec_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品规格id',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '' COMMENT '商品spu标识',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '多规格商品图片id',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品售价',
  `line_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品划线价',
  `stock_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前库存数量',
  `goods_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品销量',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`goods_spec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商品规格表';

-- ----------------------------
-- 产品规格属性表
-- ----------------------------
DROP TABLE IF EXISTS `hema_goods_spec_rel`;
CREATE TABLE IF NOT EXISTS `hema_goods_spec_rel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `spec_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '规格组id',
  `spec_value_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '规格值id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='产品规格属性表';

-- ----------------------------
-- 规格表
-- ----------------------------
DROP TABLE IF EXISTS `hema_spec`;
CREATE TABLE IF NOT EXISTS `hema_spec` (
  `spec_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格组id',
  `spec_name` varchar(255) NOT NULL DEFAULT '' COMMENT '规格组名称',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`spec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='规格表';

-- ----------------------------
-- 规格属性表
-- ----------------------------
DROP TABLE IF EXISTS `hema_spec_value`;
CREATE TABLE IF NOT EXISTS `hema_spec_value` (
  `spec_value_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格值id',
  `spec_value` varchar(255) NOT NULL COMMENT '规格值',
  `spec_id` int(11) NOT NULL COMMENT '规格组id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`spec_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='规格属性表';
-- ----------------------------
-- 微信批量发送信息表
-- ----------------------------
DROP TABLE IF EXISTS `hema_wechat_batch_send`;
CREATE TABLE `hema_wechat_batch_send` (
  `wechat_batch_send_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号ID',
  `msg_id` varchar(50) NOT NULL DEFAULT '' COMMENT '任务ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息标题',
  `msg_type` varchar(20) NOT NULL DEFAULT '' COMMENT '消息类型',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '消息内容',
  `recommend` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐语',
  `need_open_comment` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启评论，0关闭，1开启',
  `only_fans_can_comment` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '谁可评论，0所有人，1仅粉丝',
  `send_ignore_reprint` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '为转载时，0停止群发，1继续群发',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '内容描述',
  `status` int(11) unsigned NOT NULL DEFAULT '10' COMMENT '群发状态',
  `fans_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '群发人数',
  `send_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成功人数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`wechat_batch_send_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='微信批量发送信息表';

-- ----------------------------
-- 公众号素材表
-- ----------------------------
DROP TABLE IF EXISTS `hema_material`;
CREATE TABLE `hema_material` (
  `material_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '素材名称',
  `file_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '文件类型 10=图片，20=音频，30=视频，40=图文',
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '本地文件名称',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '素材media_id',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '素材网络地址',
  `text_no` varchar(20) NOT NULL DEFAULT '' COMMENT '图文素材编号',
  `introduction` varchar(255) NOT NULL DEFAULT '' COMMENT '视频素材描述',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='公众号素材表';

-- ----------------------------
-- 公众号图文素材详情表
-- ----------------------------
DROP TABLE IF EXISTS `hema_material_text`;
CREATE TABLE `hema_material_text` (
  `material_text_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录编号',
  `text_no` varchar(20) NOT NULL DEFAULT '' COMMENT '素材编号',
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '素材信息序号',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `author` varchar(50) NOT NULL DEFAULT '' COMMENT '作者',
  `digest` varchar(100) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` longtext NOT NULL COMMENT '正文',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '素材media_id',
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '本地文件名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面网络地址',
  `content_source_url` varchar(255) NOT NULL DEFAULT '#' COMMENT '图文链接地址',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`material_text_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='公众号图文素材详情表';

-- ----------------------------
-- 公众号关键字回复
-- ----------------------------
DROP TABLE IF EXISTS `hema_keyword`;
CREATE TABLE `hema_keyword` (
  `keyword_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号ID',
  `keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '关键字',
  `type` varchar(10) NOT NULL DEFAULT 'text' COMMENT '消息类型 text=文字，image=图片，voice=音频，video=视频，music=音乐，news=图文',
  `is_open` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启 0=关闭，1=开启',
  `content` longtext COMMENT '消息内容',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`keyword_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='公众号关键字回复';
-- ----------------------------
-- 用户表
-- ----------------------------
DROP TABLE IF EXISTS `hema_user`;
CREATE TABLE IF NOT EXISTS `hema_user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `union_id` varchar(50) NOT NULL DEFAULT '' COMMENT '微信开放平台唯一标识',
  `open_id` varchar(50) NOT NULL DEFAULT '' COMMENT '微信小程序唯一标识',
  `wx_open_id` varchar(50) NOT NULL DEFAULT '' COMMENT '微信公众号唯一标识',
  `alipay_open_id` varchar(50) NOT NULL DEFAULT '' COMMENT '支付宝唯一标识',
  `is_wechat` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否关注微信公众号',
  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '登录密码',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `province` varchar(50) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '城市',
  `linkman` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `birthday` varchar(50) NOT NULL DEFAULT '' COMMENT '会员生日',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '钱包余额',
  `pay` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '消费金额',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户积分',
  `converted` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已兑换积分',
  `v` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '会员等级',
  `address_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '默认收货地址',
  `login_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户表';
-- ----------------------------
-- 用户地址表
-- ----------------------------
DROP TABLE IF EXISTS `hema_address`;
CREATE TABLE IF NOT EXISTS `hema_address` (
  `address_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `location` varchar(50) NOT NULL DEFAULT '' COMMENT '位置坐标',
  `province` varchar(20) NOT NULL DEFAULT '' COMMENT '所在省份',
  `city` varchar(20) NOT NULL DEFAULT '' COMMENT '所在城市',
  `district` varchar(20) NOT NULL DEFAULT '' COMMENT '所在区/县',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户地址表';

-- ----------------------------
-- 小程序页面设计表
-- ----------------------------
DROP TABLE IF EXISTS `hema_page`;
CREATE TABLE IF NOT EXISTS `hema_page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `page_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '页面类型(10首页 20自定义页)',
  `page_data` longtext NOT NULL COMMENT '页面数据',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8 COMMENT='小程序页面设计表';
-- ----------------------------
-- 站点配置表
-- ----------------------------
DROP TABLE IF EXISTS `hema_setting`;
CREATE TABLE `hema_setting` (
  `key` varchar(30) NOT NULL COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容（json格式）',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站点配置表';
-- ----------------------------
-- 图库表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `hema_upload_file` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `storage` varchar(20) NOT NULL DEFAULT '' COMMENT '存储方式',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件分组id',
  `domain` varchar(255) NOT NULL DEFAULT '' COMMENT '存储域名',
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小(字节)',
  `file_type` varchar(20) NOT NULL DEFAULT '' COMMENT '文件类型',
  `file_ext` varchar(20) NOT NULL DEFAULT '' COMMENT '文件扩展名',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `shop_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `applet_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='图库表';
-- ----------------------------
-- 图库分组表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `hema_upload_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `group_type` varchar(10) NOT NULL DEFAULT '' COMMENT '文件类型',
  `group_name` varchar(30) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类排序(数字越小越靠前)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `shop_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `applet_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`group_id`),
  KEY `type_index` (`group_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='图库分组表';
