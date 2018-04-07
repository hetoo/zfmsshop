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
class spikeControl extends BaseHomeControl{

    /**
     * 秒杀首页
     */
    public function indexOp() {
        $model_spike = Model('p_spike');
        $pic_list = array();
        $code_info = $model_spike->getRecommendPic();
        if(!empty($code_info['code_info'])) {
            $pic_list = unserialize($code_info['code_info']);
        }
        Tpl::output('pic_list',$pic_list);
        $list = $model_spike->getRecommendList();
        Tpl::output('list',$list);
        $condition = array();
        $condition['spike_state'] = 1;
        $condition['start_time'] = array('lt',TIMESTAMP);
        $condition['end_time'] = array('gt',TIMESTAMP);
        $model_spike_goods = Model('p_spike_goods');
        $goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition,20);
        $page_count = $model_spike_goods->gettotalpage();

        Tpl::output('cur_page',$_REQUEST['cur_page']?$_REQUEST['cur_page']:1);
        Tpl::output('page_count',$page_count);
        Tpl::output('goods_list',$goods_list);
        Tpl::showpage('spike');
    }

    /**
     * 品牌秒杀
     */
    public function brandOp(){
        $spike_id = intval($_REQUEST['spike_id']);
        if($spike_id <= 0){
            showMessage('秒杀品牌不存在','index.php?act=spike');
        }
        $model_spike = Model('p_spike');
        $condition = array();
        $condition['spike_id'] = $spike_id;
        $condition['spike_state'] = 1;
        $spike_info = $model_spike->getSpikeInfo($condition);
        Tpl::output('spike_info',$spike_info);

        if(empty($spike_info)){
            showMessage('秒杀品牌不存在','index.php?act=spike');
        }
        $model_spike_goods = Model('p_spike_goods');
        $goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition,20);

        $page_count = $model_spike_goods->gettotalpage();

        Tpl::output('cur_page',$_REQUEST['cur_page']?$_REQUEST['cur_page']:1);
        Tpl::output('page_count',$page_count);
        Tpl::output('goods_list',$goods_list);

        Tpl::showpage('spike_brand');
    }

    /**
     * 秒杀列表页
     */
    public function spike_listOp(){
        $cate_id = intval($_GET['cate_id']);
        $model_spike = Model('p_spike');
        $pic_list = array();
        $code_info = $model_spike->getRecommendPic();
        if(!empty($code_info['code_info'])) {
            $pic_list = unserialize($code_info['code_info']);
        }
        Tpl::output('pic_list',$pic_list);
        $condition = array();
        if($cate_id > 0){
            $condition['gc_id_1'] = $cate_id;
        }
        $condition['spike_state'] = 1;
        $condition['start_time'] = array('lt',TIMESTAMP);
        $condition['end_time'] = array('egt',TIMESTAMP);
        $model_spike_goods = Model('p_spike_goods');
        $goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition,20);

        $page_count = $model_spike_goods->gettotalpage();

        Tpl::output('cur_page',$_REQUEST['curpage']?$_REQUEST['curpage']:1);
        Tpl::output('page_count',$page_count);
        Tpl::output('goods_list',$goods_list);

        Tpl::showpage('spike_list');
    }

    public function brand_ajaxOp(){
        $spike_id = intval($_REQUEST['spike_id']);
        $condition = array();
        $condition['spike_id'] = $spike_id;
        $condition['spike_state'] = 1;
        $model_spike_goods = Model('p_spike_goods');
        $goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition,20);
        Tpl::output('goods_list',$goods_list);
        Tpl::showpage('spike_squares_goods','null_layout');
    }

    public function list_ajaxOp(){
        $cate_id = intval($_GET['cate_id']);
        $condition = array();
        if($cate_id > 0){
            $condition['gc_id_1'] = $cate_id;
        }
        $condition['spike_state'] = 1;
        $condition['start_time'] = array('lt',TIMESTAMP);
        $condition['end_time'] = array('egt',TIMESTAMP);
        $model_spike_goods = Model('p_spike_goods');
        $goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition,20);
        Tpl::output('goods_list',$goods_list);
        Tpl::showpage('spike_squares_goods','null_layout');
    }
}