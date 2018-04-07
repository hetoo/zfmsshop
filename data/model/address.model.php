<?php
/**
 * 我的地址
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
class addressModel extends Model {

    public function __construct() {
        parent::__construct('address');
    }

    /**
     * 取得买家默认收货地址
     *
     * @param array $condition
     */
    public function getDefaultAddressInfo($condition = array(), $order = 'is_default desc,address_id desc') {
        return $this->getAddressInfo($condition, $order);
    }

    /**
     * 取得单条地址信息
     * @param array $condition
     * @param string $order
     */
    public function getAddressInfo($condition, $order = '') {
        $addr_info = $this->where($condition)->order($order)->find();
        if (empty($addr_info)) return array();
        $addr_info['type'] = '';
        if ($addr_info['dlyp_id']) {
            $model_chain = Model('chain');
            $dlyp_info = $model_chain->getDeliveryOpenInfo(array('chain_id' => $addr_info['dlyp_id']));
            if (!empty($dlyp_info)) {
                $addr_info['dlyp_mobile'] = $dlyp_info['chain_phone'];
                $addr_info['dlyp_telephony'] = $dlyp_info['chain_phone'];
                $addr_info['dlyp_address_name'] = $dlyp_info['chain_name'];
                $addr_info['dlyp_area_info'] = $dlyp_info['area_info'];
                $addr_info['dlyp_address'] = $dlyp_info['chain_address'];
                $addr_info['area_id'] = $dlyp_info['area_id_3'];
                $addr_info['area_info'] = $dlyp_info['area_info'];
                $addr_info['chain_price'] = ncPriceFormat($dlyp_info['collection_price']);
                $addr_info['address'] = $dlyp_info['chain_name'].'（'.$dlyp_info['chain_address'].'，代收费用：'.ncPriceFormat($dlyp_info['collection_price']).'）'
                . '，电话：'.$dlyp_info['chain_phone'];
                $addr_info['type'] = '[门店代收]';
            }
        }
        return $addr_info;
    }

    /**
     * 读取地址列表
     *
     * @param
     * @return array 数组格式的返回结果
     */
    public function getAddressList($condition, $order = 'address_id desc'){
        $address_list = $this->where($condition)->order($order)->select();
        if (empty($address_list)) return array();
        if (is_array($address_list)) {
            $dlyp_ids = array();$dlyp_new_list = array();
            foreach ($address_list as $k => $v) {
                if ($v['dlyp_id']) {
                    $dlyp_ids[] = $v['dlyp_id'];
                }
            }
            if (!empty($dlyp_ids)) {
                $model_chain = Model('chain');
                $condition = array();
                $condition['chain_id'] = array('in',$dlyp_ids);
                $dlyp_list = $model_chain->getDeliveryOpenList($condition);
                foreach ($dlyp_list as $k => $v) {
                    $dlyp_new_list[$v['chain_id']]= $v;
                }
            }
            if (!empty($dlyp_new_list)) {
                foreach ($address_list as $k => $v) {
                    if (!$v['dlyp_id']) continue;
                    $dlyp_info = $dlyp_new_list[$v['dlyp_id']];
                    $address_list[$k]['area_info'] = $dlyp_info['area_info'];
                    $address_list[$k]['chain_price'] = ncPriceFormat($dlyp_info['collection_price']);
                    $address_list[$k]['address'] = $dlyp_info['chain_name'].'（'.$dlyp_info['chain_address'].'，代收费用：'.ncPriceFormat($dlyp_info['collection_price']).'）'
                        . '，电话：'.$dlyp_info['chain_phone'];
                    $address_list[$k]['type'] = '[门店代收]';
                }
            }
        }
        return $address_list;
    }

    /**
     * 取数量
     * @param unknown $condition
     */
    public function getAddressCount($condition = array()) {
        return $this->where($condition)->count();
    }

    /**
     * 构造检索条件
     *
     * @param array $condition 检索条件
     * @return string 数组形式的返回结果
     */
    private function _condition($condition){
        $condition_str = '';

        if ($condition['member_id'] != ''){
            $condition_str .= " member_id = '". intval($condition['member_id']) ."'";
        }

        return $condition_str;
    }

    /**
     * 新增地址
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addAddress($param){
        return $this->insert($param);
    }

    /**
     * 取单个地址
     *
     * @param int $area_id 地址ID
     * @return array 数组类型的返回结果
     */
    public function getOneAddress($id){
        if (intval($id) > 0){
            $param = array();
            $param['table'] = 'address';
            $param['field'] = 'address_id';
            $param['value'] = intval($id);
            $result = $this->getRow1($param);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 更新地址信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editAddress($update, $condition){
        return $this->where($condition)->update($update);
    }
    /**
     * 验证地址是否属于当前用户
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function checkAddress($member_id,$address_id) {
        /**
         * 验证地址是否属于当前用户
         */
        $check_array = self::getOneAddress($address_id);
        if ($check_array['member_id'] == $member_id){
            unset($check_array);
            return true;
        }
        unset($check_array);
        return false;
    }
    /**
     * 删除地址
     *
     * @param int $id 记录ID
     * @return bool 布尔类型的返回结果
     */
    public function delAddress($condition){
        return $this->where($condition)->delete();
    }
}
