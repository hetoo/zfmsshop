<?php
/**
 * 分销商会员页面
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class distri_centerControl extends MemberDistributeControl{
    function __construct()
    {
        parent::__construct();
    }

    public function homeOp(){
        Tpl::showpage('home');
    }
}