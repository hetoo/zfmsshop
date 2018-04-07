<?php
/**
 * 门店配送
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class chain_orderControl extends BaseChainCenterControl{
    public function __construct(){
        parent::__construct();
    }
    
    public function indexOp() {
        $model_order = Model('order');
        $condition = array();
        $condition['chain_id'] = $_SESSION['chain_id'];
        $condition['order_type'] = 5;
        $condition['lock_state'] = 0;
        if ($_GET['search_state_type'] == 'yes') {//已完成配送
            $condition['order_state'] = ORDER_STATE_SUCCESS;
        } else {
            $condition['order_state'] = array('in',array(ORDER_STATE_PAY,ORDER_STATE_SEND));
        }
        if ($_GET['keyword'] != '') {
            $condition['order_sn'] = preg_match('/^\d{10,20}$/',$_GET['keyword']) ? $_GET['keyword'] : -1;
        }

        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common'));
        //页面中显示那些操作
        foreach ($order_list as $key => $order_info) {
            foreach ($order_info['extend_order_goods'] as & $value) {
                $value['image_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
                $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
            }
            usort($order_info['extend_order_goods'],function($a,$b){
                if ($a['goods_type'] == $b['goods_type']) return 0;
            	return $a['goods_type'] > $b['goods_type'] ? 1 : -1;
            });
            $order_list[$key] = $order_info;
        }
        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        Tpl::showpage('chain_order.list');
    }
    /**
     * 发货
     */
    public function sendOp() {
        $order_id = intval($_GET['order_id']);
        $model_order = Model('order');
        $logic_order = Logic('order');
        $model_chain_order = Model('chain_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $condition['order_type'] = 5;
        $condition['lock_state'] = 0;
        $order_info = $model_order->getOrderInfo($condition,array('order_common'));
        if (chksubmit()) {
            if (empty($order_info)) {
                showDialog('订单错误', 'reload', 'error', 'DialogManager.close("chain_order")');
            }
            $result = $logic_order->changeOrderSend($order_info, 'chain', $_SESSION['chain_name'], $_POST);
            if ($result['state'] && $order_info['order_state'] != ORDER_STATE_SUCCESS) {
                showDialog('操作成功，发货完成', 'reload', 'succ', 'DialogManager.close("chain_order")');
            } else {
                showDialog('操作失败', '', 'error', 'DialogManager.close("chain_order")');
            }
        }
        Tpl::output('order_info',$order_info);
        $express_list  = rkcache('express',true);
        Tpl::output('express_list',$express_list);
        Tpl::showpage('chain_order.send', 'null_layout');
    }
    /**
     * 确认完成配送
     */
    public function succ_yesOp() {
        $model_order = Model('order');
        $model_chain_order = Model('chain_order');
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            showDialog(L('wrong_argument'));
        }
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $condition['order_state'] = ORDER_STATE_SEND;
        $condition['order_type'] = 5;
        $condition['lock_state'] = 0;
        $order_info = $model_order->getOrderInfo($condition);
        if (empty($order_info)) {
            showDialog('订单错误', 'reload', 'error');
        }
        $order_info = Model('order')->getOrderInfo(array('order_id' => $order_id));
        $result = Logic('order')->changeOrderStateReceive($order_info,'chain',$_SESSION['chain_name'],'订单已经完成配送，门店更改订单为完成状态');
        if ($result['state']) {
            showDialog('操作成功','reload','succ');
        } else {
            showDialog('操作失败','reload','error');
        }
    }
    /**
     * 查看物流
     */
    public function get_expressOp() {
        $express = rkcache('express',true);
        $e_code = $express[$_GET['shipping_express_id']]['e_code'];
        $_GET['e_code'] = $e_code;
        Tpl::showpage('chain_reciver.get_express', 'null_layout');
    }
}
