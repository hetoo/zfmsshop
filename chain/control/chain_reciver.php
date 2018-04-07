<?php
/**
 * 门店代收
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class chain_reciverControl extends BaseChainCenterControl{
    public function __construct(){
        parent::__construct();
    }
    /**
     * 操作中心
     */
    public function indexOp() {
        $model_chain_order = Model('chain_order');
        $model_order = Model('order');
        $condition = array();
        $condition['chain_id'] = $_SESSION['chain_id'];
        if ($_GET['search_state_type'] == 'yes') {//代收状态 10未到站,20已到站,30已提取
            $condition['dlyo_state'] = 30;
        } else {
            $condition['dlyo_state'] = array('in',array(10,20));
        }
        if ($_GET['keyword'] != '') {
            if ($_GET['search_key_type'] == 'dlyo_pickup_code') {
            	$condition['dlyo_pickup_code'] = preg_match('/^\d{6}$/',$_GET['keyword']) ? $_GET['keyword'] : -1;
            } elseif ($_GET['search_key_type'] == 'order_sn') {
                $condition['order_sn'] = preg_match('/^\d{10,20}$/',$_GET['keyword']) ? $_GET['keyword'] : -1;
            } else {
                $condition['reciver_mobphone'] = preg_match('/^\d{11}$/',$_GET['keyword']) ? $_GET['keyword'] : -1;
            }
        }
        $dorder_list = $model_chain_order->getDeliveryOrderList($condition, 10);
        if (!empty($dorder_list)) {
            $order_ids = array();
            foreach ($dorder_list as $k => $v) {
                $order_ids[] = $v['order_id'];
            }
            $condition = array();
            $condition['order_id'] = array('in',$order_ids);
            $order_list = $model_order->getOrderList($condition);
            Tpl::output('order_list', $order_list);
        }
        Tpl::output('dorder_list', $dorder_list);
        Tpl::output('show_page', $model_chain_order->showpage());

        $dorder_state = $model_chain_order->getDeliveryOrderState();
        Tpl::output('dorder_state', $dorder_state);

        Tpl::showpage('chain_reciver.list');
    }
    /**
     * 查看物流
     */
    public function get_expressOp() {
        Tpl::showpage('chain_reciver.get_express', 'null_layout');
    }
    /**
     * 从第三方取快递信息
     */
    public function ajax_get_expressOp(){
        $content = Model('express')->get_express($_GET['e_code'], $_GET['shipping_code']);
        
        $output = array();
        foreach ($content as $k=>$v) {
            if ($v['time'] == '') continue;
            $output[]= $v['time'].'&nbsp;&nbsp;'.$v['context'];
        }
        if (empty($output)) exit(json_encode(false));
        echo json_encode($output);
    }
    /**
     * 取件通知
     */
    public function arrive_pointOp() {
        $model_chain_order = Model('chain_order');
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            showDialog(L('wrong_argument'));
        }
        $pickup_code = $this->createPickupCode();
        $dorder_info = $model_chain_order->getDeliveryOrderInfo(array('order_id' => $order_id, 'chain_id' => $_SESSION['chain_id']));
        if (empty($dorder_info)) {
            showDialog('订单错误', 'reload', 'error');
        }
        $order_info = Model('order')->getOrderInfo(array('order_id' => $order_id));
        if ($order_info['order_state'] != ORDER_STATE_SEND) showDialog('订单错误', 'reload', 'error');
        // 更新提货订单表数据
        $update = array();
        $update['dlyo_pickup_code'] = $pickup_code;
        // 更新订单扩展表数据
        Model('order')->editOrderCommon($update, array('order_id' => $order_id));
        $update['dlyo_state'] = 20;
        $model_chain_order->editDeliveryOrder($update, array('order_id' => $order_id, 'chain_id' => $_SESSION['chain_id']));
        // 发送短信提醒
        RealTimePush('sendPickupcode', array('pickup_code' => $pickup_code, 'order_id' => $order_id));
        showDialog('操作成功', 'reload', 'succ');
    }
    /**
     * 提货验证
     */
    public function pickup_parcelOp() {
        if (chksubmit()) {
            $order_id = intval($_POST['order_id']);
            $pickup_code = intval($_POST['pickup_code']);
            if ($order_id <= 0 || $pickup_code <= 0) {
                showDialog(L('wrong_argument'), '', 'error', 'DialogManager.close("pickup_parcel")');
            }
            $order_info = Model('order')->getOrderInfo(array('order_id' => $order_id));
            if ($order_info['order_state'] != ORDER_STATE_SEND) showDialog('订单错误', '', 'error', 'DialogManager.close("pickup_parcel")');
            $model_chain_order = Model('chain_order');
            $dorder_info = $model_chain_order->getDeliveryOrderInfo(array('order_id' => $order_id, 'chain_id' => $_SESSION['chain_id'], 'dlyo_pickup_code' => $pickup_code));
            if (empty($dorder_info)) {
                showDialog('提货码错误', '', 'error', 'DialogManager.close("pickup_parcel")');
            }
            $update = array();
            $update['dlyo_state'] = 30;
            $result = $model_chain_order->editDeliveryOrder($update, array('order_id' => $order_id, 'chain_id' => $_SESSION['chain_id'], 'dlyo_pickup_code' => $pickup_code));
            if ($result) {
                // 更新订单状态
                if ($order_info['order_state'] != ORDER_STATE_SUCCESS) {
                    Logic('order')->changeOrderStateReceive($order_info, 'buyer', '门店', '门店确认收货');
                }
                showDialog('操作成功，订单完成', 'reload', 'succ', 'DialogManager.close("pickup_parcel")');
            } else {
                showDialog('操作失败', '', 'error', 'DialogManager.close("pickup_parcel")');
            }
        }
        Tpl::showpage('chain_reciver.pickup_parcel', 'null_layout');
    }
    /**
     * 生成提货码
     */
    private function createPickupCode() {
        return rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
    }
}
