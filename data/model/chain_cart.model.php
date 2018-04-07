<?php
/**
 * 门店购物车记录
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

class chain_cartModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加记录
     *
     * @param
     * @return int
     */
    public function addChainCart($cart_array) {
        $result = $this->table('chain_cart')->insert($cart_array);
        return $result;
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getChainCartInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('chain_cart')->where($condition)->order('cart_id desc')->find();
        return $result;
    }

    /**
     * 查询门店单条记录
     *
     * @param
     * @return array
     */
    public function getChainInfoByID($chain_id, $field = '') {
        if (empty($field)) $field = 'chain_id,chain_name,start_amount_price,transport_freight,transport_rule,transport_distance,transport_areas,chain_lat,chain_lng,area_id_2,area_id,area_info';
        $condition = array();
        $condition['chain_id'] = $chain_id;
        $condition['chain_state'] = 1;
        $condition['is_transport'] = 1;
        if (C('chain_allow') != 1) $condition['is_own'] = 1;//如果后台门店开关关闭时只查自营
        $result = $this->table('chain')->where($condition)->field($field)->find();
        return $result;
    }

    /**
     * 查询商品单条记录
     *
     * @param
     * @return array
     */
    public function getStockInfo($condition) {
        $result = $this->table('chain_stock')->where($condition)->find();
        return $result;
    }

    /**
     * 查询商品记录
     *
     * @param
     * @return array
     */
    public function getStockByKey($condition, $key = 'goods_id') {
        $result = $this->table('chain_stock')->where($condition)->key($key)->select();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getChainCartList($condition = array(), $page = '', $limit = '', $order = 'add_time desc') {
        $result = $this->table('chain_cart')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getChainCartByKey($condition, $key = 'goods_id') {
        $result = $this->table('chain_cart')->where($condition)->key($key)->select();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getChainCartByMember($member_id,$chain_info) {
        $chain_id = $chain_info['chain_id'];
        $condition = array();
        $condition['chain_cart.member_id'] = $member_id;
        $condition['chain_cart.chain_id'] = $chain_id;
        $condition['chain_stock.chain_id'] = $chain_id;
        $field = 'chain_cart.*,chain_stock.goods_commonid,chain_stock.stock,chain_stock.chain_price';
        $list = $this->table('chain_cart,chain_stock')->join('left')->on('chain_cart.goods_id=chain_stock.goods_id')->where($condition)->field($field)->select();
        $goods_list = array();
        $invalid_list = array();
        $selected_count = 0;
        $selected_total = 0;
        $cart_count = 0;
        if (!empty($list) && is_array($list)) {
            foreach ($list as $k => $v) {
                if ($v['stock']) {
                    if ($v['goods_num'] > $v['stock']) $v['goods_num'] = $v['stock'];
                    if ($v['goods_selected']) {
                        $selected_count += $v['goods_num'];
                        $selected_total += ncPriceFormat($v['goods_num']*$v['chain_price']);
                    }
                } else {
                    $v['goods_selected'] = 0;
                }
                $cart_count += $v['goods_num'];
                $v['goods_price'] = $v['chain_price'];
                $v['goods_image_url'] = thumb($v, 60);
                $v['image_240_url'] = thumb($v, 240);
                $v['image_360_url'] = thumb($v, 360);
                if ($v['stock']) {
                    $goods_list[] = $v;
                } else {
                    $invalid_list[] = $v;
                }
            }
        }
        $cart_info = array();
        $cart_info['goods_list'] = array_merge($goods_list, $invalid_list);
        $cart_info['selected_count'] = $selected_count;
        $cart_info['selected_total'] = ncPriceFormat($selected_total);
        $cart_info['cart_count'] = $cart_count;
        $cart_info['start_amount_price'] = $chain_info['start_amount_price'];
        return $cart_info;
    }
    
    /**
     * 取得记录数量
     *
     * @param
     * @return int
     */
    public function getChainCartCount($condition) {
        return $this->table('chain_cart')->where($condition)->count();
    }

    /**
     * 更改信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editChainCart($data,$condition) {
        return $this->table('chain_cart')->where($condition)->update($data);
    }

    /**
     * 删除
     *
     * @param unknown_type $condition
     */
    public function delChainCart($condition = array()) {
        if (empty($condition)) {//删除15天前的未登录状态的数据
            $condition['member_id'] = array('like', 'u%');
            $condition['add_time'] = array('lt',TIMESTAMP-60*60*24*15);
        }
        return $this->table('chain_cart')->where($condition)->delete();
    }
}
