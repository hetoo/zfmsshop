<?php
/**
 * 手机端 - 资讯
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class mb_newsControl{

    /**
     * 验证商品是否重复
     */
    public function check_nameOp() {
        $condition = array();
        $condition['video_identity_type'] = 1;
        $condition['news_name'] = $_GET['news_name'];
        $condition['video_id'] = array('neq',intval($_GET['video_id']));
        $model_video = Model('mb_video');
        $news = $model_video->getMbVideoInfo($condition);
        if (empty($news)) {
            echo 'true';exit;
        } else {
            echo 'false';exit;
        }
    }

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
     * 手机端上传图片
     */
    public function mobile_image_uploadOp() {
        $logic_goods = Logic('goods');
        $result =  $logic_goods->uploadGoodsImage($_POST['name'],0,0);
        if(!$result['state']) {
            echo json_encode(array('error' => $result['msg']));die;
        }
        echo json_encode($result['data']);die;

    }
    /**
     * 手机端上传视频
     */
    public function mobile_video_uploadOp() {
        $logic_goods = Logic('goods');

        $result =  $logic_goods->uploadGoodsVideo($_POST['name'], 0, 0, 10240);

        if(!$result['state']) {
            echo json_encode(array('error' => $result['msg']));die;
        }

        echo json_encode($result['data']);die;
    }
    /**
     * 图片列表
     */
    public function pic_listOp(){

        /**
         * 分页类
         */
        $page   = new Page();
        $page->setEachNum(7);
        $page->setStyle('admin');
        /**
         * 实例化相册类
         */
        $model_album = Model('album');
        /**
         * 图片列表
         */
        $param = array();
        $param['album_pic.store_id']    = '0';
        $pic_list = $model_album->getPicList($param,$page);
        Tpl::output('pic_list',$pic_list);
        Tpl::output('show_page',$page->show());
        switch($_GET['item']) {
            case 'mobile':
                Tpl::output('type', $_GET['type']);
                Tpl::showpage('mb_news.mobile_image', 'null_layout');
                break;
        }
    }

    /**
     * 视频列表
     */
    public function video_listOp(){

        /**
         * 分页类
         */
        $page   = new Page();
        $page->setEachNum(7);
        $page->setStyle('admin');
        /**
         * 实例化专辑类
         */
        $model_video_album = Model('video_album');
        /**
         * 视频列表
         */
        $param = array();
        $param['album_video.store_id']    = '0';
        $video_list = $model_video_album->getVideoList($param,$page);
        Tpl::output('video_list',$video_list);
        Tpl::output('show_page',$page->show());
        switch($_GET['item']) {
            case 'mobile':
                Tpl::output('type', $_GET['type']);
                Tpl::showpage('mb_news.mobile_video', 'null_layout');
                break;
        }
    }
}
