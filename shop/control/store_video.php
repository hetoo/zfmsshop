<?php

/**
 * 视频空间操作
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class store_videoControl extends BaseSellerControl {

    public function indexOp() {
        $this->album_video_listOp();
        exit;
    }

    public function __construct() {
        parent::__construct();
        $this->album = Model('video_album');
    }

    /**
     * 视频列表
     */
    public function album_video_listOp() {
        /**
         * 验证是否存在默认视频
         */
        $return = $this->album->checkAlbum(array('video_album_class.store_id' => $_SESSION['store_id'], 'is_default' => '1'));
        if (!$return) {
            $album_arr = array();
            $album_arr['video_class_name'] = '默认媒体库';
            $album_arr['store_id'] = $_SESSION['store_id'];
            $album_arr['video_class_des'] = '';
            $album_arr['video_class_sort'] = '255';
            $album_arr['upload_time'] = time();
            $album_arr['is_default'] = '1';
            $this->album->addClass($album_arr);
        }

        /**
         * 分页类
         */
        $page = new Page();
        $page->setEachNum(15);
        $page->setStyle('admin');

        /**
         * 实例化视频类
         */
        $param = array();
        $param['video_album.store_id'] = $_SESSION['store_id'];
        if ($_GET['sort'] != '') {
            switch ($_GET['sort']) {
                case '0':
                    $param['order'] = 'upload_time desc';
                    break;
                case '1':
                    $param['order'] = 'upload_time asc';
                    break;
                case '2':
                    $param['order'] = 'video_size desc';
                    break;
                case '3':
                    $param['order'] = 'video_size asc';
                    break;
                case '4':
                    $param['order'] = 'video_name desc';
                    break;
                case '5':
                    $param['order'] = 'video_name asc';
                    break;
            }
        }
        $video_list = $this->album->getVideoList($param, $page);
        Tpl::output('video_list', $video_list);
        Tpl::output('show_page', $page->show());

        
        /**
         * 视频信息
         */
        $param = array();
        $param['field'] = array('video_class_id', 'store_id');
        $param['value'] = array(intval($_GET['id']), $_SESSION['store_id']);
        $class_info = $this->album->getOneClass($param);
        Tpl::output('class_info', $class_info);

        Tpl::output('PHPSESSID', session_id());
        self::profile_menu('album_video', 'video_list');
        Tpl::showpage('store_video_album.video_list');
    }

    /**
     * 查看视频
     */
    public function album_video_showOp(){
        $video_id = $_GET['video_id'];
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['video_id'] = $video_id;
        $video_info = $this->album->getOne($condition);
        $video_info['goods_video'] = goodsVideoPath($video_info['video_cover'] , $_SESSION['store_id']);
        Tpl::output('video_info' , $video_info);
        Tpl::showpage('store_video_album.video_show' , 'null_layout');
    }

    /**
     * 视频删除
     */
    public function album_video_delOp() {
        if (empty($_POST['video_id'])) {
            showDialog('参数错误');
        }

        if (!empty($_POST['video_id']) && is_array($_POST['video_id'])) {
            $video_id = "'" . implode("','", $_POST['video_id']) . "'";
        } else {
            $video_id = intval($_POST['video_id']);
        }

        $return = $this->album->checkAlbum(array('video_album.store_id' => $_SESSION['store_id'], 'in_video_id' => $video_id));
        if (!$return) {
            showDialog('视频删除失败');
        }

        //删除视频
        $return = $this->album->delVideo($video_id, $_SESSION['store_id']);
        if ($return) {
            showDialog('视频删除成功', 'reload', 'succ');
        } else {
            showDialog('视频删除失败');
        }
    }

    /**
     * 视频列表，外部调用
     */
    public function video_listOp(){

        /**
         * 分页类
         */
        $page   = new Page();
        if(in_array($_GET['item'] , array('goods_video'))) {
            $page->setEachNum(12);
        } else {
            $page->setEachNum(14);
        }
        $page->setStyle('admin');

        /**
         * 实例化视频空间类
         */
        $model_album = Model('video_album');
        /**
         * 视频列表
         */
        $param = array();
        $param['video_album.store_id']    = $_SESSION['store_id'];
        if(!empty($_GET) && $_GET['id'] != '0'){
            $param['video_class_id'] = intval($_GET['id']);
            /**
             * 分类列表
             */
            $cparam = array();
            $cparam['field']        = array('video_class_id','store_id');
            $cparam['value']        = array(intval($_GET['id']),$_SESSION['store_id']);
            $cinfo          = $model_album->getOneClass($cparam);
            Tpl::output('class_name',$cinfo['aclass_name']);
        }
        $video_list = $model_album->getVideoList($param,$page);
        Tpl::output('video_list',$video_list);
        Tpl::output('show_page',$page->show());
        /**
         * 分类列表
         */
        $param = array();
        $param['video_album_class.store_id'] = $_SESSION['store_id'];
        $class_info         = $model_album->getClassList($param);
        Tpl::output('class_list',$class_info);

        switch($_GET['item']) {
            case 'goods':
                Tpl::showpage('store_goods_add.step2_master_video','null_layout');
                break;
        }
    }

    
    

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type, $menu_key = '') {
        $menu_array = array();
        switch ($menu_type) {
            case 'album_video':
                $menu_array = array(
                    3 => array('menu_key' => 'video_list', 'menu_name' => '我的视频', 'menu_url' => 'index.php?act=store_video&op=album_video_list&id=' . intval($_GET['id']))
                );
                break;
        }
        if (C('oss.open')) {
            unset($menu_array[2]);
        }
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }

    

    

}
