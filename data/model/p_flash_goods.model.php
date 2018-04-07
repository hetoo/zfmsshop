<?php
/**
 * 闪购活动商品模型
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
class p_flash_goodsModel extends Model{

    const FLASH_GOODS_STATE_CANCEL = 0;
    const FLASH_GOODS_STATE_NORMAL = 1;

    public function __construct(){
        parent::__construct('p_flash_goods');
    }

    /**
     * 读取闪购商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 闪购商品列表
     *
     */
    public function getFlashGoodsList($condition, $page=null, $order='flash_goods_id desc', $field='*', $limit = 0) {
        return $flash_goods_list = $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 读取闪购商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 闪购商品列表
     *
     */
    public function getFlashGoodsExtendList($condition, $page=null, $order='flash_goods_id desc', $field='*', $limit = 0) {
        $flash_goods_list = $this->getFlashGoodsList($condition, $page, $order, $field, $limit);
        if(!empty($flash_goods_list)) {
            for($i=0, $j=count($flash_goods_list); $i < $j; $i++) {
                $flash_goods_list[$i] = $this->getFlashGoodsExtendInfo($flash_goods_list[$i]);
            }
        }
        return $flash_goods_list;
    }

    /**
     * 根据条件读取限制折扣商品信息
     * @param array $condition 查询条件
     * @return array 闪购商品信息
     *
     */
    public function getFlashGoodsInfo($condition) {
        $result = $this->where($condition)->find();
        return $result;
    }

    /**
     * 根据闪购商品编号读取限制折扣商品信息
     * @param int $flash_goods_id
     * @return array 闪购商品信息
     *
     */
    public function getFlashGoodsInfoByID($flash_goods_id, $store_id = 0) {
        if(intval($flash_goods_id) <= 0) {
            return null;
        }

        $condition = array();
        $condition['flash_goods_id'] = $flash_goods_id;
        $flash_goods_info = $this->getFlashGoodsInfo($condition);

        if($store_id > 0 && $flash_goods_info['store_id'] != $store_id) {
            return null;
        } else {
            return $flash_goods_info;
        }
    }

    /**
     * 增加闪购商品
     * @param array $flash_goods_info
     * @return bool
     *
     */
    public function addFlashGoods($flash_goods_info){
        $flash_goods_info['flash_state'] = self::FLASH_GOODS_STATE_NORMAL;
        $flash_goods_id = $this->insert($flash_goods_info);

        $flash_goods_info['flash_goods_id'] = $flash_goods_id;
        $flash_goods_info = $this->getFlashGoodsExtendInfo($flash_goods_info);
        return $flash_goods_info;
    }

    /**
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     *
     */
    public function editFlashGoods($update, $condition){
        $result = $this->where($condition)->update($update);
        if ($result) {
            $flash_goods_list = $this->getFlashGoodsList($condition, null, '', 'goods_id');
            if (!empty($flash_goods_list)) {
                foreach ($flash_goods_list as $val) {
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
    public function delFlashGoods($condition){
        $flash_goods_list = $this->getFlashGoodsList($condition, null, '', 'goods_id');
        $result = $this->where($condition)->delete();
        if ($result) {
            if (!empty($flash_goods_list)) {
                foreach ($flash_goods_list as $val) {
                    // 插入对列 更新促销价格
                    QueueClient::push('updateGoodsPromotionPriceByGoodsId', $val['goods_id']);
                }
            }
        }
        return $result;
    }

    /**
     * 获取闪购商品扩展信息
     * @param array $flash_info
     * @return array 扩展闪购信息
     *
     */
    public function getFlashGoodsExtendInfo($flash_info) {
        $flash_info['goods_url'] = urlShop('goods', 'index', array('goods_id' => $flash_info['goods_id']));
        $flash_info['image_url'] = cthumb($flash_info['goods_image'], 360, $flash_info['store_id']);
        $flash_info['flash_price'] = ncPriceFormat($flash_info['flash_price']);
        if ($flash_info['flash_amount'] < 1) $flash_info['flash_amount'] = 1;
        $flash_info['flash_discount'] = number_format($flash_info['flash_price'] / $flash_info['goods_price'] * 10, 1).'折';
        return $flash_info;
    }

    /**
     * 获取推荐闪购商品
     * @param int $count 推荐数量
     * @return array 推荐限时活动列表
     *
     */
    public function getFlashGoodsCommendList($count = 10) {
        $condition = array();
        $condition['flash_state'] = self::FLASH_GOODS_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $flash_list = array();
        $flash_list = $this->getFlashGoodsExtendList($condition, null, 'flash_recommend desc,buy_count desc', '*', $count);
        return $flash_list;
    }

    /**
     * 根据商品编号查询是否有可用闪购活动，如果有返回闪购活动，没有返回null
     * @param int $goods_id
     * @return array $flash_info
     *
     */
    public function getFlashGoodsInfoByGoodsID($goods_id) {
            $condition['flash_state'] = self::FLASH_GOODS_STATE_NORMAL;
            $condition['end_time'] = array('gt', TIMESTAMP);
            $condition['goods_id'] = $goods_id;
            $flash_goods_list = $this->getFlashGoodsExtendList($condition, null, 'start_time asc', '*', 1);
        $flash_goods_info = $flash_goods_list[0];
        if (!empty($flash_goods_info) && ($flash_goods_info['start_time'] > TIMESTAMP || $flash_goods_info['end_time'] < TIMESTAMP)) {
            $flash_goods_info = array();
        }
        return $flash_goods_info;
    }

    /**
     * 根据商品编号查询是否有可用闪购活动，如果有返回闪购活动，没有返回null
     * @param string $goods_string 商品编号字符串，例：'1,22,33'
     * @return array $flash_goods_list
     *
     */
    public function getFlashGoodsListByGoodsString($goods_string) {
        $flash_goods_list = $this->_getFlashGoodsListByGoods($goods_string);
        $flash_goods_list = array_under_reset($flash_goods_list, 'goods_id');
        return $flash_goods_list;
    }

    /**
     * 根据商品编号查询是否有可用闪购活动，如果有返回闪购活动，没有返回null
     * @param string $goods_id_string
     * @return array $flash_info
     *
     */
    private function _getFlashGoodsListByGoods($goods_id_string) {
        $condition = array();
        $condition['flash_state'] = self::FLASH_GOODS_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $condition['goods_id'] = array('in', $goods_id_string);
        $flash_goods_list = $this->getFlashGoodsExtendList($condition, null, 'flash_goods_id desc', '*');
        return $flash_goods_list;
    }
}
