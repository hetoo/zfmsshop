<?php
/**
 * 门店订单结算记录
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class chain_billModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加记录
     *
     * @param
     * @return int
     */
    public function addChainBill($log_array) {
        $ob_id = $this->table('chain_order_bill')->insert($log_array);
        return $ob_id;
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getChainBillInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('chain_order_bill')->where($condition)->order('ob_id desc')->find();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getChainBillList($condition = array(), $page = '', $limit = '', $order = 'ob_id desc') {
        $result = $this->table('chain_order_bill')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }
    
    /**
     * 取得记录数量
     *
     * @param
     * @return int
     */
    public function getChainBillCount($condition) {
        return $this->table('chain_order_bill')->where($condition)->count();
    }

    public function editChainBill($data, $condition = array()) {
        return $this->table('chain_order_bill')->where($condition)->update($data);
    }
    
    /**
     * 生成结算记录
     */
    public function createChainBill() {
        $count = $this->table('chain')->count();
        $step_num = 100;
        for ($i = 0; $i <= $count; $i = $i + $step_num){
            //每次取出100个门店信息
            $_list = $this->table('chain')->limit("{$i},{$step_num}")->select();
            if (is_array($_list) && $_list) {
                foreach ($_list as $_info) {
                    $start_time = $this->_get_start_date($_info['chain_id']);
                    if ($start_time !== 0) {
                        $this->_create_bill($start_time, $_info);
                    }
                }
            }
        }
    }
    
    /**
     * 取得结算开始时间
     */
    private function _get_start_date($chain_id) {
        $model_order = Model('order');
        $bill_info = $this->getChainBillInfo(array('ob_chain_id'=>$chain_id));
        $start_unixtime = 0;
        if ($bill_info['ob_end_date']){
            $start_unixtime = $bill_info['ob_end_date']+1;
        } else {
            $condition = array();
            $condition['order_state'] = ORDER_STATE_SUCCESS;
            $condition['chain_id'] = $chain_id;
            $order_info = $model_order->getOrderInfo($condition,array(),'min(finnshed_time) as stime');
            if ($order_info['stime']) {
                $start_unixtime = $order_info['stime'];
                $start_unixtime = strtotime(date('Y-m-d 00:00:00', $start_unixtime));
            }
        }
        return $start_unixtime;
    }
    
    /**
     * 结算周期为X天结算
     */
    private function _create_bill($start_unixtime,$chain_info) {
        $i = $chain_info['chain_cycle']-1;
        $start_unixtime = strtotime(date('Y-m-d 00:00:00', $start_unixtime));
        $current_time = strtotime(date('Y-m-d 00:00:00',TIMESTAMP));
        while (($time = strtotime('+'.$i.' day',$start_unixtime)) < $current_time) {
            $first_day_unixtime = strtotime(date('Y-m-d 00:00:00', $start_unixtime));    //开始那天0时unix时间戳
            $last_day_unixtime = strtotime(date('Y-m-d 23:59:59', $time)); //结束那天最后一秒时unix时间戳
            try {
                $this->beginTransaction();
                $data = array();
                $data['ob_create_date'] = TIMESTAMP;
                $data['ob_start_date'] = $first_day_unixtime;
                $data['ob_end_date'] = $last_day_unixtime;
                $data['ob_store_id'] = $chain_info['store_id'];
                $data['ob_chain_id'] = $chain_info['chain_id'];
                $data['ob_chain_name'] = $chain_info['chain_name'];
                $ob_id = $this->addChainBill($data);
                if ($ob_id) {
                    $data['ob_id'] = $ob_id;
                    $this->_calc_order_bill($data);
                }
                $this->commit();
            } catch (Exception $e) {
                $this->rollback();
            }
            $start_unixtime = strtotime(date('Y-m-d 00:00:00', $last_day_unixtime+86400));
        }
    }
    
    /**
     * 计算金额
     */
    private function _calc_order_bill($data_bill){
        $model_order = Model('order');
        $order_condition = array();
        $order_condition['order_state'] = ORDER_STATE_SUCCESS;
        $order_condition['chain_id'] = $data_bill['ob_chain_id'];
        $order_condition['finnshed_time'] = array('between',"{$data_bill['ob_start_date']},{$data_bill['ob_end_date']}");
        $update = array();
        //订单金额
        $fields = 'sum(order_amount) as order_amount,sum(rpt_amount) as rpt_amount,sum(shipping_fee) as shipping_amount';
        $order_info =  $model_order->getOrderInfo($order_condition,array(),$fields);
        $update['ob_order_totals'] = floatval($order_info['order_amount']);
        //红包
        $update['ob_rpt_amount'] = floatval($order_info['rpt_amount']);
        //运费
        $update['ob_shipping_totals'] = floatval($order_info['shipping_amount']);
        //佣金金额
        $order_info =  $model_order->getOrderInfo($order_condition,array(),'count(DISTINCT order_id) as count');
        $order_count = $order_info['count'];
        $commis_rate_totals_array = array();
        //分批计算佣金，最后取总和
        for ($i = 0; $i <= $order_count; $i = $i + 300){
            $order_list = $model_order->getOrderList($order_condition,'','order_id','',"{$i},300");
            $order_id_array = array();
            foreach ($order_list as $order_info) {
                $order_id_array[] = $order_info['order_id'];
            }
            if (!empty($order_id_array)){
                $order_goods_condition = array();
                $order_goods_condition['order_id'] = array('in',$order_id_array);
                $field = 'SUM(ROUND(goods_pay_price*commis_rate/100,2)) as commis_amount';
                $order_goods_info = $model_order->getOrderGoodsInfo($order_goods_condition,$field);
                $commis_rate_totals_array[] = $order_goods_info['commis_amount'];
            }else{
                $commis_rate_totals_array[] = 0;
            }
        }
        $update['ob_commis_totals'] = floatval(array_sum($commis_rate_totals_array));
        //订单线下支付金额
        $fields = 'sum(order_amount) as order_amount';
        $order_condition['payment_code'] = array('in',array('offline','chain'));
        $order_info =  $model_order->getOrderInfo($order_condition,array(),$fields);
        $update['ob_offline_totals'] = floatval($order_info['order_amount']);
        //退款总额
        $model_refund = Model('refund_return');
        $refund_condition = array();
        $refund_condition['seller_state'] = 2;
        $refund_condition['chain_id'] = $data_bill['ob_chain_id'];
        $refund_condition['goods_id'] = array('gt',0);
        $refund_condition['admin_time'] = array('gt',0);
        $refund_condition['finnshed_time'] = array(array('egt',$data_bill['ob_start_date']),array('elt',$data_bill['ob_end_date']),'and');
        $refund_info = $model_refund->getRefundReturnInfo($refund_condition,'sum(refund_amount) as refund_goods_amount,sum(rpt_amount) as rpt_amount,sum(ROUND(refund_amount*commis_rate/100,2)) as commis_amount');
        $update['ob_order_return_totals'] = floatval($refund_info['refund_goods_amount']);
        //全部退款时的红包
        $update['ob_rf_rpt_amount'] = floatval($refund_info['rpt_amount']);
        //退款佣金
        $update['ob_commis_return_totals'] = floatval($refund_info['commis_amount']);
        //本期应结
        $update['ob_result_totals'] = $update['ob_order_totals'] + $update['ob_rpt_amount'] - $update['ob_commis_totals'] - $update['ob_offline_totals'] - $update['ob_order_return_totals'] - $update['ob_rf_rpt_amount']+ $update['ob_commis_return_totals'];
        return $this->editChainBill($update,array('ob_id'=>$data_bill['ob_id']));
    }
}
