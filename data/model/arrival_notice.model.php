<?php
/**
 * 商品到货通知模型
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
class arrival_noticeModel extends Model{
    public function __construct() {
        parent::__construct('arrival_notice');
    }

    /**
     * 通知列表
     *
     *
     * @param unknown $condition
     * @param string $field
     * @param number $limit
     * @param string $order
     */
    public function getArrivalNoticeList($condition = array(), $field = '*', $limit = '', $page = null, $order = 'an_id desc') {
        return $this->where($condition)->field($field)->limit($limit)->page($page)->order($order)->select();
    }

    /**
     * 单条通知
     *
     * @param unknown $condition
     * @param string $field
     */
    public function getArrivalNoticeInfo($condition, $field = '*') {
        return $this->where($condition)->field($field)->find();
    }

    /**
     * 通知数量
     *
     * @param array $condition
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getArrivalNoticeCount($condition) {
        return $this->where($condition)->count();
    }


    /**
     * 添加通知
     * @param array $insert
     * @return int
     */
    public function addArrivalNotice($insert) {
        $insert['an_addtime'] = TIMESTAMP;
        return $this->insert($insert);
    }

    /**
     * 删除通知
     *
     * @param unknown $condition
     */
    public function delArrivalNotice($condition) {
        return $this->where($condition)->delete();
    }
}
