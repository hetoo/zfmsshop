<?php
/**
 * 分销订单
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

class distri_orderControl extends MemberDistributeControl{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 分销员分销订单管理
     */
    public function indexOp(){
        $this->order_listOp();
    }

    public function order_listOp(){
        $model_order = Model('dis_order');
        $condition = array('dis_member_id' => $_SESSION['member_id']);
        if(trim($_GET['goods_name'])){
            $condition['order_goods.goods_name'] = array('like', '%' . $_GET['goods_name'] . '%');
        }
        switch(intval($_GET['order_state'])){
            case 0:
                if(isset($_GET['order_state'])){
                    $condition['orders.order_state'] = 0;
                }
                break;
            case 10:
                $condition['orders.order_state'] = 10;
                $condition['orders.chain_code'] = 0;
                break;
            case 11:
                $condition['orders.order_state'] = 10;
                $condition['orders.chain_code'] = array('neq',0);
                break;
            case 20:
                $condition['orders.order_state'] = 20;
                $condition['orders.chain_code'] = 0;
                break;
            case 21:
                $condition['orders.order_state'] = 20;
                $condition['orders.chain_code'] = array('neq',0);
                break;
            case 30:
                $condition['orders.order_state'] = 30;break;
            case 40:
                $condition['orders.order_state'] = 40;break;
        }
        $condition['order_goods.is_dis'] = 1;
        $fields = '*';
        $list = $model_order->getMeberDistriOrderList($condition, $fields, 8);

        self::profile_menu('log','order_list');

        Tpl::output('order_list',$list);
        Tpl::output('show_page',$model_order->showpage(2));
        Tpl::showpage('distri_order.list');
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
            array('menu_key'=>'order_list',        'menu_name'=>'分销订单列表',    'menu_url'=>'index.php?act=distri_order&op=order_list')
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }

}