<?php
/**
 * 电子面单
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
class order_e_waybillModel extends Model {

    /**
     * 通过订单id获取电子面单的内容
     *
     * @param int $order_id 订单ID
     * @return array 数组类型的返回结果
     */
    public function getOneOrder($order_id){
        if (intval($order_id) > 0){
            $param = array();
            $param['table'] = 'order_e_waybill';
            $param['field'] = 'order_id';
            $param['value'] = intval($order_id);
            $result = $this->getRow1($param);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 新增
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addOrder($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $result = $this->insert1('order_e_waybill',$tmp);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 更新信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function updateOrder($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $data = $this->getOneOrder($param['order_id']);
            if(empty($data)){
                $result = $this->addOrder($param);
                return $result;
            }
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $where = " order_id = '". $param['order_id'] ."'";
            $result = $this->update1('order_e_waybill',$tmp,$where);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 删除
     *
     * @param int $id 记录ID
     * @return array $rs_row 返回数组形式的查询结果
     */
    public function delOrder($id){
        if (intval($id) > 0){
            $where = " order_id = '". intval($id) ."'";
            $result = $this->delete1('order_e_waybill',$where);
            return $result;
        }else {
            return false;
        }
    }
}
