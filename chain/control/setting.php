<?php
/**
 * 门店设置
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class settingControl extends BaseChainCenterControl{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 默认门店设置首页
     */
    public function indexOp(){
        $this->settingOp();
    }

    /**
     * 门店设置
     */
    public function settingOp(){
        $model_chain = Model('chain');
        $chain_id = intval($_SESSION['chain_id']);
        $condition = array('chain_id'=>$chain_id);
        $chain_info = $model_chain->getChainInfo($condition);
        if(chksubmit()){
            /**
             * 上传图片
             */
            $upload = new UploadFile();
            if (!empty($_FILES['chain_img']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$chain_info['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_img');
                if ($result){
                    $_POST['chain_img'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            $upload = new UploadFile();
            if (!empty($_FILES['chain_banner']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$chain_info['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_banner');
                if ($result){
                    $_POST['chain_banner'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            $upload = new UploadFile();
            if (!empty($_FILES['chain_logo']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$chain_info['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_logo');
                if ($result){
                    $_POST['chain_logo'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            //删除旧图片
            if (!empty($_POST['chain_img']) && !empty($chain_info['chain_img'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$chain_info['store_id'].DS.$chain_info['chain_img']);
            }

            if (!empty($_POST['chain_banner']) && !empty($chain_info['chain_banner'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$chain_info['store_id'].DS.$chain_info['chain_banner']);
            }

            if (!empty($_POST['chain_logo']) && !empty($chain_info['chain_logo'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$chain_info['store_id'].DS.$chain_info['chain_logo']);
            }
            $update = array();
            if (!empty($_POST['chain_img'])) {
                $update['chain_img']    = $_POST['chain_img'];
                $_SESSION['chain_img'] = getChainImage($update['chain_img'], $chain_info['store_id']);
            }
            if (!empty($_POST['chain_banner'])) {
                $update['chain_banner']    = $_POST['chain_banner'];
            }
            if (!empty($_POST['chain_logo'])) {
                $update['chain_logo']    = $_POST['chain_logo'];
            }
            $update['area_id_1']    = $_POST['area_id_1'];
            $update['area_id_2']    = $_POST['area_id_2'];
            $update['area_id_3']    = $_POST['area_id_3'];
            $update['area_id_4']    = $_POST['area_id_4'];
            $update['area_id']      = $_POST['area_id'];
            $update['area_info']    = $_POST['area_info'];
            $update['chain_address']= $_POST['chain_address'];
            $update['chain_phone']  = $_POST['chain_phone'];
            $update['chain_opening_hours']  = $_POST['chain_opening_hours'];
            $update['chain_traffic_line']   = $_POST['chain_traffic_line'];

            $update['is_self_take'] = intval($_POST['is_self_take']);
            $update['is_transport'] = intval($_POST['is_transport']);
            $update['start_amount_price'] = intval($_POST['start_amount_price']);
            $update['transport_rule'] = intval($_POST['transport_rule']);
            $update['transport_distance'] = intval($_POST['transport_distance']);
            $update['transport_freight'] = intval($_POST['transport_freight']);

            $update['is_forward_order'] = intval($_POST['is_forward_order']);
            $update['is_auto_forward'] = intval($_POST['is_auto_forward']);
            $update['express_city'] = $_POST['express_city'];
            $update['express_city_name'] = $_POST['express_city_name'];
            $update['is_collection'] = intval($_POST['is_collection']);
            $update['collection_price'] = intval($_POST['collection_price']);
            if(!empty($_POST['transport_areas']))
            {
                $update['transport_areas'] = ',';
                foreach ($_POST['transport_areas'] as $v){
                    $update['transport_areas'] .= $v.',';
                }
            }

            //处理门店地址坐标信息
            if($chain_info['chain_lat'] == 0 || $chain_info['area_info'] != $update['area_info'] || $chain_info['chain_address'] != $update['chain_address']){
                $area_info = explode(" ", $update['area_info']);
                $city = $area_info[1];
                $area = $area_info[1] . $area_info[2];
                $address = $area . $update['chain_address'];
                $location = getGeoByAddress($address, $city);
                if(!empty($location)){
                    $update['chain_lat'] = $location['location']['lat'];
                    $update['chain_lng'] = $location['location']['lng'];
                }
            }
            $result = $model_chain->editChain($update, array('chain_id' => $chain_id, 'store_id' => $chain_info['store_id']));
            if ($result) {
                showDialog('编辑成功','index.php?act=setting&op=index','succ');
            } else {
                showDialog('编辑失败', 'reload');
            }
        }
        $this->_getChianTransportAreas($chain_info);
        $chain_info['transport_areas'] = strlen($chain_info['transport_areas']) > 0 ? explode(',',substr($chain_info['transport_areas'],1)): array();
        unset($chain_info['chain_pwd']);
        Tpl::output('chain_info',$chain_info);
        $areas = Model('area')->getAreas();
        Tpl::output('areas', $areas);
        Tpl::showpage('chain_setting');
    }

    //获取门店配送区域数据
    private function _getChianTransportAreas(& $chain_info){
        $chain_info['transport_area_arr'] = array();
        $model_area = Model('area');
        $area_all_info = $model_area->getAreas();
        $area_children_arr = $area_all_info['children'];
        if ($chain_info['area_id'] == $chain_info['area_id_3'] || $chain_info['area_id'] == $chain_info['area_id_4']) {
            $area_ids_3 = $area_children_arr[$chain_info['area_id_2']];
            $chain_info['transport_area_arr'] = $this->_makeAreaData($area_ids_3);
        } elseif ($chain_info['area_id'] == $chain_info['area_id_2']) {
            $area_ids_2 = $area_children_arr[$chain_info['area_id_1']];
            $chain_info['transport_area_arr'] = $this->_makeAreaData($area_ids_2);
        } else {
            $chain_info['transport_area_arr'] = $this->_makeAreaData(array($chain_info['area_id_1']));
        }
    }
    private function _makeAreaData($area_ids){
        $model_area = Model('area');
        $area_list = $model_area->getAreaNames();
        $return_data = array();
        foreach($area_ids as $v){
            $tmp_area = array();
            $tmp_area['area_id'] = $v;
            $tmp_area['area_name'] = $area_list[$v];
            $return_data[] = $tmp_area;
        }
        return $return_data;
    }
}