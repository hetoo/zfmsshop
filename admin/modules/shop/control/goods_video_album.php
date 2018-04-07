<?php
/**
 * 视频管理
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

class goods_video_albumControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        $this->video_album = Model("video_album");
    }

    public function indexOp() {
        $this->listOp();
    }

    /**
     * 视频列表
     */
    public function listOp(){
        Tpl::showpage('goods_video_album.index');
    }

    /**
     * 输出XML数据
     */
    public function get_xmlOp() {
        
        $model = Model();
        
        // 设置页码参数名称
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('video_class_id', 'video_class_name', 'store_id', 'store_name', 'video_count', 'video_class_des');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
                $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        //店铺列表
        $album_list = $model->table('video_album_class')->where($condition)->order($order)->page($page)->select();

        $storeid_array = array();
        $classid_array = array();
        foreach ($album_list as $val) {
            $storeid_array[] = $val['store_id'];
            $classid_array[] = $val['video_class_id'];
        }

        // 店铺名称
        $store_list = Model('store')->getStoreList(array('store_id' => array('in', $storeid_array)));
        $store_array = array();
        foreach ($store_list as $val) {
            $store_array[$val['store_id']] = $val['store_name'];
        }

        // 视频数量
        $count_list = $model->cls()->table('video_album')->field('count(*) as count, video_class_id')->where(array('video_class_id' => array('in', $classid_array)))->group('video_class_id')->select();
        $count_array = array();
        foreach ($count_list as $val) {
            $count_array[$val['video_class_id']] = $val['count'];
        }

        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        foreach ($album_list as $value) {
            $param = array();
            $operation = "<a class='btn green' href='index.php?act=goods_video_album&op=video_list&video_class_id=".$value['video_class_id']."&store_id=".$value['store_id']."'><i class='fa fa-list-alt'></i>查看</a>";
            if ($value['store_id']) $operation = "<a class='btn red' href='javascript:void(0);' onclick='fg_del(". $value['video_class_id'] .")'><i class='fa fa-trash-o'></i>删除</a><a class='btn green' href='index.php?act=goods_video_album&op=video_list&video_class_id=".$value['video_class_id']."&store_id=".$value['store_id']."'><i class='fa fa-list-alt'></i>查看</a>";
            $param['operation'] = $operation;
            $param['video_class_id'] = $value['video_class_id'];
            $param['video_class_name'] = $value['video_class_name'];
            $param['store_id'] = $value['store_id'];
            $param['store_name'] = "<a href='". urlShop('show_store', 'index', array('store_id' => $value['store_id'])) ."' target='blank'>". $store_array[$value['store_id']] . "<i class='fa fa-external-link ' title='新窗口打开'></i></a>";
            $param['video_count'] = intval($count_array[$value['video_class_id']]);
            $param['video_class_des'] = $value['video_class_des'];
            $data['list'][$value['video_class_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 删除媒体库
     */
    public function video_class_delOp(){
        $video_class_id = intval($_GET['id']);
        if (!is_numeric($video_class_id)){
            exit(json_encode(array('state'=>false,'msg'=>'参数错误')));
        }
        $model = Model();
        $video = $model->table('video_album')->field('video_cover')->where(array('video_class_id'=>$video_class_id))->select();
        if (is_array($video)){
            foreach ($video as $v) {
                $this->del_file($v['video_cover']);
            }
        }
        $model->table('video_album')->where(array('video_class_id'=>$video_class_id))->delete();
        $model->table('video_album_class')->where(array('video_class_id'=>$video_class_id))->delete();
        $this->log('删除视频集'.'[ID:'.intval($_GET['video_class_id']).']',1);
        exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
    }

    /**
     * 删除视频文件
     *
     */
    private function del_file($filename){
        //取店铺ID
        if (preg_match('/^(\d+_)/',$filename)){
            $store_id = substr($filename,0,strpos($filename,'_'));
        }else{
            $store_id = Model()->cls()->table('video_album')->getfby_video_cover($filename,'store_id');
        }
        if (C('oss.open')) {
            if ($filename != '') {
                oss::del(array(ATTACH_GOODS.DS.$store_id.DS.'goods_video'.DS.$filename));
            }
        } else {
            $path = BASE_UPLOAD_PATH.'/'.ATTACH_GOODS.'/'.$store_id.'/'.'goods_video'.'/'.$filename;

            if (is_file($fpath = str_replace('.', '.', $path))){
                @unlink($fpath);
            }
            if (is_file($path)) @unlink($path);
        }
    }

    /**
     * 视频列表
     */
    public function video_listOp(){
        $store_id = $_GET['store_id'];
        $model_store = Model('store');
        $store_info = $model_store->getStoreInfo(array('store_id' => $store_id));
        $store_name = $store_info['store_name'];
        Tpl::output('store_name' , $store_name);
        Tpl::output('video_class_id' , $_GET['video_class_id']);
        Tpl::output('store_id' , $_GET['store_id']);
        Tpl::showpage('goods_video_album.video_list');
    }

    /**
     * 视频列表
     */
    public function get_video_xmlOp(){
        $model_album = Model('video_album');
        $model_store = Model('store');

        $page = $_POST['rp'];

        $condition = array();
        $condition['video_class_id'] = $_GET['video_class_id'];
        $condition['store_id'] = $_GET['store_id'];
        $video_list = Model()->table('video_album')->where($condition)->order('upload_time desc')->page($page)->select();
        $data = array();
        $data['now_page'] = $model_album->shownowpage();
        $data['total_num'] = $model_album->gettotalnum();
        foreach ($video_list as $value) {
            $param = array();
            $operation = "<a class='btn red' href='javascript:void(0);' onclick='fg_del(". $value['video_id'] ." , ". $value['store_id'] .")'><i class='fa fa-trash-o'></i>删除</a>";
            $operation .= "<a class='btn red' href='javascript:void(0);' onclick='fg_show_video(". $value['video_id'] .")'><i class='fa fa-list-alt'></i>查看</a>";
            $param['operation'] = $operation;
            $param['video_name'] = $value['video_name'];
            $param['video_size'] = number_format($value['video_size']/1024/1024 , 2) .'MB';
            $param['updload_time'] = date('Y-m-d h:i:s' , $value['upload_time']);
            $store_info = $model_store->getStoreInfo(array('store_id' => $value['store_id']));
            $param['store_name'] = $store_info['store_name'];
            $data['list'][$value['video_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 删除一张视频及其对应记录
     *
     */
    public function del_album_videoOp(){
        $model_album = Model('video_album');
        if (empty($_GET['video_id'])) {
            echo json_encode(array('state' => false , 'msg' => '参数错误'));exit;
        }

        if (!empty($_GET['video_id']) && is_array($_GET['video_id'])) {
            $video_id = "'" . implode("','", $_GET['video_id']) . "'";
        } else {
            $video_id = intval($_GET['video_id']);
        }

        $return = $model_album->checkAlbum(array('video_album.store_id' => $_GET['store_id'], 'in_video_id' => $video_id));
        if (!$return) {
            echo json_encode(array('state' => false , 'msg' => '删除视频错误'));exit;
        }

        //删除视频
        $return = $model_album->delVideo($video_id, $_GET['store_id']);
        if($return){
            echo json_encode(array('state' => true));exit;
            $this->log('删除视频'.'[ID:'.$video_id.']成功',1);
        } else {
            echo json_encode(array('state' => false , 'msg' => '删除视频错误'));exit;
            $this->log('删除视频'.'[ID:'.$video_id.']失败',0);
        }
    }

    /**
     * 查看视频
     */
    public function show_videoOp(){
        $model_album = Model('video_album');
        $condition = array();
        $condition['video_id'] = $_GET['id'];
        $video_info = $model_album->getOne($condition);
        $video_info['goods_video'] = goodsVideoPath($video_info['video_cover'] , $video_info['store_id']);
        Tpl::output('video_info' , $video_info);
        Tpl::showpage('goods_video_album.show_video' , 'null_layout');
    }
}
