<?php
/**
 * 资讯管理
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
class mb_newsControl extends SystemControl{

    public function __construct(){
        parent::__construct();
    }

    public function indexOp() {
        $this->listOp();
    }

    /**
     * 管理
     */
    public function listOp(){
        Tpl::showpage('mb_news.index');

    }

    public function get_xmlOp(){
        $model_video = Model('mb_video');

        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $condition['video_identity_type'] = 1;
        $page = $_POST['rp'];
        $news_list = $model_video->getMbVideoList($condition,$page);

        $data = array();
        $data['now_page'] = $model_video->shownowpage();
        $data['total_num'] = $model_video->gettotalnum();
        foreach($news_list as $k => $v){
            $param = array();
            $operation = "<a class='btn red' href='javascript:void(0);' onclick=\"fg_del(".$v['video_id'].")\"><i class='fa fa-trash-o'></i>删除</a>";
            $operation .= "<a class='btn blue' href='index.php?act=mb_news&op=news_edit&video_id=" . $v['video_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>";
            $param['operation'] = $operation;
            $cate_info = Model('mb_video_cate')->getVideoCategoryInfo(array( 'cate_id' => $v['cate_id']));
            $param['cate_name'] = $cate_info['cate_name'];
            $param['news_name'] = $v['news_name'];
            if(!empty($v['news_image'])) {
                $param['news_image'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=". getMbNewsImageUrl($v['news_image']).">\")'><i class='fa fa-picture-o'></i></a>";
            }

            $data['list'][$v['video_id']] = $param;
        }

        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 添加
     */
    public function news_addOp(){
        /**
         * 视频分类
         */
        $video_cate_list = Model('mb_video_cate')->getVideoCategoryList(array());
        Tpl::output('video_cate_list', $video_cate_list);


        if (chksubmit()){
            $insert_array = array();
            $insert_array['news_name']        = $_POST['news_name'];
            $insert_array['news_image']        = $_POST['news_image_path'];
            $insert_array['mobile_body']        = $this->_getMobileBody($_POST['m_body']);
            $insert_array['cate_id']        = intval($_POST['video_cate']);
            $insert_array['recommend_goods']        = $this->_getRecommendGoods($_POST['goods']);
            $insert_array['add_time']        = time();
            $insert_array['video_identity'] = 'news';
            $insert_array['video_identity_type'] = 1;
            $result = Model('mb_video')->addMbVideo($insert_array);
            if ($result){
                $this->log('新增视频资讯',1);
                showMessage('保存成功','index.php?act=mb_news&op=index');
            }else {
                $this->log('新增视频资讯',0);
                showMessage('保存失败');
            }
        }

        Tpl::showpage('mb_news.add');
    }

    /**
     * 编辑
     */
    public function news_editOp(){

        /**
         * 资讯信息
         */
        $news_array = Model('mb_video')->getMbVideoInfoByID(intval($_GET['video_id']));
        if (empty($news_array)){
            showMessage('参数非法');
        }

        if (chksubmit()){
            // 更新分类信息
            $update_array = array();
            $update_array['news_name']        = $_POST['news_name'];
            $update_array['mobile_body']        = $this->_getMobileBody($_POST['m_body']);
            $update_array['cate_id']        = intval($_POST['video_cate']);
            $update_array['recommend_goods']        = $this->_getRecommendGoods($_POST['goods']);
            $update_array['video_identity'] = 'news';
            $update_array['video_identity_type'] = 1;
            $update_array['news_image']        = $_POST['news_image_path'];
            $result = Model('mb_video')->editMbVideo($update_array, intval($_GET['video_id']));
            if (!$result){
                $this->log('编辑视频资讯',0);
                showMessage('保存失败');
            }

            $this->log('编辑视频资讯',1);
            showMessage('保存成功','index.php?act=mb_news&op=index','html','succ',1,5000);
        }

        /**
         * 视频分类
         */
        $video_cate_list = Model('mb_video_cate')->getVideoCategoryList(array());
        Tpl::output('video_cate_list', $video_cate_list);

        

        //手机端描述
        if ($news_array['mobile_body'] != '') {
            $news_array['mb_body'] = unserialize($news_array['mobile_body']);
            if (is_array($news_array['mb_body'])) {
                $mobile_body = '[';
                foreach ($news_array['mb_body'] as $val ) {
                    $mobile_body .= '{"type":"' . $val['type'] . '","value":"' . $val['value'] . '"},';
                }
                $mobile_body = rtrim($mobile_body, ',') . ']';
            }
            $news_array['mobile_body'] = $mobile_body;
        }

        /**
         * 推荐商品
         */
        if (!empty($news_array['recommend_goods'])) {
            $recommend_goods = unserialize($news_array['recommend_goods']);
            $goodscommonid_array = $recommend_goods['goods_commonid'];
            $goods_common_list = Model('goods')->getGoodsCommonList(array('goods_commonid' => array('in', $goodscommonid_array)), 'goods_commonid,goods_price,goods_image,goods_name');
            foreach($goods_common_list as $k => $v){
                $goods_common_list[$k]['recommend_appoint'] = 1;
            }
            $recommend_goods_common_list = array_under_reset($goods_common_list, 'goods_commonid');
            Tpl::output('recommend_goods_common_list', $recommend_goods_common_list);
        }

        Tpl::output('video_id',$_GET['video_id']);

        Tpl::output('news_array',$news_array);
        Tpl::showpage('mb_news.edit');
    }


    /**
     * 删除
     */
    public function news_delOp(){
        $model_video = Model('mb_video');
        if ($_GET['id'] != ''){
            $news_info = $model_video->getMbVideoInfoByID($_GET['id']);
            /**
             * 删除图片
             */
            if (!empty($news_info['news_image'])){
                @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_MOBILE.'/video_news/'.$news_info['news_image']);
            }
            //删除
            Model('mb_video')->delMbVideoByID($_GET['id']);
            $this->log('删除视频资讯' . '[ID:' . $_GET['id'] . ']',1);
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        }else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }


    /**
     * 序列化保存手机端商品描述数据
     */
    private function _getMobileBody($mobile_body) {
        if ($mobile_body != '') {
            $mobile_body = str_replace('&quot;', '"', $mobile_body);
            $mobile_body = json_decode($mobile_body, true);
            if (!empty($mobile_body)) {
                return serialize($mobile_body);
            }
        }
        return '';
    }

    /**
     * 序列化保存手机端商品推荐数据
     */
    private function _getRecommendGoods($goods){
        $list = array();
        if(!empty($goods)){
            foreach($goods as $k => $v){
                $list['goods_commonid'][] = $v['gid'];
            }
            return serialize($list);
        }else{
            return '';
        }
    }

    /**
     * 添加推荐商品
     */
    public function recommend_add_goodsOp() {
        /**
         * 实例化模型
         */
        $model_goods = Model('goods');

        // where条件
        $where = array ();
        if($_GET['keyword'] != '') {
            $where[$_GET['qtype']] = array('like', '%' . $_GET['keyword'] . '%');
        }
        $goods_list = $model_goods->getGeneralGoodsCommonList($where, '*', 8);
        $storage_array = $model_goods->calculateStorage($goods_list);
        foreach($goods_list as $k => $v){
            $goods_list[$k]['goods_storage'] = $storage_array[$v['goods_commonid']]['sum'];
        }
        Tpl::output('show_page', $model_goods->showpage(2));
        Tpl::output('goods_list', $goods_list);

        Tpl::showpage('mb_news.recommend_add_goods', 'null_layout');
    }

}
