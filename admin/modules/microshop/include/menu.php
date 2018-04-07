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
$_menu['microshop'] = array (
        'name' => '微商城',
        'child' => array(
                array(
                        'name' => $lang['nc_config'], 
                        'child' => array(
                                'manage' => $lang['nc_microshop_manage'],
                                'comment' => $lang['nc_microshop_comment_manage'],
                                'adv' => $lang['nc_microshop_adv_manage']
                        )
                ),
                array(
                        'name' => '随心看', 
                        'child' => array(
                                'goods' => $lang['nc_microshop_goods_manage'],
                                'goods_class' => $lang['nc_microshop_goods_class']
                        )
                ),
                array(
                        'name' => '个人秀', 
                        'child' => array(
                                'personal' => $lang['nc_microshop_personal_manage'],
                                'personal_class' => $lang['nc_microshop_personal_class']
                        )
                        
                ),
                array(
                        'name' => '店铺街',
                        'child' => array(
                                'store' => $lang['nc_microshop_store_manage']
                        )
                )
        )
);