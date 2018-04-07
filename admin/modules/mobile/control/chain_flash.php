<?php
/**
 * 门店轮播
 *
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
class chain_flashControl extends SystemControl{

    public function __construct(){
        parent::__construct();
    }

    public function indexOp(){
        $this->edit_flashOp();
    }

    public function edit_flashOp(){
        $model_setting = Model('setting');
        $chain_flash_info = $model_setting->getRowSetting('mb_chain_flash');
        $flash_info = array();
        if($chain_flash_info && strlen($chain_flash_info['value']) > 0){
            $flash_info =  unserialize($chain_flash_info['value']);
        }

        Tpl::output('flash_info',$flash_info);
        Tpl::showpage('chain_flash');
    }

    public function save_flashOp(){
        if($_POST){
            $chain_flash_info = serialize($_POST['item_data']);
            $update_array = array();
            $update_array['mb_chain_flash'] = $chain_flash_info;
            $model_setting = Model('setting');
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                showDialog('操作成功');
            } else {
                showDialog('操作失败');
            }
        }
    }

    /**
     * 图片上传
     */
    public function flash_image_uploadOp() {
        $data = array();
        if(!empty($_FILES['flash_image']['name'])) {
            $upload = new UploadFile();
            $upload->set('default_dir', ATTACH_MOBILE . DS . 'ad');
            $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));

            $result = $upload->upfile('flash_image');
            if(!$result) {
                $data['error'] = $upload->error;
            }
            $data['image_name'] = $upload->file_name;
            $data['image_url'] =  UPLOAD_SITE_URL . DS . ATTACH_MOBILE . DS . 'ad' . DS . $upload->file_name;
        }
        echo json_encode($data);
    }
}