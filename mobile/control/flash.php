<?php
/**
 * 闪购
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
class flashControl extends mobileHomeControl
{
    function __construct()
    {
        parent::__construct();
    }

    public function indexOp(){
        $this->flash_listOp();
    }

    /**
     * 闪购活动列表
     */
    public function flash_listOp(){
        $stat = 0;
        if($_REQUEST['state'] == 'unflash'){
            $stat = 1;
        }elseif($_REQUEST['state'] == 'oldflash'){
            $stat = 2;
        }
        $condition = $this->_arrangeFlashListCondition($stat);
        $model_flash = Model('p_flash');
        $filed = "flash_id,flash_name,flash_title,flash_explain,start_time,end_time,flash_state,flash_brand,flash_pic";
        $t_list = $model_flash->getFlashList($condition,$this->page,'',$filed);
        $page_count = $model_flash->gettotalpage();
        $flash_list = array();
        foreach($t_list as $val){
            $tmp_arr = array();
            $tmp_arr = $val;
            $tmp_arr['exp_second'] = $val['end_time'] - TIMESTAMP;
            $tmp_arr['exp_day'] = intval($tmp_arr['exp_second']/(24*3600));
            $flash_list[] = $tmp_arr;
        }

        output_data(array('flash_list' => $flash_list), mobile_page($page_count));
    }

    /**
     * 活动详情
     */
    public function flash_detialOp(){
        $flash_id = intval($_GET['flash_id']);
        if($flash_id <= 0){
            output_error('参数错误');
        }
        $model_flash = Model('p_flash');
        $condition = array();
        $condition['flash_id'] = $flash_id;
        $flash_t_info = $model_flash->getFlashInfo($condition);

        if(empty($flash_t_info)){
            output_error('活动不存在');
        }
        $flash_info = $this->_arrangeFlashData($flash_t_info);

        $model_flash_goods = Model('p_flash_goods');
        $goods_fields = "goods_id,store_id,goods_name,goods_price,flash_price,goods_image,upper_limit";
        $goods_t_list = (array)$model_flash_goods->getFlashGoodsExtendList($condition, $this->page, 'flash_goods_id desc', $goods_fields);
        $goods_list = array();
        foreach($goods_t_list as $val){
            $tmp_arr = $val;
            $tmp_arr['goods_img_url'] = cthumb($val['goods_image'], 360, $val['store_id']);
            $tmp_arr['goods_price'] = ncPriceFormat($val['goods_price']);
            $tmp_arr['flash_price'] = ncPriceFormat($val['flash_price']);
            $tmp_arr['price_pencent'] = number_format($val['flash_price']/$val['goods_price'] * 10,1);
            $goods_list[] = $tmp_arr;
        }
        $page_count = $model_flash_goods->gettotalpage();

        output_data(array('flash_info' => $flash_info, 'goods_list' => $goods_list), mobile_page($page_count));
    }


    //整理闪购列表条件
    private function _arrangeFlashListCondition($stat = 0){
        $return = array();
        $return['flash_state'] = 1;
        $return['end_time'] = array('gt',TIMESTAMP);
        if($stat == 1){ //即将开始
            $return['start_time'] = array('gt',time());
            unset($return['end_time']);
        }elseif($stat == 2) { //最后疯抢
            $d_s = date('Y-m-d',TIMESTAMP);
            $start_time = strtotime($d_s);
            $return['start_time'] = array('lt',$start_time);
        }else{ //今日上新
            $d_s = date('Y-m-d',TIMESTAMP);
            $d_e = $d_s . " 23:59:59";
            $start_time = strtotime($d_s);
            $end_time = strtotime($d_e);
            $return['start_time'] = array('time',array($start_time,$end_time));
        }
        return $return;
    }

    //整理闪购活动信息数据
    private function _arrangeFlashData($spike = array()){
        $return = array();
        if(!empty($spike)){
            $spike_state = false;
            if($spike['flash_state'] && time()>$spike['start_time'] && time()<$spike['end_time']){
                $spike_state = true;
            }
            $return['flash_id'] = $spike['flash_id'];
            $return['flash_name'] = $spike['flash_name'];
            $return['flash_title'] = $spike['flash_title'];
            $return['flash_explain'] = $spike['flash_explain'];
            $return['upper_limit'] = $spike['upper_limit'];
            $return['start_time'] = $spike['start_time'];
            $return['end_time'] = $spike['end_time'];
            $return['flash_state'] = $spike_state;
            $return['flash_brand_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$spike['flash_brand'];
            $return['flash_pic_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$spike['flash_pic'];
            $return['flash_banner_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$spike['flash_banner'];
            $return['to_time'] = $spike['start_time'] - TIMESTAMP > 0 ? $spike['start_time'] - TIMESTAMP : $spike['end_time'] - TIMESTAMP;
        }
        return $return;
    }
}