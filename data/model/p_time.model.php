<?php
/**
 * 促销时间模型
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
use Shopnc\Tpl;
class p_timeModel extends Model{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 读取列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 列表
     *
     */
    public function getList($condition, $page=null, $order='', $field='*', $limit=0) {
        $list = $this->table('p_time')->field($field)->where($condition)->limit($limit)->page($page)->order($order)->select();
        return $list;
    }

    /**
     * 根据条件读取信息
     * @param array $condition 查询条件
     * @return array 信息
     *
     */
    public function getInfo($condition) {
        $_info = $this->table('p_time')->where($condition)->find();
        return $_info;
    }

    /*
     * 增加
     * @param array $param
     * @return bool
     *
     */
    public function add($param){
        return $this->table('p_time')->insert($param);
    }

    /*
     * 更新
     * @return bool
     *
     */
    public function edit($condition, $data){
        return $this->table('p_time')->where($condition)->update($data);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     *
     */
    public function del($condition){
        if (empty($condition)) return false;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function getGoodsInfo($goods_id) {
        $condition = array();
        $condition['goods_id'] = $goods_id;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $_info = $this->table('p_time')->where($condition)->find();
        $promotion_type = $_info['promotion_type'];//类型:2限时折扣,3秒杀,4闪购
        $goods_info = array();
        switch ($promotion_type) {
            case '2':
                $goods_info = $this->table('p_xianshi_goods')->where(array('goods_id'=> $goods_id,'xianshi_id'=> $_info['promotion_id']))->find();
                if (!empty($goods_info)) {
                    $goods_info = Model('p_xianshi_goods')->getXianshiGoodsExtendInfo($goods_info);
                    $goods_info['promotion_price'] = $goods_info['xianshi_price'];
                }
                break;
            case '3':
                $goods_info = $this->table('p_spike_goods')->where(array('goods_id'=> $goods_id,'spike_id'=> $_info['promotion_id']))->find();
                if (!empty($goods_info)) {
                    $goods_info = Model('p_spike_goods')->getSpikeGoodsExtendInfo($goods_info);
                    $goods_info['promotion_price'] = $goods_info['spike_price'];
                }
                break;
            case '4':
                $goods_info = $this->table('p_flash_goods')->where(array('goods_id'=> $goods_id,'flash_id'=> $_info['promotion_id']))->find();
                if (!empty($goods_info)) {
                    $goods_info = Model('p_flash_goods')->getFlashGoodsExtendInfo($goods_info);
                    $goods_info['promotion_price'] = $goods_info['flash_price'];
                }
                break;
            default:
                $goods_info = array();
                break;
        }
        if (!empty($goods_info)) {
            $goods_info['promotion_id'] = $_info['promotion_id'];
            $goods_info['promotion_type'] = $promotion_type;
        }
        return $goods_info;
    }
    public function addXianshi($info){
        $param = array();
        $param['start_time'] = $info['start_time'];
        $param['end_time'] = $info['end_time'];
        $param['store_id'] = $info['store_id'];
        $param['goods_id'] = $info['goods_id'];
        $param['promotion_id'] = $info['xianshi_id'];
        $param['promotion_price'] = $info['xianshi_price'];
        $param['promotion_type'] = 2;
        $log_id = $this->table('p_time')->insert($param);
        return $log_id;
    }
    public function delXianshi($xianshi_id) {
        $condition = array();
        $condition['promotion_id'] = $xianshi_id;
        $condition['promotion_type'] = 2;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function delXianshiGoods($info) {
        $condition = array();
        $condition['promotion_id'] = $info['xianshi_id'];
        $condition['goods_id'] = $info['goods_id'];
        $condition['promotion_type'] = 2;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function editXianshiGoods($info){
        $condition = array();
        $condition['promotion_id'] = $info['xianshi_id'];
        $condition['goods_id'] = $info['goods_id'];
        $condition['promotion_type'] = 2;
        $update = array();
        $update['promotion_price'] = $info['xianshi_price'];
        $goods_id = $this->table('p_time')->where($condition)->update($update);
        return $goods_id;
    }
    public function addSpike($info){
        $param = array();
        $param['start_time'] = $info['start_time'];
        $param['end_time'] = $info['end_time'];
        $param['store_id'] = $info['store_id'];
        $param['goods_id'] = $info['goods_id'];
        $param['promotion_id'] = $info['spike_id'];
        $param['promotion_price'] = $info['spike_price'];
        $param['promotion_type'] = 3;
        $log_id = $this->table('p_time')->insert($param);
        return $log_id;
    }
    public function delSpike($spike_id) {
        $condition = array();
        $condition['promotion_id'] = $spike_id;
        $condition['promotion_type'] = 3;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function delSpikeGoods($info) {
        $condition = array();
        $condition['promotion_id'] = $info['spike_id'];
        $condition['goods_id'] = $info['goods_id'];
        $condition['promotion_type'] = 3;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function editSpikeGoods($info){
        $condition = array();
        $condition['promotion_id'] = $info['spike_id'];
        $condition['goods_id'] = $info['goods_id'];
        $condition['promotion_type'] = 3;
        $update = array();
        $update['promotion_price'] = $info['spike_price'];
        $goods_id = $this->table('p_time')->where($condition)->update($update);
        return $goods_id;
    }
    public function addFlash($info){
        $param = array();
        $param['start_time'] = $info['start_time'];
        $param['end_time'] = $info['end_time'];
        $param['store_id'] = $info['store_id'];
        $param['goods_id'] = $info['goods_id'];
        $param['promotion_id'] = $info['flash_id'];
        $param['promotion_price'] = $info['flash_price'];
        $param['promotion_type'] = 4;
        $log_id = $this->table('p_time')->insert($param);
        return $log_id;
    }
    public function delFlash($flash_id) {
        $condition = array();
        $condition['promotion_id'] = $flash_id;
        $condition['promotion_type'] = 4;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function delFlashGoods($info) {
        $condition = array();
        $condition['promotion_id'] = $info['flash_id'];
        $condition['goods_id'] = $info['goods_id'];
        $condition['promotion_type'] = 4;
        return $this->table('p_time')->where($condition)->delete();
    }
    public function editFlashGoods($info){
        $condition = array();
        $condition['promotion_id'] = $info['flash_id'];
        $condition['goods_id'] = $info['goods_id'];
        $condition['promotion_type'] = 4;
        $update = array();
        $update['promotion_price'] = $info['flash_price'];
        $goods_id = $this->table('p_time')->where($condition)->update($update);
        return $goods_id;
    }
    public function getPromotionList($goods_list, $time) {
        $id_list = array_under_reset($goods_list, 'goods_id');
        $condition = array();
        $condition['goods_id'] = array('in',array_keys($id_list));
        $condition['end_time'] = array('gt', $time);
        $list = $this->table('p_time')->where($condition)->key('goods_id')->select();
        return $list;
    }
}
