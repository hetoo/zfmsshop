<?php
/**
 * 保证金记录
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit ('Access Invalid!');
class earnest_moneyControl extends BaseSellerControl {
    public function indexOp(){
        $this->log_listOp();
    }

    public function log_listOp() {
        $model_log = Model('earnest_money');
        $condition = array();
        $condition['lg_member_id'] = $_SESSION['member_id'];
        if(!empty($_GET['lg_desc'])) {
            $condition['lg_desc'] = array('like', '%'.$_GET['lg_desc'].'%');
        }
        $condition['lg_add_time'] = array('time', array(strtotime($_GET['add_time_from']), strtotime($_GET['add_time_to'])));

        $log_list = $model_log->getEarnestMoneyLogList($condition, 10);
        Tpl::output('log_list', $log_list);
        Tpl::output('show_page', $model_log->showpage(2));

        $this->profile_menu('log_list');
        Tpl::showpage('earnest_money_log.list');
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = array();
        $menu_array[] = array(
            'menu_key' => 'log_list',
            'menu_name' => '保证金记录列表',
            'menu_url' => urlShop('earnest_money', 'log_list')
        );
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }
}