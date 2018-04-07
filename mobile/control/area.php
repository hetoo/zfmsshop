<?php
/**
 * 地区
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
class areaControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->area_listOp();
    }

    /**
     * 地区列表
     */
    public function area_listOp() {
        $area_id = intval($_GET['area_id']);

        $model_area = Model('area');

        $condition = array();
        if($area_id > 0) {
            $condition['area_parent_id'] = $area_id;
        } else {
            $condition['area_deep'] = 1;
        }
        $area_list = $model_area->getAreaList($condition, 'area_id,area_name');
        output_data(array('area_list' => $area_list));
    }

    /**
     * 根据坐标获取当前区域信息
     */
    public function get_area_infoOp(){
        $lat = floatval($_GET['lat']);
        $lng = floatval($_GET['lng']);

        $res = geoToAddr($lat,$lng);
        $model_area = Model('area');
        $field = 'area_id,area_name,area_parent_id,area_deep';
        $addressComponent = $res['addressComponent'];
        $city_info = (array)$model_area->getAreaInfo(array('area_name'=>$addressComponent['city']),$field);
        $district_info = (array)$model_area->getAreaInfo(array('area_name'=>array('like',$addressComponent['district']),'area_parent_id'=>$city_info['area_id']),$field);
        output_data(array('city_info'=>$city_info,'district_info'=>$district_info,'area_text'=>$res['business']));
    }

    /**
     * 根据地址获取当前区域信息
     */
    public function get_curr_infoOp(){
        $address = trim($_GET['address']);
        $city_id = intval($_GET['city_id']);
        $area_name = trim($_GET['area_name']);
        if($address == ''){
            output_error('参数错误');
        }
        if($area_name == ''){
            output_error('参数错误');
        }
        if($city_id <= 0){
            output_error('参数错误');
        }

        $return_data = array();
        //获取坐标
        $location = getGeoByAddress($address);
        if(!empty($location)){
            $return_data['chain_lat'] = $location['location']['lat'];
            $return_data['chain_lng'] = $location['location']['lng'];
        }else{
            $return_data['chain_lat'] = 0;
            $return_data['chain_lng'] = 0;
        }

        //获取区级信息
        $model_area = Model('area');
        $field = 'area_id,area_name,area_parent_id,area_deep';
        $district_info = (array)$model_area->getAreaInfo(array('area_name'=>array('like',$area_name."%"),'area_parent_id'=>$city_id),$field);
        $return_data['area_name'] = $district_info['area_name'];
        $return_data['area_id'] = $district_info['area_id'];
        output_data($return_data);
    }

    /**
     * 获取开通门店城市列表
     */
    public function get_city_listOp(){
        $is_hot = intval($_GET['hot']);
        $model_chain = Model('chain');
        $fields = "area.area_id,area.area_name,area.area_parent_id,area.area_deep,count(chain.chain_id) as chain_count";
        $condition = array();
        $condition['chain.chain_state'] = 1;
        $condition['chain.is_transport'] = 1;
        $page = $this->page;
        if($is_hot > 0){
            $page = 12;
        }
        $city = $model_chain->getChainOpenCityList($condition, $fields, $page);
        $extend_data = array();
        if($is_hot <= 0){
            $page_count = $model_chain->gettotalpage();
            $extend_data = mobile_page($page_count);
        }
        output_data(array('city_list'=>$city),$extend_data);
    }

    /**
     * 地址检索
     */
    public function suggest_listOp(){
        $city_name = trim($_GET['city_name']);
        $keyword = trim($_GET['place_name']);
        if($city_name == '' || $keyword == ''){
            $err = array();
            $err['status'] = -1;
            $err['message'] = "参数错误";
            echo json_encode($err);exit;
        }
        $url = "http://api.map.baidu.com/place/v2/suggestion?query={$keyword}&region={$city_name}&city_limit=true&output=json&ak=".C('baidu_map_key');
        echo http_get($url);exit;
    }
}
