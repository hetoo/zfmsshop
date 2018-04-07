<?php
/**
 * 门店订单记录
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

class chain_orderModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加记录
     *
     * @param
     * @return int
     */
    public function addDeliveryOrder($order_array) {
        $result = $this->table('chain_reciver')->insert($order_array);
        return $result;
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getDeliveryOrderInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('chain_reciver')->where($condition)->order('order_id desc')->find();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getDeliveryOrderList($condition = array(), $page = '', $limit = '', $order = 'order_id desc') {
        $result = $this->table('chain_reciver')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }
    
    /**
     * 取得记录数量
     *
     * @param
     * @return int
     */
    public function getDeliveryOrderCount($condition) {
        return $this->table('chain_reciver')->where($condition)->count();
    }

    /**
     * 更改信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editDeliveryOrder($data,$condition) {
        return $this->table('chain_reciver')->where($condition)->update($data);
    }

    /**
     * 删除
     *
     * @param unknown_type $condition
     */
    public function delDeliveryOrder($condition) {
        return $this->table('chain_reciver')->where($condition)->delete();
    }

    /**
     * 取订单状态
     * @return multitype:string
     */
    public function getDeliveryOrderState() {
        $order_state = array(
            10 => '未到店',
            20 => '已到店',
            30 => '已提取'
        );
        return $order_state;
    }

    /**
     * 增加门店发货记录
     * @return boolean
     */
    public function addChainSenderLog($data) {
        return $this->table('chain_sender_log')->insert($data);
    }
}
