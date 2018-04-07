<?php
/**
 * 店铺门店库存模型
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
class chain_stockModel extends Model {
    public function __construct(){
        parent::__construct('chain_stock');
    }

    /**
     * 门店库存列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getChainStockList($condition, $field = '*', $page = 0,$order= 'goods_id asc', $group = '') {
        return $this->field($field)->where($condition)->order($order)->group($group)->page($page)->select();
    }

    /**
     * 门店库存
     * @param array $condition
     * @return array
     */
    public function getChainStockInfo($condition, $field = '*') {
        return $this->where($condition)->find();
    }

    /**
     * 添加门店库存
     * @param unknown $insert
     * @return boolean
     */
    public function addChainStock($insert) {
        $result = $this->pk(array('chain_id', 'goods_id'))->insert($insert, true);
        if ($result) {
            if (intval($insert['stock']) > 0) {
                Model('goods')->editGoodsById(array('is_chain' => '1'), $insert['goods_id']);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新门店库存
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editChainStock($update, $condition) {
        return $this->where($condition)->update($update);
    }

    /**
     * 删除门店库存
     * @param array $condition
     * @return boolean
     */
    public function delChainStock($condition) {
        return $this->where($condition)->delete();
    }

    /**
     * 获取门店商品列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function getChainGoodsList($condition = array(), $field = '*', $page = 0,$order= 'goods.goods_id desc', $group = '',$limit = false){
        return $this->table('chain,chain_stock,goods')->join('Left')->on('chain.chain_id=chain_stock.chain_id,chain_stock.goods_id=goods.goods_id')->field($field)->where($condition)->group($group)->order($order)->limit($limit)->page($page)->select();
    }

    /**
     * 获取门店商品详细信息
     * @param array $condition
     * @param string $field
     * @return mixed
     */
    public function getChainGoodsInfo($condition = array(), $field = '*'){
        return $this->table('chain_stock,goods')->join('Left')->on('chain_stock.goods_id=goods.goods_id')->field($field)->where($condition)->find();
    }

    /**
     * 获取门店商品数量
     * @param array $condition
     * @return mixed
     */
    public function getChainGoodsCount($condition = array()){
        return $this->table('chain_stock,goods')->join('Left')->on('chain_stock.goods_id=goods.goods_id')->where($condition)->count();
    }

    /**
     * 获取指定门店的20个推荐商品
     * @param $chain_id
     * @return mixed
     */
    public function getChainCommentGoods($chain_id, $field = "*", $keyword = '', $cate_id = 0){
        $condition = array();
        $condition['chain_stock.chain_id'] = $chain_id;
        $condition['goods.goods_state'] = 1;
        $condition['goods.goods_verify'] = 1;
        $condition['chain_stock.stock'] = array('gt', 0);
        if($keyword != ''){
            $condition['goods.goods_name|goods.goods_jingle'] = array('like',"%$keyword%");
        }
        if($cate_id > 0){
            $condition['goods.gc_id'] = array('in',$this->_getCateIds($cate_id));
        }
        if(!C('chain_allow')){
            $condition['chain.is_own'] = 1;
        }
        $goods_list = $this->getChainGoodsList($condition, $field, 20,"goods.goods_salenum desc,goods.goods_id desc");
        return $goods_list;
    }

    /**
     * 获取门店商品评价
     * @param array $condition
     * @param string $filed
     * @param null $page
     * @param string $order
     * @return mixed
     */
    public function getGoodsEvaluateList($condition = array(), $filed = "*", $page = null, $order = "evaluate_goods.geval_addtime desc"){
        return $this->table('orders,order_goods,evaluate_goods')->join('left')->on('orders.order_id=order_goods.order_id,order_goods.order_id=evaluate_goods.geval_orderid')->field($filed)->where($condition)->page($page)->order($order)->select();
    }

    public function getGoodsEvaluateInfo($condition = array(), $filed = "*"){
        return $this->table('orders,order_goods,evaluate_goods')->join('left')->on('orders.order_id=order_goods.order_id,order_goods.order_id=evaluate_goods.geval_orderid')->field($filed)->where($condition)->group()->find();
    }

    //获取分类及子分类ID
    private function _getCateIds($cate_id){
        $model = Model('goods_class');
        $goods_list = $model->getChildClass($cate_id);
        $return_data = array();
        foreach ((array)$goods_list as $val){
            $return_data[] = $val['gc_id'];
        }
        return $return_data;
    }
}
