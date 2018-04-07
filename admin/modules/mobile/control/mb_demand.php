<?php
/**
 * 点播管理
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
class mb_demandControl extends SystemControl{

    public function __construct(){
        parent::__construct();
    }

    public function indexOp() {
        $this->listOp();
    }

    /**
     * 点播列表
     */
    public function listOp(){
        Tpl::showpage('mb_demand.index');
    }

    public function get_xmlOp(){
        $model_video = Model('mb_video');
        $model_store = Model('store');

        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $condition['video_identity_type'] = 2;
        $page = $_POST['rp'];
        $demand_list = $model_video->getMbVideoList($condition,$page);

        $data = array();
        $data['now_page'] = $model_video->shownowpage();
        $data['total_num'] = $model_video->gettotalnum();
        foreach($demand_list as $k => $v){
            $param = array();
            $operation = "<a class='btn red' href='javascript:void(0);' onclick=\"fg_del(".$v['video_id'].")\"><i class='fa fa-trash-o'></i>删除</a>";
            $operation .= "<span class='btn'><em><i class='fa fa-cog'></i>设置 <i class='arrow'></i></em><ul>";
            $operation .= "<li><a href='index.php?act=mb_demand&op=demand_edit&video_id=" . $v['video_id'] . "'>编辑点播</a></li>";
            if($v['recommend_goods'] == ''){
                $operation .= "<li><a href='index.php?act=mb_demand&op=demand_recommend_goods&video_id=".$v['video_id']."&store_id=".$v['store_id']."'>推荐商品</a></li>";
            }else{
                $operation .= "<li><a href='index.php?act=mb_demand&op=demand_recommend_goods&video_id=" . $v['video_id'] . "&store_id=".$v['store_id']."'>推荐商品</a></li>";
            }
            $operation .= "</ul>";
            $param['operation'] = $operation;
            $cate_info = Model('mb_video_cate')->getVideoCategoryInfo(array( 'cate_id' => $v['cate_id']));
            $param['cate_name'] = $cate_info['cate_name'];
            $param['store_id'] = $v['store_id'];
            $store_info = $model_store->getStoreInfoByID($v['store_id']);
            $param['store_name'] = (empty($v['store_name'])) ? $store_info['store_name'] : $v['store_name'];
            $param['store_avatar'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getStoreLogo($store_info['store_avatar']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['store_label'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getStoreLogo($store_info['store_label'], 'store_logo').">\")'><i class='fa fa-picture-o'></i></a>";
            $data['list'][$v['video_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 点播添加
     */
    public function demand_addOp(){
        /**
         * 视频分类
         */
        $cate_where = array();
        $video_cate_list = Model('mb_video_cate')->getVideoCategoryList($cate_where);
        Tpl::output('video_cate_list', $video_cate_list);
        /**
         * 点播
         */
        if (chksubmit()){
            $model_video = Model('mb_video');
            //店铺详细信息
            $store_info = Model('store')->getStoreInfoByID($_POST['store']);
            //增加点播
            $insert_array = array();                
            $insert_array['store_id']        = $store_info['store_id'];
            $insert_array['store_name']      = $store_info['store_name'];
            $insert_array['cate_id']        = $_POST['video_cate'];
            $insert_array['add_time']        = time();
            $insert_array['promote_video']        = ($_POST['promote'] == '1') ? '' : $_POST['promote_video_path'];
            $insert_array['promote_text']        = ($_POST['promote'] == '1') ? '' : $_POST['promote_text'];
            $insert_array['demand_video']        = ($_POST['promote'] == '1') ? '' : $_POST['demand_video_path'];
            $insert_array['promote_image']    =  ($_POST['promote'] == '0') ? '' : $_POST['promote_image_path'];
            $insert_array['video_identity'] = 'demand';
            $insert_array['video_identity_type'] = 2;
            $result = $model_video->addMbVideo($insert_array);
            if ($result){
                $this->log('新增点播信息，店铺名称:"'.$store_info['store_name'].'"',1);
                showDialog('保存成功', 'index.php?act=mb_demand&op=index', 'succ', '', 3);
            }else {
                $this->log('新增点播信息，店铺名称:"' . $store_info['store_name'] . '"', 0);
                showMessage('保存失败');
            }
        }
        Tpl::showpage('mb_demand.add');
    }

    /**
     * 编辑
     */
    public function demand_editOp(){
        /**
         * 点播信息
         */
        $demand_array = Model('mb_video')->getMbVideoInfoByID(intval($_GET['video_id']));
        if (empty($demand_array)){
            showMessage('参数非法');
        }
        Tpl::output('demand_array',$demand_array);
        /**
         * 店铺信息
         */
        $store_info = Model('store')->getStoreInfoByID($demand_array['store_id']);
        Tpl::output('store_info', $store_info);
        /**
         * 视频分类
         */
        $video_cate_list = Model('mb_video_cate')->getVideoCategoryList(array());
        Tpl::output('video_cate_list', $video_cate_list);

        if (chksubmit()){
            // 更新点播信息
            $update_array = array();
            $update_array['store_id']        = $_POST['store'];
            $update_array['store_name']      = $store_info['store_name'];
            $update_array['cate_id']        = $_POST['video_cate'];
            $update_array['promote_video']        = ($_POST['promote'] == '1') ? '' : $_POST['promote_video_path'];
            $update_array['promote_text']        = ($_POST['promote'] == '1') ? '' : $_POST['promote_text'];
            $update_array['demand_video']        = ($_POST['promote'] == '1') ? '' : $_POST['demand_video_path'];
            $update_array['promote_image']    =  ($_POST['promote'] == '0') ? '' : $_POST['promote_image_path'];
            $update_array['video_identity'] = 'demand';
            $update_array['video_identity_type'] = 2;
            $result = Model('mb_video')->editMbVideo($update_array, intval($_GET['video_id']));
            if (!$result){
                $this->log('编辑点播信息',0);
                showMessage('保存失败');
            }
            $this->log('编辑点播信息',1);
            showDialog('保存成功', 'index.php?act=mb_demand&op=index' , 'succ', '', 3);
        }

        Tpl::showpage('mb_demand.edit');
    }


    /**
     * 删除点播
     */
    public function demand_delOp(){
        $model_video = Model('mb_video');
        if ($_GET['id'] != ''){
            $demand_array = $model_video->getMbVideoInfoByID($_GET['id']);
            /**
             * 删除推广位图片
             */
            if (!empty($demand_array['promote_image'])){
                @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_MOBILE.'/video_promote/image/'.$demand_array['promote_image']);
            }

            /**
             * 删除推广位视频
             */
            if (!empty($demand_array['promote_video'])){
                @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_MOBILE.'/video_promote/video/'.$demand_array['promote_video']);
            }

            /**
             * 删除点播视频
             */
            if (!empty($demand_array['demand_video'])){
                @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_MOBILE.'/video_demand/'.$demand_array['demand_video']);
            }

            //删除点播
            Model('mb_video')->delMbVideoByID($_GET['id']);
            $this->log('删除点播' . '[ID:' . $_GET['id'] . ']',1);
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        }else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }

    /**
     * 推荐商品
     */
    public function demand_recommend_goodsOp(){
        $model_video = Model('mb_video');
        $model_goods = Model('goods');
        if(!$_GET['store_id']){
            showMessage('先推荐店铺，才可以推荐商品');
        }
        if(!$_GET['video_id']){
            showMessage('参数错误');
        }
        $video_id = $_GET['video_id'];
        //判断推荐商品是否有值
        $video_info = $model_video->getMbVideoInfo(array('video_id' => $video_id) , 'recommend_goods');
        if(!empty($video_info['recommend_goods'])){
            /**
             * 推荐商品编辑
             */
            $recommend_list = unserialize($video_info['recommend_goods']);
            foreach($recommend_list as $goods_commonid => $item){
                $recommend_goods_common_list[$item['goods_commonid']]['goods_commonid'] = $item['goods_commonid'];
                $common_info = $model_goods->getGoodsCommonInfo(array('goods_commonid' => $goods_commonid));
                $recommend_goods_common_list[$item['goods_commonid']]['goods_name'] = $common_info['goods_name'];
                $recommend_goods_common_list[$item['goods_commonid']]['goods_price'] = $common_info['goods_price'];
                $recommend_goods_common_list[$item['goods_commonid']]['goods_image'] = $common_info['goods_image'];
            }
        }else{
            $recommend_goods_common_list = array();
        }
        Tpl::output('recommend_goods_common_list', $recommend_goods_common_list);

        if (chksubmit()){
            $recommend_common_list = $this->_getRecommendGoods($_POST['goods']);
            $update_data = array();
            $update_data['recommend_goods'] = serialize(array());
            if(!empty($recommend_common_list)){
                $update_data['recommend_goods'] = serialize($recommend_common_list);
            }
            $result = $model_video->editMbVideo($update_data , $video_id);
            if($result){
                $this->log('点播推荐商品',1);
                showMessage('保存成功' , 'index.php?act=mb_demand&op=index');
            }else{
                $this->log('点播推荐商品',0);
                showMessage('保存失败');
            }
        }
        Tpl::output('store_id',$_GET['store_id']);
        Tpl::showpage('mb_demand.recommend_goods');
    }

    /**
     * 整理推荐商品数据
     *
     * @param $goods_arr
     */
    private function _getRecommendGoods($goods_arr){
        $goods_list = array();
        if(!empty($goods_arr) && is_array($goods_arr)){
            foreach($goods_arr as $key => $goods_info){
                $goods_list[$goods_info['gid']]['goods_commonid'] = $goods_info['gid'];
            }
        }
        return $goods_list;
    }

    /**
     * 添加推荐商品
     */
    public function recommend_add_goodsOp() {
        $model_goods = Model('goods');

        if(!$_GET['store_id']){
            showMessage('先推荐店铺，才可以推荐商品');
        }

        // where条件
        $where = array ();
        $where['store_id'] = $_GET['store_id'];
        if($_GET['keyword'] != '') {
            $where[$_GET['qtype']] = array('like', '%' . $_GET['keyword'] . '%');
        }

        $goods_common_list = $model_goods->getGeneralGoodsCommonList($where, '*', 8);
        $storage_array = $model_goods->calculateStorage($goods_common_list);
        foreach($goods_common_list as $k => $v){
            $goods_common_list[$k]['goods_storage'] = $storage_array[$v['goods_commonid']]['sum'];
        }
        Tpl::output('show_page', $model_goods->showpage(2));
        Tpl::output('goods_common_list', $goods_common_list);

        Tpl::showpage('mb_demand.recommend_add_goods', 'null_layout');
    }

    /**
     * 选择推荐店铺
     */
    public function select_recommend_storeOp() {
        $condition = array();
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_GET['member_name'] != '') {
            $condition['member_name'] = array('like', '%' . $_GET['member_name'] . '%');
        }
        if ($_GET['seller_name'] != '') {
            $condition['seller_name'] = array('like', '%' . $_GET['seller_name'] . '%');
        }
        if ($_GET['search_keyword'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['search_keyword'] . '%');
        }
        $store_list = Model('store')->getStoreOnlineList($condition, 10);

        Tpl::output('store_list', $store_list);
        Tpl::output('show_page', Model('store')->showpage());
        Tpl::showpage('mb_demand.recommend_store', 'null_layout');
    }
}
