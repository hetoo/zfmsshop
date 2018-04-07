<?php
/**
 * 到家公共接口
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class dhome_searchControl extends mobileHomeControl{
    /**
     * 搜门店
     */
    public function indexOp(){
        $lng = floatval($_GET['chain_lng']);
        $lat = floatval($_GET['chain_lat']);

        $district_id = intval($_GET['district_id']);
        if($district_id <= 0){
            output_error('参数错误');
        }
        $keyword = trim($_GET['keyword']);
        $cate_id = intval($_GET['cate_id']);
        if($cate_id <= 0 && $keyword == ''){
            output_error('参数错误');
        }
        //获取门店及
        $model_chain = Model('chain');
        list($chain_list,$page_count) = $model_chain->getChainListByLocation($lng, $lat, $district_id, $this->page, $keyword, $cate_id);
        output_data(array('chain_list'=>$chain_list),mobile_page($page_count));
    }

    /**
     * 热门搜索
     */
    public function get_hotOp(){
        $words = C('chain_hot_keyword');
        $split_1 = explode(',',$words);
        $hot_words = array();
        foreach ((array)$split_1 as $val){
            $tmp = explode('，',$val);
            if(is_array($tmp) && !empty($tmp)){
                $hot_words = array_merge($hot_words,$tmp);
            }else {echo $val;
                array_push($hot_words,$val);
            }
        }
        if (!$hot_words || !is_array($hot_words)) {
            $hot_words = array();
        }
        if ($_COOKIE['hisSearch'] != '') {
            $his_search_list = explode('~', $_COOKIE['hisSearch']);
        }
        if (!$his_search_list || !is_array($his_search_list)) {
            $his_search_list = array();
        }
        output_data(array('list'=>$hot_words,'his_list'=>$his_search_list));
    }

    /**
     * 店内搜索
     */
    public function goods_listOp(){
        $chain_id = intval($_GET['chain_id']);
        if($chain_id <= 0){
            output_error('参数错误');
        }
        $keyword = trim($_GET['keyword']);
        if($keyword == ''){
            output_error('参数错误');
        }
        $model_stock = Model('chain_stock');
        $field = "chain_stock.goods_id,chain_stock.chain_price,chain_stock.stock,goods.goods_name,goods.goods_jingle,goods.store_id,goods.goods_image";
        $goods_list = $model_stock->getChainCommentGoods($chain_id, $field, $keyword);
        $page_count = $model_stock->gettotalpage();
        foreach ((array)$goods_list as $key=>$value){
            $goods_list[$key]['goods_image'] = cthumb($value['goods_image'],240,$value['store_id']);
        }
        output_data(array('goods_list'=>$goods_list),mobile_page($page_count));
    }

}