<?php
/**
 * 秒杀活动商品模型
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
class p_spike_goodsModel extends Model{

    const SPIKE_GOODS_STATE_CANCEL = 0;
    const SPIKE_GOODS_STATE_NORMAL = 1;

    public function __construct(){
        parent::__construct('p_spike_goods');
    }

    /**
     * 读取秒杀商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 秒杀商品列表
     *
     */
    public function getSpikeGoodsList($condition, $page=null, $order='spike_goods_id desc', $field='*', $limit = 0) {
        return $spike_goods_list = $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 读取秒杀商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 秒杀商品列表
     *
     */
    public function getSpikeGoodsExtendList($condition, $page=null, $order='spike_goods_id desc', $field='*', $limit = 0) {
        $spike_goods_list = $this->getSpikeGoodsList($condition, $page, $order, $field, $limit);
        if(!empty($spike_goods_list)) {
            for($i=0, $j=count($spike_goods_list); $i < $j; $i++) {
                $spike_goods_list[$i] = $this->getSpikeGoodsExtendInfo($spike_goods_list[$i]);
            }
        }
        return $spike_goods_list;
    }

    /**
     * 根据条件读取限制折扣商品信息
     * @param array $condition 查询条件
     * @return array 秒杀商品信息
     *
     */
    public function getSpikeGoodsInfo($condition) {
        $result = $this->where($condition)->find();
        return $result;
    }

    /**
     * 根据秒杀商品编号读取限制折扣商品信息
     * @param int $spike_goods_id
     * @return array 秒杀商品信息
     *
     */
    public function getSpikeGoodsInfoByID($spike_goods_id, $store_id = 0) {
        if(intval($spike_goods_id) <= 0) {
            return null;
        }

        $condition = array();
        $condition['spike_goods_id'] = $spike_goods_id;
        $spike_goods_info = $this->getSpikeGoodsInfo($condition);

        if($store_id > 0 && $spike_goods_info['store_id'] != $store_id) {
            return null;
        } else {
            return $spike_goods_info;
        }
    }

    /**
     * 增加秒杀商品
     * @param array $spike_goods_info
     * @return bool
     *
     */
    public function addSpikeGoods($spike_goods_info){
        $spike_goods_info['spike_state'] = self::SPIKE_GOODS_STATE_NORMAL;
        $spike_goods_id = $this->insert($spike_goods_info);

        $spike_goods_info['spike_goods_id'] = $spike_goods_id;
        $spike_goods_info = $this->getSpikeGoodsExtendInfo($spike_goods_info);
        return $spike_goods_info;
    }

    /**
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     *
     */
    public function editSpikeGoods($update, $condition){
        $result = $this->where($condition)->update($update);
        if ($result) {
            $spike_goods_list = $this->getSpikeGoodsList($condition, null, '', 'goods_id');
            if (!empty($spike_goods_list)) {
                foreach ($spike_goods_list as $val) {
                    // 插入对列 更新促销价格
                    QueueClient::push('updateGoodsPromotionPriceByGoodsId', $val['goods_id']);
                }
            }
        }
        return $result;
    }

    /**
     * 删除
     * @param array $condition
     * @return bool
     *
     */
    public function delSpikeGoods($condition){
        $spike_goods_list = $this->getSpikeGoodsList($condition, null, '', 'goods_id');
        $result = $this->where($condition)->delete();
        if ($result) {
            if (!empty($spike_goods_list)) {
                foreach ($spike_goods_list as $val) {
                    // 插入对列 更新促销价格
                    QueueClient::push('updateGoodsPromotionPriceByGoodsId', $val['goods_id']);
                }
            }
        }
        return $result;
    }

    /**
     * 获取秒杀商品扩展信息
     * @param array $spike_info
     * @return array 扩展秒杀信息
     *
     */
    public function getSpikeGoodsExtendInfo($spike_info) {
        $spike_info['goods_url'] = urlShop('goods', 'index', array('goods_id' => $spike_info['goods_id']));
        $spike_info['image_url'] = cthumb($spike_info['goods_image'], 360, $spike_info['store_id']);
        $spike_info['spike_price'] = ncPriceFormat($spike_info['spike_price']);
        if ($spike_info['spike_amount'] < 1) $spike_info['spike_amount'] = 1;
        $spike_info['spike_discount'] = number_format($spike_info['spike_price'] / $spike_info['goods_price'] * 10, 1).'折';
        return $spike_info;
    }

    /**
     * 根据商品编号查询是否有可用秒杀活动，如果有返回秒杀活动，没有返回null
     * @param int $goods_id
     * @return array $spike_info
     *
     */
    public function getSpikeGoodsInfoByGoodsID($goods_id) {
            $condition['spike_state'] = self::SPIKE_GOODS_STATE_NORMAL;
            $condition['end_time'] = array('gt', TIMESTAMP);
            $condition['goods_id'] = $goods_id;
            $spike_goods_list = $this->getSpikeGoodsExtendList($condition, null, 'start_time asc', '*', 1);
        $spike_goods_info = $spike_goods_list[0];
        if (!empty($spike_goods_info) && ($spike_goods_info['start_time'] > TIMESTAMP || $spike_goods_info['end_time'] < TIMESTAMP)) {
            $spike_goods_info = array();
        }
        return $spike_goods_info;
    }

    /**
     * 根据商品编号查询是否有可用秒杀活动，如果有返回秒杀活动，没有返回null
     * @param string $goods_string 商品编号字符串，例：'1,22,33'
     * @return array $spike_goods_list
     *
     */
    public function getSpikeGoodsListByGoodsString($goods_string) {
        $spike_goods_list = $this->_getSpikeGoodsListByGoods($goods_string);
        $spike_goods_list = array_under_reset($spike_goods_list, 'goods_id');
        return $spike_goods_list;
    }

    /**
     * 根据商品编号查询是否有可用秒杀活动，如果有返回秒杀活动，没有返回null
     * @param string $goods_id_string
     * @return array $spike_info
     *
     */
    private function _getSpikeGoodsListByGoods($goods_id_string) {
        $condition = array();
        $condition['spike_state'] = self::SPIKE_GOODS_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $condition['goods_id'] = array('in', $goods_id_string);
        $spike_goods_list = $this->getSpikeGoodsExtendList($condition, null, 'spike_goods_id desc', '*');
        return $spike_goods_list;
    }
}
