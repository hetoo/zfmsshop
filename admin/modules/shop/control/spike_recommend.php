<?php
/**
 * 秒杀推荐设置
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
class spike_recommendControl extends SystemControl{
    private $links = array(
        array('url'=>'act=spike_recommend&op=index','text'=>'商品推荐'),
        array('url'=>'act=spike_recommend&op=pic','text'=>'头部图片')
    );
    public function __construct(){
        parent::__construct();
    }

    /**
     * 商品推荐
     */
    public function indexOp(){
        Tpl::output('top_link',$this->sublink($this->links, 'index'));
        $model_spike = Model('p_spike');
        if (chksubmit()){
            $result = $model_spike->updateRecommend($_POST['spike_list']);
            if ($result){
                showMessage(Language::get('nc_common_save_succ'));
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $list = $model_spike->getRecommendList();
        Tpl::output('list',$list);
        Tpl::showpage('spike_recommend_edit');
    }

    /**
     * 商品推荐
     */
    public function goods_listOp() {
        $model_spike_goods = Model('p_spike_goods');
        $condition = array();
        $goods_name = trim($_GET['goods_name']);
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $condition['goods_name|spike_name'] = array('like','%'.$goods_name.'%');
        $condition['spike_state'] = 1;
        $goods_list = $model_spike_goods->getSpikeGoodsList($condition,8);
        Tpl::output('show_page',$model_spike_goods->showpage(1));
        Tpl::output('goods_list',$goods_list);
        Tpl::showpage('spike_recommend_goods','null_layout');
    }

    /**
     * 活动推荐
     */
    public function spike_listOp() {
        $model_spike = Model('p_spike');
        $condition = array();
        $spike_name = trim($_GET['spike_name']);
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $condition['store_name|spike_name'] = array('like','%'.$spike_name.'%');
        $condition['spike_state'] = 1;
        $spike_list = $model_spike->getSpikeList($condition,8);
        Tpl::output('show_page',$model_spike->showpage(1));
        Tpl::output('spike_list',$spike_list);
        Tpl::showpage('spike_recommend_spike','null_layout');
    }

    /**
     * 头部图片
     */
    public function picOp(){
        Tpl::output('top_link',$this->sublink($this->links, 'pic'));
        $size = 3;//上传显示图片总数
        $i = 1;
        $info = array();
        $model_spike = Model('p_spike');
        $code_info = $model_spike->getRecommendPic();
        if(!empty($code_info['code_info'])) {
            $info = unserialize($code_info['code_info']);
        }
        if (chksubmit()) {
            for ($i;$i <= $size;$i++) {
                $file = 'pic'.$i;
                $info[$i]['pic'] = $_POST['show_pic'.$i];
                $info[$i]['url'] = $_POST['url'.$i];
                if (!empty($_FILES[$file]['name'])) {//上传图片
                    $filename_tmparr = explode('.', $_FILES[$file]['name']);
                    $ext = end($filename_tmparr);
                    $file_name = 'spike_pic_'.$i.'.'.$ext;
                    $upload = new UploadFile();
                    $upload->set('default_dir',ATTACH_COMMON);
                    $upload->set('file_name',$file_name);
                    $result = $upload->upfile($file);
                    if ($result) {
                        $info[$i]['pic'] = $file_name;
                    }
                }
            }
            $result = $model_spike->updateRecommendPic($info);
            showMessage(Language::get('nc_common_save_succ'),'index.php?act=spike_recommend&op=pic');
        }
        Tpl::output('size',$size);
        Tpl::output('list',$info);
        Tpl::showpage('spike_recommend_pic');
    }

}
