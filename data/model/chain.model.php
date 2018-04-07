<?php
/**
 * 店铺门店模型管理
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
class chainModel extends Model {
    public function __construct(){
        parent::__construct('chain');
    }

    /**
     * 门店列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getChainList($condition, $field = '*', $page = null, $order = '', $limit = '') {
        return $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 门店详细信息
     * @param array $condition
     * @return array
     */
    public function getChainInfo($condition, $field = '*') {
        return $this->field($field)->where($condition)->find();
    }

    /**
     * 门店数量
     * @param array $condition
     * @return int
     */
    public function getChainCount($condition) {
        return $this->where($condition)->count();
    }

    /**
     * 添加门店
     * @param unknown $insert
     * @return boolean
     */
    public function addChain($insert) {
        return $this->insert($insert);
    }

    /**
     * 更新门店
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editChain($update, $condition) {
        return $this->where($condition)->update($update);
    }

    /**
     * 删除门店
     * @param array $condition
     * @return boolean
     */
    public function delChain($condition) {
        $chain_list = $this->getChainInfo($condition);
        if (empty($chain_list)) {
            return true;
        }
        foreach ($chain_list as $val) {
            @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$val['store_id'].DS.$val['chain_img']);
        }
        return $this->where($condition)->delete();
    }
    /**
     * 开启中门店代收列表
     * @param unknown $condition
     * @param number $page
     * @param string $order
     */
    public function getDeliveryOpenList($condition, $page = 0, $field = '*', $order = 'chain_id desc') {
        $condition['chain_state'] = 1;
        $condition['is_collection'] = 1;
        return $this->getChainList($condition, $field, $page, $order);
    }

    /**
     * 取得开启中门店代收
     * @param unknown $condition
     * @param string $field
     */
    public function getDeliveryOpenInfo($condition, $field = '*') {
        $condition['chain_state'] = 1;
        $condition['is_collection'] = 1;
        return $this->where($condition)->field($field)->find();
    }
    /**
     * 开启中门店发货列表
     * @param unknown $condition
     * @param number $page
     * @param string $order
     */
    public function getChainSenderList($order_info) {
        $city_id = $order_info['extend_order_common']['reciver_city_id'];
        $condition = ' chain_state=1 and is_forward_order=1 ';//开启中门店发货
        $condition .= " and express_city like '%,".$city_id.",%'";
        if (!empty($order_info['extend_order_goods']) && is_array($order_info['extend_order_goods'])) {
            $condition_goods = ' and (';
            foreach ($order_info['extend_order_goods'] as $key => $value) {
                if ($key > 0) $condition_goods .= ' or ';
                $condition_goods .= '(goods_id='.$value['goods_id'].' and stock>='.$value['goods_num'].')';
            }
            $condition_goods .= ')';
        }
        $condition .= $condition_goods;
        $field = 'chain.chain_id,chain_name,is_auto_forward';
        return $this->table('chain_stock,chain')->join('inner')->on('chain_stock.chain_id=chain.chain_id')->field($field)->where($condition)
        ->group('chain_id')->order('is_auto_forward desc')->having("count('goods_id')=".count($order_info['extend_order_goods']))->limit(99)->key('chain_id')->select();
    }

    /**
     * 获取门店及区域信息
     * @param $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @return mixed
     */
    public function getChainOpenCityList($condition, $field = "*", $page = null, $order='chain_count desc,area.area_id asc'){
        return $this->table('chain,area')->join('left')->on('chain.area_id_2=area.area_id')->field($field)->where($condition)->group('area.area_id')->page($page)->order($order)->select();
    }

    /**
     * 获取当前位置可配送门店列表及热销商品
     * @param $chain_lng 经度
     * @param $chain_lat 纬度
     * @param $district_id 区编号
     * @param bool $flag 是否返回热销商品
     * @return array
     */
    public function getChainListByLocation($chain_lng, $chain_lat, $district_id, $page=null, $keyword = '', $cate_id = 0, $flag = true){
        $condition = array();
        if(!C('chain_allow')){
            $condition['is_own'] = 1;
        }
        $condition['chain_state'] = 1;
        $condition['is_transport'] = 1;
        $condition['transport'] = array('exp','(transport_rule=2 and transport_areas like "%,'.$district_id.',%") or (transport_rule=1 and transport_distance*1000>=(acos(cos((chain_lng-'.$chain_lng.')*0.01745329252)*cos((chain_lat-'.$chain_lat.')*0.01745329252))*6371004))');
        $field = "chain_id,store_id,chain_name,chain_img,acos(cos((chain_lng-$chain_lng)*0.01745329252)*cos((chain_lat-$chain_lat)*0.01745329252))*6371004 as gps,start_amount_price,transport_freight,chain_time";

        $return_data = array();
        $chain_ids = $this->_getChainIds($keyword, $cate_id);
        if(empty($chain_ids)){
            if($keyword != ''){
                $condition['chain_name'] = array('like',"%$keyword%");
            }else{
                return $return_data;
            }
        }else{
            if($keyword != ''){
                $condition['chain'] = array('exp','chain_id in ('.implode(',',$chain_ids).') or chain_name like "%'.$keyword.'%"');
            }else{
                $condition['chain_id'] = array('in',$chain_ids);
            }
        }
        $chain_list = $this->getChainList($condition, $field, $page, "gps asc,is_own desc,chain_id asc");
        $page_count = $this->gettotalpage();
        $model_stock = Model('chain_stock');
        $model_order = Model('order');
        foreach ((array)$chain_list as $val){
            $tmp_arr = array();
            $tmp_arr = $val;
            $tmp_arr['chain_img'] = getChainImage($val['chain_img'],$val['store_id']);
            $tmp_arr['gps'] = round($val['gps']);
            $tmp_arr['is_new'] = $val['chain_time'] + 30*24*3600 < time() ? 1 : 0;
            unset($tmp_arr['chain_time']);
            //获取门店订单数量
            $order_count = $model_order->getOrderCount(array('chain_id'=>$val['chain_id'],'order_type'=>5));
            $tmp_arr['order_amount'] = $order_count;

            //获取门店商品数量
            $goods_count = $model_stock->getChainGoodsCount(array('chain_id'=>$val['chain_id']));
            $tmp_arr['goods_amount'] = $goods_count;

            //获取门店商品列表
            if($flag){
                $field = "chain_stock.goods_id,chain_stock.chain_price,chain_stock.stock,goods.goods_name,goods.goods_jingle,goods.store_id,goods.goods_image";
                $goods_list = $model_stock->getChainCommentGoods($val['chain_id'], $field, $keyword, $cate_id);
                foreach ((array)$goods_list as $key=>$value){
                    $goods_list[$key]['goods_image'] = cthumb($value['goods_image'],240,$value['store_id']);
                }
                $tmp_arr['goods_list'] = $goods_list ? $goods_list : array();
            }
            $return_data[] = $tmp_arr;
        }
        return array($return_data,$page_count);
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

    //获取门店ID
    private function _getChainIds($keyword,$cate_id){
        $condition = array();
        if($cate_id > 0){
            $condition['goods.gc_id'] = array('in',$this->_getCateIds($cate_id));
        }
        if($keyword != ''){
            $condition['goods.goods_name|goods.goods_jingle'] = array('like',"%$keyword%");
            if ($_COOKIE['hisSearch'] == '') {
                $his_sh_list = array();
            } else {
                $his_sh_list = explode('~', $_COOKIE['hisSearch']);
            }
            if (strlen($keyword) <= 20 && !in_array($keyword,$his_sh_list)) {
                if (array_unshift($his_sh_list, $keyword) > 8) {
                    array_pop($his_sh_list);
                }
            }
            setcookie('hisSearch', implode('~', $his_sh_list), time()+2592000, '/', SUBDOMAIN_SUFFIX ? SUBDOMAIN_SUFFIX : '', false);
        }

        $model = Model('chain_stock');
        $goods_list = $model->getChainGoodsList($condition, 'chain_stock.chain_id', 0,'chain.chain_id asc','chain.chain_id');
        $data = array();
        foreach ($goods_list as $value){
            $data[] = $value['chain_id'];
        }
        return $data;
    }
}
