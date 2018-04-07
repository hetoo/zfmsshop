<?php
/**
 * 分销会员结算管理
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

class distri_billControl extends MemberDistributeControl{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 分销员分销订单管理
     */
    public function indexOp(){
        $this->bill_listOp();
    }

    public function bill_listOp(){
        $model_bill = Model('dis_bill');
        $condition = array('dis_member_id' => $_SESSION['member_id']);
        if(trim($_GET['goods_name'])){
            $condition['goods_name'] = array('like', '%' . $_GET['goods_name'] . '%');
        }
        if(is_numeric($_GET['bill_state']) && intval($_GET['bill_state']) >= 0){
            $condition['log_state'] = intval($_GET['bill_state']);
        }
        $fields = '*';
        $list = $model_bill->getDistriBillList($condition, $fields, 15);

        self::profile_menu('log','bill_list');

        Tpl::output('bill_list',$list);
        Tpl::output('show_page',$model_bill->showpage(2));
        Tpl::showpage('distri_bill.list');
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key=''){
        $menu_array = array(
            array('menu_key'=>'bill_list',        'menu_name'=>'分销结算列表',    'menu_url'=>'index.php?act=distri_bill&op=bill_list')
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }

}