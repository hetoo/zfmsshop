<?php
/**
 * 发货
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


defined('InShopNC') or exit('Access Invalid!');

class store_deliverControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index,deliver');
    }

    /**
     * 发货列表
     *
     */
    public function indexOp() {
        $model_order = Model('order');
        $model_pintuan = Model('p_pintuan');
        $model_chain = Model('chain');
        if (!in_array($_GET['state'],array('deliverno','delivering','delivered'))) $_GET['state'] = 'deliverno';
        $order_state = str_replace(array('deliverno','delivering','delivered'),
                array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),$_GET['state']);
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['order_state'] = $order_state;
        $condition['order_type'] = array('in',array(1,2,4));
        if ($_GET['buyer_name'] != '') {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        if (preg_match('/^\d{10,20}$/',$_GET['order_sn'])) {
            $condition['order_sn'] = $_GET['order_sn'];
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
        $order_list = $model_order->getOrderList($condition,5,'*','order_id desc','',array('order_goods','order_common','member'));
        foreach ($order_list as $key => $order_info) {
            foreach ($order_info['extend_order_goods'] as $value) {
                $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
                $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
                $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
                $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
                if ($value['goods_type'] == 5) {
                    $order_info['zengpin_list'][] = $value;
                } else {
                    $order_info['goods_list'][] = $value;
                }
            }

            if (empty($order_info['zengpin_list'])) {
                $order_info['goods_count'] = count($order_info['goods_list']);
            } else {
                $order_info['goods_count'] = count($order_info['goods_list']) + 1;
            }
            if ($order_info['order_type'] == 4) {//拼团订单
                $order_id = $order_info['order_id'];
                $_info = $model_pintuan->getOrderInfo(array('order_id'=> $order_id));
                if ($_info['lock_state'] == 1) {
                    $order_info['lock_state'] = 1;
                }
            }
            if (empty($order_info['lock_state']) && $order_info['order_state'] == ORDER_STATE_PAY) {
                $chain_list = $model_chain->getChainSenderList($order_info);
                $order_info['chain_list'] = $chain_list;
                $order_info['chain_count'] = count($chain_list);
            }
            $order_list[$key] = $order_info;
        }
        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        self::profile_menu('deliver',$_GET['state']);
        Tpl::showpage('store_order.deliver');
    }

    /**
     * 发货
     */
    public function sendOp(){
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0){
            showMessage(Language::get('wrong_argument'),'','html','error');
        }

        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        $if_allow_send = intval($order_info['lock_state']) || !in_array($order_info['order_state'],array(ORDER_STATE_PAY,ORDER_STATE_SEND));
        if ($if_allow_send) {
            showMessage(Language::get('wrong_argument'),'','html','error');
        }

        if (chksubmit()){
            $logic_order = Logic('order');
            $_POST['reciver_info'] = $this->_get_reciver_info();
            $result = $logic_order->changeOrderSend($order_info, 'seller', $_SESSION['seller_name'], $_POST);
            if (!$result['state']) {
                showDialog($result['msg'],'','error');
            } else {
                showDialog($result['msg'],$_POST['ref_url'],'succ');
            }
        }

        Tpl::output('order_info',$order_info);
        //取发货地址
        $model_daddress = Model('daddress');
        if ($order_info['extend_order_common']['daddress_id'] > 0 ){
            $daddress_info = $model_daddress->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
        }else{
            //取默认地址
            $daddress_info = $model_daddress->getAddressList(array('store_id'=>$_SESSION['store_id']),'*','is_default desc',1);
            $daddress_info = $daddress_info[0];

            //写入发货地址编号
            $this->_edit_order_daddress($daddress_info['address_id'], $order_id);
        }
        Tpl::output('daddress_info',$daddress_info);

        $express_list  = rkcache('express',true);
        //快递公司
        $my_express_list = Model()->table('store_extend')->getfby_store_id($_SESSION['store_id'],'express');
        if (!empty($my_express_list)){
            $my_express_list = explode(',',$my_express_list);
        }
        //电子面单数据
        $e_waybill_model = Model('order_e_waybill');
        $e_waybill_info = $e_waybill_model->getOneOrder($order_id);

        Tpl::output('my_express_list',$my_express_list);
        Tpl::output('express_list',$express_list);
        Tpl::output('e_waybill_info',$e_waybill_info);
        Tpl::showpage('store_deliver.send');
    }

    /**
     * 批量发货
     */
    public function batch_sendOp(){
        $order_id = explode(',', trim($_GET['order_id']));

        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = array('in', $order_id);
        $condition['order_type'] = array('in',array(1,2,4));
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['order_state'] = ORDER_STATE_PAY;
        $order_list = $model_order->getOrderList($condition,'','*','order_id desc','',array('order_common','order_goods'));
        if(empty($order_list)){
            showMessage('没有可批量发货的订单','','html','error');
        }

        if (chksubmit()){
            $logic_order = Logic('order');
            foreach ($order_list as $k=>$v){
                //需要物流
                if(intval($_POST['shipping_type']) == 1){
                    $deliver_explain_name = 'deliver_explain_'.$k;
                    $shipping_code_name = 'shipping_code_'.$k;
                    $_POST['deliver_explain'] = $_POST[$deliver_explain_name];
                    $_POST['shipping_code'] = $_POST[$shipping_code_name];
                    $result = $logic_order->batchChangeOrderSend($v, 'seller', $_SESSION['seller_name'], $_POST);
                }else{
                    $_POST['shipping_express_id'] = 0;
                    $_POST['shipping_code'] = NULL;
                    $result = $logic_order->batchChangeOrderSend($v, 'seller', $_SESSION['seller_name'], $_POST);
                }
                if (!$result['state']) {
                    showDialog($result['msg'],'','error');
                }
            }
            showDialog($result['msg'],$_POST['ref_url'],'succ');
        }

        Tpl::output('order_list',$order_list);

        $order_id = array_keys($order_list);
        $order_id_str = implode(',', $order_id);
        Tpl::output('order_id',$order_id_str);
        //取发货地址
        $model_daddress = Model('daddress');
        if ($order_list[$order_id[0]]['extend_order_common']['daddress_id'] > 0 ){
            $daddress_info = $model_daddress->getAddressInfo(array('address_id'=>$order_list[$order_id[0]]['extend_order_common']['daddress_id']));

            //写入发货地址编号
            $this->_batch_edit_order_daddress($daddress_info['address_id'], $order_id);
        }else{
            //取默认地址
            $daddress_info = $model_daddress->getAddressList(array('store_id'=>$_SESSION['store_id']),'*','is_default desc',1);
            $daddress_info = $daddress_info[0];

            //写入发货地址编号
            $this->_batch_edit_order_daddress($daddress_info['address_id'], $order_id);
        }
        //快递公司
        $express_list  = rkcache('express',true);
        $my_express_list = Model()->table('store_extend')->getfby_store_id($_SESSION['store_id'],'express');
        if (!empty($my_express_list)){
            $my_express_list = explode(',',$my_express_list);
        }

        Tpl::output('express_list',$express_list);
        Tpl::output('my_express_list',$my_express_list);
        Tpl::output('daddress_info',$daddress_info);
        Tpl::showpage('store_deliver.batch_send');
    }

    /**
     * 编辑收货地址
     * @return boolean
     */
    public function buyer_address_editOp() {
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) return false;
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_common_info = $model_order->getOrderCommonInfo($condition);
        if (!$order_common_info) return false;
        $order_common_info['reciver_info'] = @unserialize($order_common_info['reciver_info']);
        Tpl::output('address_info',$order_common_info);

        Tpl::showpage('store_deliver.buyer_address.edit','null_layout');
    }

    /**
     * 编辑收货地址，批量发货页
     */
    public function batch_buyer_address_editOp() {
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) return false;
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_common_info = $model_order->getOrderCommonInfo($condition);
        if (!$order_common_info) return false;
        $order_common_info['reciver_info'] = @unserialize($order_common_info['reciver_info']);
        Tpl::output('address_info',$order_common_info);

        Tpl::showpage('store_deliver.batch_buyer_address.edit','null_layout');
    }

    /**
     * 收货地址保存
     */
    public function buyer_address_saveOp() {
        $model_order = Model('order');
        $data = array();
        $data['reciver_name'] = $_POST['reciver_name'];
        $data['reciver_info'] = $this->_get_reciver_info();
        $condition = array();
        $condition['order_id'] = intval($_POST['order_id']);
        $condition['store_id'] = $_SESSION['store_id'];
        $result = $model_order->editOrderCommon($data, $condition);
        if($result) {
            echo 'true';
        } else {
            echo 'flase';
        }
    }

    /**
     * 组合reciver_info
     */
    private function _get_reciver_info() {
        $reciver_info = array(
            'address' => $_POST['reciver_area'] . ' ' . $_POST['reciver_street'],
            'phone' => trim($_POST['reciver_mob_phone'] . ',' . $_POST['reciver_tel_phone'],','),
            'area' => $_POST['reciver_area'],
            'street' => $_POST['reciver_street'],
            'mob_phone' => $_POST['reciver_mob_phone'],
            'tel_phone' => $_POST['reciver_tel_phone'],
            'dlyp' => $_POST['reciver_dlyp'],
            'chain_price' => ncPriceFormat(floatval($_POST['chain_price']))
        );
        return serialize($reciver_info);
    }

    /**
     * 选择发货地址
     * @return boolean
     */
    public function send_address_selectOp() {
        Language::read('deliver');
        $address_list = Model('daddress')->getAddressList(array('store_id'=>$_SESSION['store_id']));
        Tpl::output('address_list',$address_list);
        Tpl::output('order_id', $_GET['order_id']);
        Tpl::showpage('store_deliver.daddress.select','null_layout');
    }

    /**
     * 保存发货地址修改
     */
    public function send_address_saveOp() {
        $order_id = explode(',', trim($_POST['order_id']));
        if(count($order_id) > 1){
            $result = $this->_batch_edit_order_daddress($_POST['daddress_id'], $order_id);
        }else{
            $result = $this->_edit_order_daddress($_POST['daddress_id'], $_POST['order_id']);
        }
        if($result) {
            echo 'true';
        } else {
            echo 'flase';
        }
    }

    /**
     * 修改发货地址
     */
    private function _edit_order_daddress($daddress_id, $order_id) {
        $model_order = Model('order');
        $data = array();
        $data['daddress_id'] = intval($daddress_id);
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        return $model_order->editOrderCommon($data, $condition);
    }

    /**
     * 批量修改发货地址
     * @param $daddress_id
     * @param array $order_id
     * @return mixed
     */
    private function _batch_edit_order_daddress($daddress_id, $order_id = array()) {
        $model_order = Model('order');
        $data = array();
        $data['daddress_id'] = intval($daddress_id);
        $condition = array();
        $condition['order_id'] = array('in', $order_id);
        $condition['store_id'] = $_SESSION['store_id'];
        return $model_order->editOrderCommon($data, $condition);
    }

    /**
     * 物流跟踪
     */
    public function search_deliverOp(){
        Language::read('member_member_index');
        $lang   = Language::getLangContent();

        $order_sn   = $_GET['order_sn'];
        if (!preg_match('/^\d{10,20}$/',$_GET['order_sn'])) showMessage(Language::get('wrong_argument'),'','html','error');
        $model_order    = Model('order');
        $condition['order_sn'] = $order_sn;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info) || $order_info['shipping_code'] == '') {
            showMessage('未找到信息','','html','error');
        }
        $order_info['state_info'] = orderState($order_info);
        Tpl::output('order_info',$order_info);
        //卖家发货信息
        $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
        Tpl::output('daddress_info',$daddress_info);

        //取得配送公司代码
        $express = rkcache('express',true);
        Tpl::output('e_code',$express[$order_info['extend_order_common']['shipping_express_id']]['e_code']);
        Tpl::output('e_name',$express[$order_info['extend_order_common']['shipping_express_id']]['e_name']);
        Tpl::output('e_url',$express[$order_info['extend_order_common']['shipping_express_id']]['e_url']);
        Tpl::output('shipping_code',$order_info['shipping_code']);

        self::profile_menu('search','search');
        Tpl::showpage('store_deliver.detail');
    }

    /**
     * 分派订单
     */
    public function chainOp(){
        $order_id = intval($_GET['order_id']);
        $model_order = Model('order');
        $model_chain = Model('chain');
        $model_chain_order = Model('chain_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['order_state'] = ORDER_STATE_PAY;
        $condition['order_type'] = array('in',array(1,2,4));
        $condition['lock_state'] = 0;
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        Tpl::output('order_info',$order_info);
        if ($order_info['chain_id']){
            $chain_info = $model_chain->getChainInfo(array('chain_id' => $order_info['chain_id']));
            Tpl::output('chain_info',$chain_info);
        }
        if (chksubmit()){
            if (empty($order_info)) {
                showDialog(Language::get('wrong_argument'),'reload','error');
            }
            $update = 0;
            $chain_id = intval($_POST['chain_id']);
            $_info = $model_chain->getChainInfo(array('chain_id' => $chain_id,'store_id'=>$_SESSION['store_id']));
            if (!empty($_info) && is_array($_info)) {
                $data = array();
                $data['chain_id'] = $chain_id;
                $data['chain_sender_state'] = $_info['is_auto_forward'] ? 20 : 10;
                $update = $model_order->editOrder($data,$condition);
            }
            if ($update) {
                $data = array();
                $data['order_id'] = $order_id;
                $data['order_sn'] = $order_info['order_sn'];
                $data['store_id'] = $_SESSION['store_id'];
                $data['chain_id'] = $chain_id;
                $data['log_msg'] = '商家分派订单给门店：'.$_info['chain_name'];
                $data['log_time'] = TIMESTAMP;
                $data['log_user'] = $_SESSION['seller_name'];
                $data['chain_sender_state'] = $_info['is_auto_forward'] ? 20 : 10;
                $model_chain_order->addChainSenderLog($data);
                showDialog('操作成功','reload','succ','CUR_DIALOG.close();');
            } else {
                showDialog('操作失败','reload','error');
            }
        }
        $chain_list = $model_chain->getChainSenderList($order_info);
        Tpl::output('chain_list',$chain_list);
        Tpl::showpage('chain_sender.detail','null_layout');
    }

    /**
     * 延迟收货
     */
    public function delay_receiveOp(){
        $order_id = intval($_GET['order_id']);
        $model_trade = Model('trade');
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['lock_state'] = 0;
        $order_info = $model_order->getOrderInfo($condition);

        //取目前系统最晚收货时间
        $delay_time = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 3600 * 24;
        if (chksubmit()) {
            $delay_date = intval($_POST['delay_date']);
            if (!in_array($delay_date,array(5,10,15))) {
                showDialog(Language::get('wrong_argument'),'','error',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
            }
            $update = $model_order->editOrder(array('delay_time'=>array('exp','delay_time+'.$delay_date*3600*24)),$condition);
            if ($update) {
                //新的最晚收货时间
                $dalay_date = date('Y-m-d H:i:s',$delay_time+$delay_date*3600*24);
                showDialog("成功将最晚收货期限延迟到了".$dalay_date.'&emsp;','','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();',4);
            } else {
                showDialog('延迟失败','','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
            }
        } else {
            $order_info['delay_time'] = $delay_time;
            Tpl::output('order_info',$order_info);
            Tpl::showpage('store_deliver.delay_receive','null_layout');
            exit();
        }
    }

    /**
     * 电子面单获取物流单号
     */
    public function get_logistic_codeOp(){
        //取订单信息
        $order_id = intval($_POST['order_id']);
        $model_express = Model('express');
        $express_info = $model_express->getExpressInfo(intval($_POST['shipping_express_id']));
        $data = $this->_initData($order_id, $express_info['e_code']);
        $result = $model_express->getEOrderByJson($data);
        $result = json_decode($result,true);
        //压缩html代码
        $string = str_replace("\r\n", '', $result['PrintTemplate']); //清除换行符
        $string = str_replace("\n", '', $string); //清除换行符
        $string = str_replace("\t", '', $string); //清除制表符

        $data = array();
        if($result['ResultCode'] == '100'){
            //写入数据库
            $update = array();
            $update['order_id'] = $order_id;
            $update['kdn_order_code'] = $result['Order']['KDNOrderCode'];
            $update['logistic_code'] = $result['Order']['LogisticCode'];
            $update['print_template'] = $string;
            $update['insert_time'] = TIMESTAMP;
            $model = Model('order_e_waybill');
            $model->updateOrder($update);

            $data['code'] = 200;
            $data['result'] = $result['Reason'];
            $data['logisticCode'] = $result['Order']['LogisticCode'];
        }else{
            $data['code'] = 400;
            $data['result'] = $result['Reason'];
        }
        echo json_encode($data);exit;
    }

    /**
     * 组装电子面单提交信息
     */
    private function _initData($order_id,$shipper_code){
        //取订单信息
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        if($order_info['order_state'] != ORDER_STATE_PAY){
            $data = array();
            $data['code'] = 400;
            $data['result'] = '已发货，获取物流单失败';
            echo json_encode($data);exit;
        }

        //取发货地址
        $model_daddress = Model('daddress');
        if ($order_info['extend_order_common']['daddress_id'] > 0 ){
            $daddress_info = $model_daddress->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
        }else{
            //取默认地址
            $daddress_info = $model_daddress->getAddressList(array('store_id'=>$_SESSION['store_id']),'*','is_default desc',1);
            $daddress_info = $daddress_info[0];
        }

        $eorder = [];
        $eorder["ShipperCode"] = $shipper_code;
        $eorder["OrderCode"] = $order_info['order_sn'];
        $eorder["PayType"] = 1;
        $eorder["ExpType"] = 1;
        $eorder["IsReturnPrintTemplate"] = "1";

        $sender = [];
        $sender["Name"] = $daddress_info['seller_name'];
        $sender["Mobile"] = $daddress_info['telphone'];
        //拆分地址
        $area_arr = explode(" ", $daddress_info['area_info']);
        $sender["ProvinceName"] = $area_arr[0];
        $sender["CityName"] = $area_arr[1];
        $sender["ExpAreaName"] = $area_arr[2];
        $sender["Address"] = $daddress_info['address'];

        $receiver = [];
        $receiver["Name"] = $order_info['extend_order_common']['reciver_name'];
        $receiver["Mobile"] = $order_info['extend_order_common']['reciver_info']['phone'];
        //拆分地址
        $area_arr = explode(" ", $order_info['extend_order_common']['reciver_info']['area']);
        $receiver["ProvinceName"] = $area_arr[0];
        $receiver["CityName"] = $area_arr[1];
        $receiver["ExpAreaName"] = $area_arr[2];
        $receiver["Address"] = $order_info['extend_order_common']['reciver_info']['street'];

        $commodity = [];
        foreach ($order_info['extend_order_goods'] as $v){
            $commodityOne = [];
            $commodityOne["GoodsName"] = $v['goods_name'];
            $commodityOne["Goodsquantity"] = $v['goods_num'];
            $commodity[] = $commodityOne;
        }

        $eorder["Sender"] = $sender;
        $eorder["Receiver"] = $receiver;
        $eorder["Commodity"] = $commodity;

        return json_encode($eorder, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 电子面单打印
     */
    public function e_waybill_printOp(){
        Language::read('member_printorder');
        $order_id = intval($_GET['order_id']);
        if($order_id <= 0) {
            showMessage(L('param_error'));
        }
        $model = Model('order_e_waybill');
        $data = $model->getOneOrder(intval($_GET['order_id']));
        if(empty($data)){
            showMessage('请首先获取物流单', '', '', 'error');
        }
        Tpl::output('print_template', $data['print_template']);
        Tpl::showpage('e_waybill.print', 'null_layout');
    }


    /**
     * 运单打印
     */
    public function waybill_printOp() {
        $order_id = intval($_GET['order_id']);
        if($order_id <= 0) {
            showMessage(L('param_error'));
        }

        $model_order = Model('order');
        $model_store_waybill = Model('store_waybill');
        $model_waybill = Model('waybill');

        $order_info = $model_order->getOrderInfo(array('order_id' => intval($_GET['order_id'])), array('order_common'));

        $store_waybill_list = $model_store_waybill->getStoreWaybillList(array('store_id' => $order_info['store_id']), 'is_default desc');

        $store_waybill_info = $this->_getCurrentWaybill($store_waybill_list, $_GET['store_waybill_id']);
        if(empty($store_waybill_info)) {
            showMessage('请首先绑定打印模板', urlShop('store_waybill', 'waybill_manage'), '', 'error');
        }

        $waybill_info = $model_waybill->getWaybillInfo(array('waybill_id' => $store_waybill_info['waybill_id']));
        if(empty($waybill_info)) {
            showMessage('请首先绑定打印模板', urlShop('store_waybill', 'waybill_manage'), '', 'error');
        }

        //根据订单内容获取打印数据
        $print_info = $model_waybill->getPrintInfoByOrderInfo($order_info);

        //整理打印模板
        $store_waybill_data = unserialize($store_waybill_info['store_waybill_data']);
        foreach ($waybill_info['waybill_data'] as $key => $value) {
            $waybill_info['waybill_data'][$key]['show'] = $store_waybill_data[$key]['show'];
            $waybill_info['waybill_data'][$key]['content'] = $print_info[$key];
        }

        //使用商家自定义的偏移尺寸
        $waybill_info['waybill_pixel_top'] = $store_waybill_info['waybill_pixel_top'];
        $waybill_info['waybill_pixel_left'] = $store_waybill_info['waybill_pixel_left'];

        Tpl::output('waybill_info', $waybill_info);
        Tpl::output('store_waybill_list', $store_waybill_list);
        Tpl::showpage('waybill.print', 'null_layout');
    }

    /**
     * 获取当前打印模板
     */
    private function _getCurrentWaybill($store_waybill_list, $store_waybill_id) {
        if(empty($store_waybill_list)) {
            return false;
        }

        $store_waybill_id = intval($store_waybill_id);

        $store_waybill_info = null;

        //如果指定模板使用指定的模板，未指定使用默认模板
        if($store_waybill_id > 0) {
            foreach ($store_waybill_list as $key => $value) {
                if($store_waybill_id == $value['store_waybill_id']) {
                    $store_waybill_info = $store_waybill_list[$key];
                    break;
                }
            }
        }

        if(empty($store_waybill_info)) {
            $store_waybill_info = $store_waybill_list[0];
        }

        return $store_waybill_info;
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key='') {
        Language::read('member_layout');
        $menu_array     = array();
        switch ($menu_type) {
            case 'deliver':
                $menu_array = array(
                array('menu_key'=>'deliverno',          'menu_name'=>Language::get('nc_member_path_deliverno'), 'menu_url'=>'index.php?act=store_deliver&op=index&state=deliverno'),
                array('menu_key'=>'delivering',         'menu_name'=>Language::get('nc_member_path_delivering'),    'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
                array('menu_key'=>'delivered',      'menu_name'=>Language::get('nc_member_path_delivered'), 'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
                );
                break;
            case 'search':
                $menu_array = array(
                1=>array('menu_key'=>'nodeliver',           'menu_name'=>Language::get('nc_member_path_deliverno'), 'menu_url'=>'index.php?act=store_deliver&op=index&state=nodeliver'),
                2=>array('menu_key'=>'delivering',          'menu_name'=>Language::get('nc_member_path_delivering'),    'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
                3=>array('menu_key'=>'delivered',       'menu_name'=>Language::get('nc_member_path_delivered'), 'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
                4=>array('menu_key'=>'search',      'menu_name'=>Language::get('nc_member_path_deliver_info'),  'menu_url'=>'###'),
                );
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
