<?php
/**
 * 手机视频模型
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class mb_videoModel extends Model{

    public function __construct() {
        parent::__construct('mb_video');
    }

    /**
     * 读取视频列表
     * @param array $condition
     *
     */
    public function getMbVideoList($condition, $page='', $order='video_id desc', $field='*',$limit='') {
        $list = $this->table('mb_video')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $list;
    }

    /**
     * 读取视频详情
     * @param array $condition
     *
     */
    public function getMbVideoDetails($condition, $page='', $order='video_id desc', $field='*',$limit='') {
        $list = $this->table('mb_video')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        $live_list = array();
        if(!empty($list)){
            foreach($list as $k => $info){
                $list['video_id'] = $info['video_id'];
                $list['page_view'] = $info['page_view'];
                $live_list[] = $list;
            }
        }
        return $live_list;
    }

    /**
     * 统计视频列表
     * @param array $condition
     *
     */
    public function getMbVideoCount($condition) {
        $count = $this->table('mb_video')->where($condition)->count();
        return $count;
    }

    /**
 * 读取视频列表
 * @param array $condition
 *
 */
    public function getMbVideoInfoByID($video_id) {
        $video_id = intval($video_id);
        if($video_id <= 0) {
            return false;
        }

        $condition = array();
        $condition['video_id'] = $video_id;
        $info = $this->table('mb_video')->where($condition)->find();
        return $info;
    }

    /**
     * 读取视频信息
     * @param array $condition
     *
     */
    public function getMbVideoInfo($condition,$fields = "*") {
        $info = $this->table('mb_video')->field($fields)->where($condition)->find();
        return $info;
    }

    /*
     * 增加视频
     * @param array $param
     * @return bool
     *
     */
    public function addMbVideo($param){
        return $this->table('mb_video')->insert($param);
    }

    /*
     * 更新视频
     * @param array $update
     * @param array $video_id 视频ID
     * @return bool
     *
     */
    public function editMbVideo($update, $video_id) {
        $video_id = intval($video_id);
        if($video_id <= 0) {
            return false;
        }

        $condition = array();
        $condition['video_id'] = $video_id;
        $result = $this->table('mb_video')->where($condition)->update($update);
        if($result) {
            return true;
        } else {
            return false;
        }
    }


    /*
     * 更新视频
     * @param array $update
     * @param array $video_id 视频ID
     * @return bool
     *
     */
    public function editMbVideoList($update, $condition) {
        return $this->table('mb_video')->where($condition)->update($update);
    }

    /*
     * 删除视频
     * @param int $video_id
     * @return bool
     *
     */
    public function delMbVideoByID($video_id,$condition=array()) {
        $video_id = intval($video_id);
        if($video_id <= 0) {
            return false;
        }

        $condition = array();
        $condition['video_id'] = $video_id;

        $this->delMbVideoItem($condition, $video_id);

        return $this->table('mb_video')->where($condition)->delete();
    }



    /**
     * 检查视频项目是否存在
     * @param array $condition
     *
     */
    public function isMbVideoItemExist($condition) {
        $item_list = $this->table('mb_video')->where($condition)->select();
        if($item_list) {
            return true;
        } else {
            return false;
        }
    }



    /*
     * 删除
     * @param array $condition
     * @return bool
     *
     */
    public function delMbVideoItem($condition) {
        return $this->table('mb_video')->where($condition)->delete();
    }

    /**
     * 上传点播视频
     */
    public function uploadVideo($video_name,$video_size ,$upload_type){
        if($upload_type == 'promotefile'){
            $video_path = ATTACH_MOBILE . DS . 'video_promote/video';
        }elseif($upload_type == 'demandfile'){
            $video_path = ATTACH_MOBILE . DS . 'video_demand';
        }
        $upload = new UploadVideoFile();
        $upload->set('default_dir', $video_path);
        $upload->set('max_size' , $video_size);
        $upload->set('allow_type', array('mp4'));
        $result = $upload->upfile($video_name);
        if (!$result) {
            return callback(false, $upload->error);
        }

        $data = array();
        $data['name'] = $upload->file_name;
        $data['video_file'] = getMbDemandVideoUrl($upload->file_name , $upload_type);
        return callback(true, '', $data);
    }

    /**
     * 上传图片
     */
    public function uploadImages($image_name , $upload_type){

        $upload = new UploadFile();
        if($upload_type == 'promote_image'){
            $video_path = ATTACH_MOBILE . DS . 'video_promote/image' . DS . $upload->getSysSetPath();
        } elseif ($upload_type == 'news_image'){
            $video_path = ATTACH_MOBILE . DS . 'video_news' . DS . $upload->getSysSetPath();
        }

        $upload->set('default_dir', $video_path);
        $upload->set('max_size', C('image_max_filesize'));
        $upload->set('thumb_width', GOODS_IMAGES_WIDTH);
        $upload->set('thumb_height', GOODS_IMAGES_HEIGHT);
        $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
        $result = $upload->upfile($image_name,false);
        if (!$result) {
            return callback(false, $upload->error);
        }

        $img_path = $upload->getSysSetPath() . $upload->file_name;
        if($upload_type == 'promote_image') {
            $thumb_name = getMbDemandImageUrl($img_path);
        } elseif ($upload_type == 'news_image') {
            $thumb_name = getMbNewsImageUrl($img_path);
        }

        $data = array ();
        $data ['thumb_name'] = $thumb_name;
        $data ['name']      = $img_path;

        return callback(true, '', $data);
    }


}
