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
class flashControl extends BaseHomeControl{

    /**
     * 闪购首页
     */
    public function indexOp() {
        $model_flash = Model('p_flash');
        $pic_list = array();
        $code_info = $model_flash->getRecommendPic();
        if(!empty($code_info['code_info'])) {
            $pic_list = unserialize($code_info['code_info']);
        }
        Tpl::output('pic_list',$pic_list);
        $time_0 = strtotime(date("Y-m-d"));//当天零点
        $time_from = $time_0-60*60*24;
        $condition = array();
        $condition['flash_state'] = 1;
        $condition['start_time'] = array('time',array($time_from,TIMESTAMP));
        $condition['end_time'] = array('gt',TIMESTAMP);
        $start_list = $model_flash->getFlashList($condition, null, 'is_recommend desc, start_time desc', '*',6);//1天内开始的活动
        Tpl::output('start_list',$start_list);
        $condition = array();
        $condition['flash_state'] = 1;
        $condition['start_time'] = array('lt',$time_from);
        $condition['end_time'] = array('gt',TIMESTAMP);
        $start_list = $model_flash->getFlashList($condition, null, 'is_recommend desc, start_time desc', '*',7);//1天前开始的活动
        Tpl::output('start_list_brand',$start_list);
        
        $condition = array();
        $condition['flash_state'] = 1;
        $condition['start_time'] = array('lt',TIMESTAMP);
        $condition['end_time'] = array('gt',TIMESTAMP);
        $start_list = $model_flash->getFlashList($condition, null, 'start_time asc', '*',20);//已开始的活动
        Tpl::output('start_list_0',$start_list);
        $model_flash_goods = Model('p_flash_goods');
        $goods_list = $model_flash_goods->getFlashGoodsCommendList(10);//推荐商品
        Tpl::output('goods_list',$goods_list);
        
        $time_from = $time_0+60*60*24;
        $condition = array();
        $condition['flash_state'] = 1;
        $condition['start_time'] = array('gt',$time_from);
        $condition['end_time'] = array('gt',$time_from);
        $start_list = $model_flash->getFlashList($condition, null, 'flash_id desc', '*',9);//1天后开始的活动
        Tpl::output('start_list_1',$start_list);
        
        $time_from = $time_0+60*60*24*2;
        $condition = array();
        $condition['flash_state'] = 1;
        $condition['start_time'] = array('gt',$time_from);
        $condition['end_time'] = array('gt',$time_from);
        $start_list = $model_flash->getFlashList($condition, null, 'flash_id desc', '*',9);//2天后开始的活动
        Tpl::output('start_list_2',$start_list);
        
        $time_from = $time_0+60*60*24*3;
        $condition = array();
        $condition['flash_state'] = 1;
        $condition['start_time'] = array('gt',$time_from);
        $condition['end_time'] = array('gt',$time_from);
        $start_list = $model_flash->getFlashList($condition, null, 'flash_id desc', '*',9);//3天后开始的活动
        Tpl::output('start_list_3',$start_list);
        $time_3 = date("N",$time_from);
        $week_arr = array('1'=>'周一','2'=>'周二','3'=>'周三','4'=>'周四','5'=>'周五','6'=>'周六','7'=>'周日');
        Tpl::output('week_3',$week_arr[$time_3]);
        Tpl::showpage('flash');
    }

    /**
     * 品牌闪购
     */
    public function brandOp(){
        $flash_id = intval($_REQUEST['flash_id']);
        if($flash_id <= 0){
            showMessage('闪购品牌不存在','index.php?act=flash');
        }
        $model_flash = Model('p_flash');
        $condition = array();
        $condition['flash_id'] = $flash_id;
        $condition['flash_state'] = 1;
        $flash_info = $model_flash->getFlashInfo($condition);
        Tpl::output('flash_info',$flash_info);

        if(empty($flash_info)){
            showMessage('闪购品牌不存在','index.php?act=flash');
        }
        $model_flash_goods = Model('p_flash_goods');
        $goods_list = $model_flash_goods->getFlashGoodsExtendList($condition,8);
        Tpl::output('goods_list',$goods_list);
        Tpl::output('show_page', $model_flash_goods->showpage());
        Tpl::showpage('flash_brand');
    }
}