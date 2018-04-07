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
class dhomeControl extends mobileHomeControl{

    public function chain_swipeOp(){
        $model_setting = Model('setting');
        $chain_flash_info = $model_setting->getRowSetting('mb_chain_flash');
        $flash_info = array();
        if($chain_flash_info && strlen($chain_flash_info['value']) > 0){
            $flash_info =  unserialize($chain_flash_info['value']);
        }
        $swipe_list = array();
        foreach ((array)$flash_info as $val){
            $tmp_arr = array();
            $tmp_arr = $val;
            $tmp_arr['image'] = UPLOAD_SITE_URL . DS . ATTACH_MOBILE . DS . 'ad' . DS .$val['image'];
            $swipe_list[] = $tmp_arr;
        }
        output_data(array('swipe_list'=>$swipe_list));
    }

    public function chain_listOp(){
        $lng = floatval($_GET['chain_lng']);
        $lat = floatval($_GET['chain_lat']);

        $district_id = intval($_GET['district_id']);
        if($district_id <= 0){
            output_error('参数错误');
        }
        //获取门店及
        $model_chain = Model('chain');
        list($chain_list,$page_count) = $model_chain->getChainListByLocation($lng, $lat, $district_id, $this->page);
        output_data(array('chain_list'=>$chain_list),mobile_page($page_count));
    }
}