<?php
/**
 * 秒杀
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class spikeControl extends mobileHomeControl{
    function __construct()
    {
        parent::__construct();
    }

    public function indexOp(){
        $this->spike_listOp();
    }

    /**
     * 秒杀列表页
     */
    public function spike_listOp(){
        $stat = 0;
        if($_REQUEST['state'] == 'unspike'){
            $stat = 1;
        }
        $condition = $this->_arrangeSpikeListCondition($stat);

        // 获取秒杀活动
        $model_spike = Model('p_spike');
        $field = 'spike_id,spike_name,spike_title,start_time,end_time,spike_state,spike_common_bg';
        $spike_list = $model_spike->table('p_spike')->field($field)->where($condition)->page(5)->order('spike_id desc')->select();
        $page_count = $model_spike->gettotalpage();
        $spike_ids = array();
        if(!empty($spike_list)) {
            for($i =0, $j = count($spike_list); $i < $j; $i++) {
                $spike_list[$i] = $model_spike->getSpikeExtendInfo($spike_list[$i]);
                array_push($spike_ids,$spike_list[$i]['spike_id']);
                $spike_list[$i]['spike_common_bg'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$spike_list[$i]['spike_common_bg'];
            }
        }

        //获取秒杀活动商品
        $goods_condition = array();
        $goods_condition['spike_id'] = array('in',implode(',',$spike_ids));
        $field_goods = "spike_id,goods_id,store_id,goods_name,goods_price,spike_price,goods_image,upper_limit,spike_amount,had_spiked_count";
        $recommend_t_goods = (array)Model()->table('p_spike_goods')->field($field_goods)->where($goods_condition)->order('spike_recommend desc,spike_goods_id desc')->select();
        $recommend_goods = array();
        foreach($recommend_t_goods as $key => $val){
            $tmp_arr = $val;
            $tmp_arr['goods_img_url'] = cthumb($val['goods_image'], 360, $val['store_id']);
            $tmp_arr['goods_price'] = ncPriceFormat($val['goods_price']);
            $tmp_arr['spike_percent'] = intval($val['had_spiked_count'] / $val['spike_amount'] * 100)> 100 ? 100 : intval($val['had_spiked_count'] / $val['spike_amount'] * 100);
            $recommend_goods[$val['spike_id']][] = $tmp_arr;
        }

        output_data(array('spike_list' => $spike_list,'recommend_goods'=>$recommend_goods), mobile_page($page_count));
    }

    /**
     * 品牌秒杀
     */
    public function spike_brandsOp(){
        $brand_id = intval($_GET['brand_id']);
        if($brand_id <= 0){
            output_error('参数错误');
        }
        $model_spike = Model('p_spike');
        $condition = array();
        $condition['spike_id'] = $brand_id;
        $spike_t_info = $model_spike->getSpikeInfo($condition);
        if(empty($spike_t_info)){
            output_error('活动不存在');
        }
        $spike_info = $this->_arrangeSpikeData($spike_t_info);

        $model_spike_goods = Model('p_spike_goods');
        $goods_fields = "goods_id,store_id,goods_name,goods_price,spike_price,goods_image,upper_limit,spike_amount,had_spiked_count";
        $goods_t_list = (array)$model_spike_goods->getSpikeGoodsExtendList($condition, $this->page, '', $goods_fields);
        $goods_list = array();
        foreach($goods_t_list as $val){
            $tmp_arr = $val;
            $tmp_arr['goods_img_url'] = cthumb($val['goods_image'], 360, $val['store_id']);
            $tmp_arr['goods_price'] = ncPriceFormat($val['goods_price']);
            $tmp_arr['spike_percent'] = intval($val['had_spiked_count'] / $val['spike_amount'] * 100)> 100 ? 100 : intval($val['had_spiked_count'] / $val['spike_amount'] * 100);
            $goods_list[] = $tmp_arr;
        }
        $page_count = $model_spike_goods->gettotalpage();

        output_data(array('spike_info' => $spike_info, 'goods_list' => $goods_list), mobile_page($page_count));
    }

    //整理秒杀列表条件
    private function _arrangeSpikeListCondition($stat = 0){
        $return = array();
        $return['spike_state'] = 1;
        $return['start_time'] = array('lt',time());
        $return['end_time'] = array('gt',time());
        if($stat == 1){
            $return['start_time'] = array('gt',time());
            unset($return['end_time']);
        }
        return $return;
    }


    //整理秒杀活动信息数据
    private function _arrangeSpikeData($spike = array()){
        $return = array();
        if(!empty($spike)){
            $spike_state = false;
            if($spike['spike_state'] && time()>$spike['start_time'] && time()<$spike['end_time']){
                $spike_state = true;
            }
            $return['spike_id'] = $spike['spike_id'];
            $return['spike_name'] = $spike['spike_name'];
            $return['spike_title'] = $spike['spike_title'];
            $return['spike_explain'] = $spike['spike_explain'];
            $return['upper_limit'] = $spike['upper_limit'];
            $return['start_time'] = $spike['start_time'];
            $return['end_time'] = $spike['end_time'];
            $return['spike_state'] = $spike_state;
            $return['spike_banner'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$spike['spike_banner'];
            $return['to_time'] = $spike['start_time'] - TIMESTAMP > 0 ? $spike['start_time'] - TIMESTAMP : $spike['end_time'] - TIMESTAMP;
        }
        return $return;
    }
}