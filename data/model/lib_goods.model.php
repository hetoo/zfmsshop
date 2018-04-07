<?php
/**
 * 商品库
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

class lib_goodsModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加记录
     *
     * @param
     * @return int
     */
    public function addGoods($goods_array) {
        $goods_array['mobile_body'] = $this->_getMobileBody($goods_array['mobile_body'],$goods_array['is_open_m_body']);
        //去掉敏感词过滤开关
        unset($goods_array['is_open_m_body']);
        $goods_id = $this->table('lib_goods')->insert($goods_array);
        return $goods_id;
    }

    /**
     * 修改记录
     *
     * @param
     * @return bool
     */
    public function editGoods($condition, $data) {
        if (empty($condition)) {
            return false;
        }
        if (is_array($data)) {
            $data['mobile_body'] = $this->_getMobileBody($data['mobile_body'],$data['is_open_m_body']);
            //去掉敏感词过滤开关
            unset($data['is_open_m_body']);
            $result = $this->table('lib_goods')->where($condition)->update($data);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getGoodsInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('lib_goods')->where($condition)->order('goods_id desc')->find();
        return $result;
    }

    /**
     * 删除记录
     *
     * @param
     * @return bool
     */
    public function delGoods($condition) {
        if (empty($condition)) {
            return false;
        } else {
            $result = $this->table('lib_goods')->where($condition)->delete();
            return $result;
        }
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getGoodsList($condition = array(), $page = '', $limit = '', $order = 'goods_id desc') {
        $result = $this->table('lib_goods')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }
    /**
     * 序列化保存手机端商品描述数据
     */
    private function _getMobileBody($mobile_body,$is_open=0) {
        if ($mobile_body != '') {
            $mobile_body = str_replace('&quot;', '"', $mobile_body);
            $mobile_body = json_decode($mobile_body, true);
            //过滤敏感词
            if($is_open == 1){
                $data = array();
                foreach ($mobile_body as $v){
                    if($v['type'] == 'text'){
                        $v['value'] = sensitiveWordFilter($v['value']);
                    }
                    $data[] = $v;
                }
                $mobile_body = $data;
            }
            if (!empty($mobile_body)) {
                return serialize($mobile_body);
            }
        }
        return '';
    }
}
