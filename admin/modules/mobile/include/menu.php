<?php
/**
 * 菜单
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
$_menu['mobile'] = array (
        'name'=>$lang['nc_mobile'],
        'child'=>array(
                array(
                        'name'=>'设置',
                        'child' => array(
                                'mb_setting' => '手机端设置',
                                'mb_special' => '模板设置',
                                'chain_flash' => '门店轮播',
                                'mb_category' => $lang['nc_mobile_catepic'],
                                'mb_app' => '应用安装',
                                'mb_feedback' => $lang['nc_mobile_feedback'],
                                'mb_payment' => '手机支付',
                                'mb_wx' => '微信二维码',
                                'mb_connect' => '第三方登录',
                                'mb_push' => '推送通知'
                        )
                ),
            array(
                'name'=>'视频',
                'child' => array(
                    'mb_video_setting' => '视频设置',
                    'mb_video_focus' => '广告图设置',
                    'mb_video_cate' => '视频分类管理',
                    'mb_demand' => '点播管理',
                    'mb_news' => '资讯管理',
                )
            ),
        )
);