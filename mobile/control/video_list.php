<?php
/**
 * 视频列表
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invideo_infoid!');
class video_listControl extends mobileHomeControl{

    public function __construct(){
        parent::__construct();
    }

    public function indexOp(){
        $this->listOp();
    }

    // 视频点播列表
    public function listOp(){
        $model_store = Model('store');
        $model_focus = Model('mb_video_focus');
        $model_video = Model('mb_video');
        //广告图
        $focus_list_arr = array();
        $focus_list = $model_focus->getMbFocusList(array(),'','focus_sort desc');
        if(!empty($focus_list) && is_array($focus_list)){
            foreach($focus_list as $key => $v){
                $focus_list_arr[$key]['focus_id'] = $v['focus_id'];
                $focus_list_arr[$key]['focus_image_url'] = getMbFocusImageUrl($v['focus_image']);
                $focus_list_arr[$key]['focus_data'] = $v['focus_url'];
                $focus_list_arr[$key]['focus_image'] = $v['focus_image'];
                $focus_list_arr[$key]['focus_type'] = $v['focus_type'];
            }
        }
        //按分类筛选
        $condition = array();
        if($_GET['cate_id']){
            $condition['cate_id'] = $_GET['cate_id'];
        }
        //视频点播列表
        $order = 'video_identity_type desc , add_time desc';
        $video_list = $model_video->getMbVideoList($condition , $this->page,$order);
        //整理数据
        foreach($video_list as $key => $video_info){
            $video_list[$key]['video_id'] = $video_info['video_id'];
            $video_list[$key]['add_time'] = date('Y-m-d',$video_info['add_time']);
            $video_list[$key]['identity'] = $video_info['video_identity'];
            $video_list[$key]['page_view'] = intval($video_info['page_view']);
            $video_list[$key]['store_id'] = $video_info['store_id'];
            $store_info = $model_store->getStoreInfoByID($video_info['store_id']);
            $video_list[$key]['store_name'] = $store_info['store_name'];
            $video_list[$key]['store_logo'] = getStoreLogo($store_info['store_label'], 'store_logo');
            $video_list[$key]['store_avatar'] = getStoreLogo($store_info['store_avatar'],'store_avatar');
            $video_list[$key]['promote_video'] = (empty($video_info['promote_video'])) ? '' : getMbDemandVideoUrl($video_info['promote_video'] , 'promotefile');
            $video_list[$key]['demand_video'] = (empty($video_info['demand_video'])) ? '' : getMbDemandVideoUrl($video_info['demand_video'],'demandfile');
            $video_list[$key]['promote_text'] = $video_info['promote_text'];
            $video_list[$key]['promote_image'] = (empty($video_info['promote_image'])) ? '' : getMbDemandImageUrl($video_info['promote_image']);
            //推荐商品
            $model_goods = Model('goods');
            $recommend_goods_data = array();
            if(!empty($video_info['recommend_goods'])){
                $recommend_goods_arr = array();
                $commonid_array = array();
                $recommend_goods = unserialize($video_info['recommend_goods']);
                if($video_info['video_identity_type'] == 2){//点播
                    foreach($recommend_goods as $goods_commonid => $common_info){
                        $commonid_array[] = $goods_commonid;
                    }
                } elseif ($video_info['video_identity_type'] == 1) {//资讯
                    $commonid_array = $recommend_goods['goods_commonid'];
                }
                //获取sku的id
                $where['goods_commonid'] = array('in' , $commonid_array);
                $common_list = $model_goods->getGoodsCommonList($where);
                if(!empty($common_list) && is_array($common_list)){
                    foreach($common_list as $k => $goods_info){
                        $goods_datas = $model_goods->getGoodsInfo(array('goods_commonid' => $goods_info['goods_commonid']) , 'goods_id');
                        $recommend_goods_arr['goods_id'] = $goods_datas['goods_id'];
                        $recommend_goods_arr['goods_name'] = $goods_info['goods_name'];
                        $recommend_goods_arr['goods_price'] = $goods_info['goods_price'];
                        $recommend_goods_arr['goods_commonid'] = $goods_commonid;
                        $recommend_goods_arr['goods_image_path'] = thumb($goods_info , 240);
                        $recommend_goods_data[] = $recommend_goods_arr;
                    }
                }
            }
            $video_list[$key]['recommend_goods'] = $recommend_goods_data;
            // 如果已登录 判断该店铺是否已被收藏
            if ($memberId = $this->getMemberIdIfExists()) {
                $c = (int) Model('favorites')->getStoreFavoritesCountByStoreId($video_info['store_id'], $memberId);
                $video_list[$key]['is_favorate'] = $c > 0;
            } else {
                $video_list[$key]['is_favorate'] = false;
            }
            $video_list[$key]['news_name'] = $video_info['news_name'];
            $video_list[$key]['news_pic'] = getMbNewsImageUrl($video_info['news_image']);
        }
        $page_count = $model_video->gettotalpage();
        output_data(array('focus_list' => $focus_list_arr ,'lists' => $video_list) , mobile_page($page_count));
    }

    //视频分类
    public function cate_listOp(){
        $cate_list = Model('mb_video_cate')->getVideoCategoryList(array());
        foreach($cate_list as $keyey => $video_infoue){
            $cate_list[$keyey]['cate_image'] = UPLOAD_SITE_URL.DS.ATTACH_MOBILE.'/video_cate/'.$video_infoue['cate_image'];
        }
        output_data(array('cate_list' => $cate_list));
    }

}