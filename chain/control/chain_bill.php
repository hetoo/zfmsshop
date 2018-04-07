<?php
/**
 * 门店订单结算
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit('Access Invalid!');
class chain_billControl extends BaseChainCenterControl {

    public function __construct() {
        parent::__construct();
        $chain_order_type = array(
            '1' => '门店发货',
            '3' => '门店自提',
            '5' => '门店配送'
            );//门店订单类型:1门店发货,3门店自提,5门店配送
        Tpl::output('chain_order_type',$chain_order_type);
    }

    /**
     * 结算列表
     *
     */
    public function indexOp() {
        $model_chain = Model('chain');
        $model_bill = Model('chain_bill');
        $condition = array();
        $condition['ob_chain_id'] = $_SESSION['chain_id'];
        if ($_GET['ob_id']) {
            $condition['ob_id'] = intval($_GET['ob_id']);
        }
        $bill_list = $model_bill->getChainBillList($condition,12);
        Tpl::output('bill_list',$bill_list);
        Tpl::output('show_page',$model_bill->showpage());
        $chain_info = $model_chain->getChainInfo(array('chain_id' => $_SESSION['chain_id']));
        Tpl::output('chain_info',$chain_info);
        Tpl::showpage('chain_bill.index');
    }

    /**
     * 查看结算单详细
     *
     */
    public function show_billOp(){
        if (!preg_match('/^\d+$/',$_GET['ob_id'])) {
            showMessage('参数错误','','html','error');
        }
        $model_bill = Model('chain_bill');
		$condition = array();
		$condition['ob_id'] = intval($_GET['ob_id']);
		$condition['ob_chain_id'] = $_SESSION['chain_id'];
        $bill_info = $model_bill->getChainBillInfo($condition);
        if (!$bill_info){
            showMessage('参数错误','','html','error');
        }
        $order_condition = array();
        $order_condition['order_state'] = ORDER_STATE_SUCCESS;
        $order_condition['chain_id'] = $bill_info['ob_chain_id'];
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']) : null;
        if ($if_start_date || $if_end_date) {
            $order_condition['finnshed_time'] = array('time',array($start_unixtime,$end_unixtime));
        } else {
            $order_condition['finnshed_time'] = array('between',"{$bill_info['ob_start_date']},{$bill_info['ob_end_date']}");
        }
        if ($_GET['type'] =='refund'){
            //退款订单列表
            $model_refund = Model('refund_return');
            $refund_condition = array();
            $refund_condition['seller_state'] = 2;
            $refund_condition['chain_id'] = $bill_info['ob_chain_id'];
            $refund_condition['goods_id'] = array('gt',0);
            $refund_condition['finnshed_time'] = $order_condition['finnshed_time'];
            if (preg_match('/^\d{8,20}$/',$_GET['query_order_no'])) {
                $refund_condition['refund_sn'] = $_GET['query_order_no'];
            }
            $refund_list = $model_refund->getRefundReturnList($refund_condition,20,'refund_return.*,ROUND(refund_amount*commis_rate/100,2) as commis_amount');
            if (is_array($refund_list) && count($refund_list) == 1 && $refund_list[0]['refund_id'] == '') {
                $refund_list = array();
            }
            Tpl::output('refund_list',$refund_list);
            Tpl::output('show_page',$model_refund->showpage());
            $sub_tpl_name = 'chain_bill.show.refund_list';
            $this->profile_menu('show','refund_list');
        } else {
            if (preg_match('/^\d{8,20}$/',$_GET['query_order_no'])) {
                $order_condition['order_sn'] = $_GET['query_order_no'];
            }
            //订单列表
            $model_order = Model('order');
            $order_list = $model_order->getOrderList($order_condition,20);

            //然后取订单商品佣金
            $order_id_array = array();
            if (is_array($order_list)) {
                foreach ($order_list as $order_info) {
                    $order_id_array[] = $order_info['order_id'];
                }
            }
            $order_goods_condition = array();
            $order_goods_condition['order_id'] = array('in',$order_id_array);
            $field = 'SUM(ROUND(goods_pay_price*commis_rate/100,2)) as commis_amount,order_id';
            $commis_list = $model_order->getOrderGoodsList($order_goods_condition,$field,null,null,'','order_id','order_id');
            Tpl::output('commis_list',$commis_list);
            Tpl::output('order_list',$order_list);
            Tpl::output('show_page',$model_order->showpage());
            $sub_tpl_name = 'chain_bill.show.order_list';
            $this->profile_menu('show','order_list');
        }

        Tpl::output('sub_tpl_name',$sub_tpl_name);
        Tpl::output('bill_info',$bill_info);
        Tpl::showpage('chain_bill.show');
    }

    /**
     * 小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key='') {
        $menu_array = array();
        switch ($menu_type) {
            case 'show':
                $menu_array = array(
                array('menu_key'=>'order_list','menu_name'=>'订单列表', 'menu_url'=>'index.php?act=chain_bill&op=show_bill&ob_id='.$_GET['ob_id']),
                array('menu_key'=>'refund_list','menu_name'=>'退款订单','menu_url'=>'index.php?act=chain_bill&op=show_bill&type=refund&ob_id='.$_GET['ob_id'])
                );
                break;
        }
        Tpl::output('chain_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
