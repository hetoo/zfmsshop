INSERT INTO  `#__setting` (`name`, `value`) VALUES ('mobile_isuse', '1');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('mobile_app', 'mb_app.png');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('mobile_apk', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('mobile_apk_version', '3.0');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('mobile_ios', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('mobile_wx','');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('baidu_push_ios', '1');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('baidu_push_ios_key', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('baidu_push_ios_secret', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('baidu_push_android_key', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('baidu_push_android_secret', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('video_isuse', '1');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('video_modules_logo', '');
INSERT INTO  `#__setting` (`name`, `value`) VALUES ('video_modules_name', '');

CREATE TABLE  `#__mb_category` (
  `gc_id` smallint(5) unsigned DEFAULT NULL COMMENT '商城系统的分类ID',
  `gc_thumb` varchar(150) DEFAULT NULL COMMENT '缩略图',
  PRIMARY KEY (`gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='一级分类缩略图[手机端]';

INSERT INTO  `#__mb_category` VALUES 
(1, '04890563322151999.png'),
(2, '04890563422383990.png'),
(3, '04890563504528679.png'),
(256, '04890563696273833.png'),
(308, '04890563795020268.png'),
(470, '04890563899590392.png'),
(530, '04890564008326975.png'),
(593, '04890564117641665.png'),
(662, '04890564217197194.png'),
(730, '04890564337411152.png'),
(825, '04890564448947743.png'),
(888, '04890564563319438.png'),
(959, '04890564663651232.png'),
(1037, '04890564750587950.png');

CREATE TABLE  `#__mb_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(500) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT '1来自手机端2来自PC端',
  `ftime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '反馈时间',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';

CREATE TABLE `#__mb_video_focus` (
  `focus_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '项目编号',
  `focus_type` varchar(50) DEFAULT NULL COMMENT '项目类型',
  `focus_url` varchar(200) DEFAULT NULL COMMENT '图片链接',
  `focus_image` varchar(100) NOT NULL DEFAULT '' COMMENT '项目内容',
  `focus_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '项目排序',
  PRIMARY KEY (`focus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='视频列表广告';

CREATE TABLE `#__mb_payment` (
  `payment_id` tinyint(1) unsigned NOT NULL COMMENT '支付索引id',
  `payment_code` varchar(15) NOT NULL COMMENT '支付代码名称',
  `payment_name` char(10) NOT NULL COMMENT '支付名称',
  `payment_config` varchar(2000) DEFAULT NULL COMMENT '支付接口配置信息',
  `payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '接口状态0禁用1启用',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机支付方式表';

INSERT INTO `#__mb_payment` (`payment_id`, `payment_code`, `payment_name`, `payment_config`, `payment_state`) VALUES
(1, 'alipay', '支付宝', '', '0'),
(2, 'wxpay', '微信支付', '', '0'),
(3, 'wxpay_jsapi', '微信支付JSAPI', '', '0'),
(4, 'alipay_native', '支付宝移动支付', '', '0');

CREATE TABLE  `#__mb_push` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `log_msg` varchar(300) NOT NULL COMMENT '推送内容',
  `log_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '推送类型:1为关键字,2为专题编号,3为商品编号,默认为1',
  `log_type_v` varchar(20) DEFAULT '' COMMENT '推送类型值',
  `msg_tag` varchar(10) NOT NULL COMMENT '标签组,默认为default',
  `msg_id` varchar(20) DEFAULT '0' COMMENT 'Android消息ID',
  `msg_ios_id` varchar(20) DEFAULT '0' COMMENT 'iOS消息ID',
  `ios_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'iOS应用状态:1为开发,2为生产',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`log_id`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推送通知记录表';

CREATE TABLE  `#__mb_seller_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌编号',
  `seller_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `seller_name` varchar(50) NOT NULL COMMENT '用户名',
  `token` varchar(50) NOT NULL COMMENT '登录令牌',
  `login_time` int(10) unsigned NOT NULL COMMENT '登录时间',
  `client_type` varchar(10) NOT NULL COMMENT '客户端类型 windows',
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户端商家登录令牌表';

CREATE TABLE  `#__mb_special` (
  `special_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题编号',
  `special_desc` varchar(20) NOT NULL COMMENT '专题描述',
  PRIMARY KEY (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机专题表';

CREATE TABLE  `#__mb_special_item` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题项目编号',
  `special_id` int(10) unsigned NOT NULL COMMENT '专题编号',
  `item_type` varchar(50) NOT NULL COMMENT '项目类型',
  `item_data` varchar(2000) NULL DEFAULT '' COMMENT '项目内容',
  `item_usable` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '项目是否可用 0-不可用 1-可用',
  `item_sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '项目排序',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机专题项目表';

CREATE TABLE  `#__mb_user_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `token` varchar(50) NOT NULL COMMENT '登录令牌',
  `login_time` int(10) unsigned NOT NULL COMMENT '登录时间',
  `client_type` varchar(10) NOT NULL COMMENT '客户端类型 android wap',
  `openid` varchar(50) NULL COMMENT '微信支付jsapi的openid缓存',
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='移动端登录令牌表';

CREATE TABLE `#__mb_video` (
  `video_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺索引id',
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '视频分类ID',
  `recommend_goods` text COMMENT '推荐商品',
  `promote_video` varchar(100) DEFAULT NULL COMMENT '推广位视频',
  `promote_text` varchar(200) DEFAULT NULL COMMENT '推广位文字',
  `demand_video` varchar(100) DEFAULT NULL COMMENT '点播视频',
  `promote_image` varchar(100) DEFAULT NULL COMMENT '推广位图片',
  `news_name` varchar(100) DEFAULT NULL COMMENT '资讯名称',
  `news_image` varchar(100) DEFAULT NULL COMMENT '资讯图片',
  `mobile_body` text COMMENT '手机端商品描述',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `page_view` int(10) unsigned DEFAULT '0' COMMENT '点击率',
  `video_identity` varchar(50) NOT NULL DEFAULT '' COMMENT '标识 news资讯 demand点播',
  `video_identity_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '标识类型 1资讯 2点播',
  `store_name` VARCHAR(150) NOT NULL COMMENT '店铺名称',
  PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='点播、资讯列表';

CREATE TABLE `#__mb_video_cate` (
  `cate_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `cate_name` varchar(100) NOT NULL COMMENT '分类名称',
  `type_id` int(10) unsigned DEFAULT '0' COMMENT '类型id',
  `type_name` varchar(100) DEFAULT '' COMMENT '类型名称',
  `cate_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `cate_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `cate_image` varchar(300) NOT NULL COMMENT '分类图片',
  `cate_description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐，前台显示 0否1是',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='视频分类表';
