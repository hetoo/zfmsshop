<?php
/**
 * 手机端 - 点播
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class mb_demandControl{
    /**
     * 上传图片
     */
    public function image_uploadOp() {
        $model_video = Model('mb_video');
        $result =  $model_video->uploadImages($_POST['name'] , $_GET['upload_type']);
        if(!$result['state']) {
            echo json_encode(array('error' => $result['msg']));die;
        }
        echo json_encode($result['data']);die;
    }

    /**
     * 上传视频
     */
    public function video_uploadOp() {
        $model_video = Model('mb_video');
        $size_type = $this->_get_video_size();
        $result =  $model_video->uploadVideo($_POST['name'],$size_type[$_POST['size_type']],$_GET['upload_type']);
        if(!$result['state']) {
            echo json_encode(array('error' => $result['msg']));die;
        }
        echo json_encode($result['data']);die;
    }

    /**
     * 获取视频尺寸
     *
     * @return mixed
     */
    private function _get_video_size(){
        $size_array[1] = 5120;
        $size_array[2] = 20480;
        return $size_array;
    }
}