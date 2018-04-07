<?php
/**
 * 用户中心-秒杀
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
class store_promotion_spikeControl extends BaseSellerControl {

    const LINK_SPIKE_LIST = 'index.php?act=store_promotion_spike&op=spike_list';
    const LINK_SPIKE_MANAGE = 'index.php?act=store_promotion_spike&op=spike_manage&spike_id=';

    public function __construct() {
        parent::__construct() ;

        //读取语言包
        Language::read('member_layout,promotion_spike');
        //检查秒杀是否开启
        if (intval(C('promotion_allow')) !== 1){
            showMessage("商品促销功能尚未开启", urlShop('seller_center', 'index'),'','error');
        }

    }

    public function indexOp() {
        $this->spike_listOp();
    }

    /**
     * 发布的秒杀活动列表
     **/
    public function spike_listOp() {
        $model_spike_quota = Model('p_spike_quota');
        $model_spike = Model('p_spike');

        if (checkPlatformStore()) {
            Tpl::output('isOwnShop', true);
        } else {
            $current_spike_quota = $model_spike_quota->getSpikeQuotaCurrent($_SESSION['store_id']);
            Tpl::output('current_spike_quota', $current_spike_quota);
        }

        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        if(!empty($_GET['spike_name'])) {
            $condition['spike_name'] = array('like', '%'.$_GET['spike_name'].'%');
        }
        if(!empty($_GET['state'])) {
            $condition['spike_state'] = intval($_GET['state']);
        }
        $spike_list = $model_spike->getSpikeList($condition, 10, 'spike_state asc, end_time desc');
        Tpl::output('list', $spike_list);
        Tpl::output('show_page', $model_spike->showpage());
        Tpl::output('spike_state_array', $model_spike->getSpikeStateArray());

        self::profile_menu('spike_list');
        Tpl::showpage('store_promotion_spike.list');
    }

    /**
     * 添加秒杀活动
     **/
    public function spike_addOp() {
        if (checkPlatformStore()) {
            Tpl::output('isOwnShop', true);
        } else {
            $model_spike_quota = Model('p_spike_quota');
            $current_spike_quota = $model_spike_quota->getSpikeQuotaCurrent($_SESSION['store_id']);
            if(empty($current_spike_quota)) {
                showMessage(Language::get('spike_quota_current_error1'),'','','error');
            }
            Tpl::output('current_spike_quota',$current_spike_quota);
        }

        //输出导航
        self::profile_menu('spike_add');
        Tpl::showpage('store_promotion_spike.add');

    }

    /**
     * 保存添加的秒杀活动
     **/
    public function spike_saveOp() {
        //验证输入
        $spike_name = trim($_POST['spike_name']);
        $start_time = strtotime($_POST['start_time']);
        $end_time = strtotime($_POST['end_time']);
        $upper_limit = intval($_POST['upper_limit']);
        if($upper_limit < 0) {
            $upper_limit = 1;
        }
        $order_limit = intval($_POST['order_limit']);
        if($order_limit < 0) {
            $order_limit = 0;
        }
        if(empty($spike_name)) {
            showDialog(Language::get('spike_name_error'));
        }
        if($start_time >= $end_time) {
            showDialog(Language::get('greater_than_start_time'));
        }

        if (!checkPlatformStore()) {
            //获取当前套餐
            $model_spike_quota = Model('p_spike_quota');
            $current_spike_quota = $model_spike_quota->getSpikeQuotaCurrent($_SESSION['store_id']);
            if(empty($current_spike_quota)) {
                showDialog('没有可用秒杀套餐,请先购买套餐');
            }
            $quota_start_time = intval($current_spike_quota['start_time']);
            $quota_end_time = intval($current_spike_quota['end_time']);
            if($start_time < $quota_start_time) {
                showDialog(sprintf(Language::get('spike_add_start_time_explain'),date('Y-m-d',$current_spike_quota['start_time'])));
            }
            if($end_time > $quota_end_time) {
                showDialog(sprintf(Language::get('spike_add_end_time_explain'),date('Y-m-d',$current_spike_quota['end_time'])));
            }
        }

        //生成活动
        $model_spike = Model('p_spike');
        $param = array();
        $param['spike_name'] = $spike_name;
        $param['spike_title'] = $_POST['spike_title'];
        $param['spike_explain'] = $_POST['spike_explain'];
        $param['quota_id'] = $current_spike_quota['quota_id'] ? $current_spike_quota['quota_id'] : 0;
        $param['start_time'] = $start_time;
        $param['end_time'] = $end_time;
        $param['store_id'] = $_SESSION['store_id'];
        $param['store_name'] = $_SESSION['store_name'];
        $param['member_id'] = $_SESSION['member_id'];
        $param['member_name'] = $_SESSION['member_name'];
        $param['upper_limit'] = $upper_limit;
        $param['order_limit'] = $order_limit;

        $upload = new UploadFile();
        //上传图片
        if (!empty($_FILES['spike_banner']['name'])){
            $upload->set('default_dir', ATTACH_STORE.DS.$_SESSION['store_id']);
            $upload->set('thumb_ext',   '');
            $upload->set('file_name','');
            $upload->set('ifremove',false);
            $result = $upload->upfile('spike_banner');
            if ($result){
                $param['spike_banner'] = $_SESSION['store_id'].DS.$upload->file_name;
            }else {
                showDialog($upload->error);
            }
        }
        //上传图片
        if (!empty($_FILES['spike_common_bg']['name'])){
            $upload->set('default_dir', ATTACH_STORE.DS.$_SESSION['store_id']);
            $upload->set('thumb_ext',   '');
            $upload->set('file_name','');
            $upload->set('ifremove',false);
            $result = $upload->upfile('spike_common_bg');
            if ($result){
                $param['spike_common_bg'] = $_SESSION['store_id'].DS.$upload->file_name;
            }else {
                showDialog($upload->error);
            }
        }

        $result = $model_spike->addSpike($param);
        if($result) {
            $this->recordSellerLog('添加秒杀活动，活动名称：'.$spike_name.'，活动编号：'.$result);
            showDialog(Language::get('spike_add_success'),self::LINK_SPIKE_MANAGE.$result,'succ','',3);
        }else {
            showDialog(Language::get('spike_add_fail'));
        }
    }

    /**
     * 编辑秒杀活动
     **/
    public function spike_editOp() {
        $model_spike = Model('p_spike');

        $spike_info = $model_spike->getSpikeInfoByID($_GET['spike_id']);
        if(empty($spike_info) || !$spike_info['editable']) {
            showMessage(L('param_error'),'','','error');
        }

        Tpl::output('spike_info', $spike_info);

        //输出导航
        self::profile_menu('spike_edit');
        Tpl::showpage('store_promotion_spike.add');
    }

    /**
     * 编辑保存秒杀活动
     **/
    public function spike_edit_saveOp() {
        $spike_id = $_POST['spike_id'];

        $model_spike = Model('p_spike');
        $model_spike_goods = Model('p_spike_goods');

        $spike_info = $model_spike->getSpikeInfoByID($spike_id, $_SESSION['store_id']);
        if(empty($spike_info) || !$spike_info['editable']) {
            showMessage(L('param_error'),'','','error');
        }

        //验证输入
        $spike_name = trim($_POST['spike_name']);
        $upper_limit = intval($_POST['upper_limit']);
        if($upper_limit < 0) {
            $upper_limit = 1;
        }
        $order_limit = intval($_POST['order_limit']);
        if($order_limit < 0) {
            $order_limit = 0;
        }
        if(empty($spike_name)) {
            showDialog(Language::get('spike_name_error'));
        }
        //生成活动
        $param = array();
        $param['spike_name'] = $spike_name;
        $param['spike_title'] = $_POST['spike_title'];
        $param['spike_explain'] = $_POST['spike_explain'];
        $param['upper_limit'] = $upper_limit;
        $param['order_limit'] = $order_limit;

        $upload = new UploadFile();
        //上传图片
        if (!empty($_FILES['spike_banner']['name'])){
            $upload->set('default_dir', ATTACH_STORE.DS.$_SESSION['store_id']);
            $upload->set('thumb_ext',   '');
            $upload->set('file_name','');
            $upload->set('ifremove',false);
            $result = $upload->upfile('spike_banner');
            if ($result){
                $param['spike_banner'] = $_SESSION['store_id'].DS.$upload->file_name;
            }else {
                showDialog($upload->error);
            }
        }
        //删除旧图片
        if (!empty($param['spike_banner']) && !empty($spike_info['spike_banner'])){
            @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$_SESSION['store_id'].DS.$spike_info['spike_banner']);
        }
        //上传图片
        if (!empty($_FILES['spike_common_bg']['name'])){
            $upload->set('default_dir', ATTACH_STORE.DS.$_SESSION['store_id']);
            $upload->set('thumb_ext',   '');
            $upload->set('file_name','');
            $upload->set('ifremove',false);
            $result = $upload->upfile('spike_common_bg');
            if ($result){
                $param['spike_common_bg'] = $_SESSION['store_id'].DS.$upload->file_name;
            }else {
                showDialog($upload->error);
            }
        }
        //删除旧图片
        if (!empty($param['spike_common_bg']) && !empty($spike_info['spike_common_bg'])){
            @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$_SESSION['store_id'].DS.$spike_info['spike_common_bg']);
        }
        $result = $model_spike->editSpike($param, array('spike_id'=>$spike_id));
        unset($param['spike_banner']);
        unset($param['spike_common_bg']);
        $result1 = $model_spike_goods->editSpikeGoods($param, array('spike_id'=>$spike_id));
        if($result && $result1) {
            $this->recordSellerLog('编辑秒杀活动，活动名称：'.$spike_name.'，活动编号：'.$spike_id);
            showDialog(Language::get('nc_common_op_succ'),self::LINK_SPIKE_LIST,'succ','',3);
        }else {
            showDialog(Language::get('nc_common_op_fail'));
        }
    }

    /**
     * 秒杀活动删除
     **/
    public function spike_delOp() {
        $spike_id = intval($_POST['spike_id']);

        $model_spike = Model('p_spike');

        $data = array();
        $data['result'] = true;

        $spike_info = $model_spike->getSpikeInfoByID($spike_id, $_SESSION['store_id']);
        if(!$spike_info) {
            showDialog(L('param_error'));
        }

        $model_spike = Model('p_spike');
        $result = $model_spike->delSpike(array('spike_id'=>$spike_id));

        if($result) {
            Model('p_time')->delSpike($spike_id);
            $this->recordSellerLog('删除秒杀活动，活动名称：'.$spike_info['spike_name'].'活动编号：'.$spike_id);
            showDialog(L('nc_common_op_succ'), urlShop('store_promotion_spike', 'spike_list'), 'succ');
        } else {
            showDialog(L('nc_common_op_fail'));
        }
    }

    /**
     * 秒杀活动管理
     **/
    public function spike_manageOp() {
        $model_spike = Model('p_spike');
        $model_spike_goods = Model('p_spike_goods');

        $spike_id = intval($_GET['spike_id']);
        $spike_info = $model_spike->getSpikeInfoByID($spike_id, $_SESSION['store_id']);
        if(empty($spike_info)) {
            showDialog(L('param_error'));
        }
        Tpl::output('spike_info',$spike_info);

        //获取秒杀商品列表
        $condition = array();
        $condition['spike_id'] = $spike_id;
        $spike_goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition, 10);
        Tpl::output('show_page', $model_spike_goods->showpage());
        Tpl::output('spike_goods_list', $spike_goods_list);

        //输出导航
        self::profile_menu('spike_manage');
        Tpl::showpage('store_promotion_spike.manage');
    }


    /**
     * 秒杀套餐购买
     **/
    public function spike_quota_addOp() {
        //输出导航
        self::profile_menu('spike_quota_add');
        Tpl::showpage('store_promotion_spike_quota.add');
    }

    /**
     * 秒杀套餐购买保存
     **/
    public function spike_quota_add_saveOp() {

        $spike_quota_quantity = intval($_POST['spike_quota_quantity']);

        if($spike_quota_quantity <= 0 || $spike_quota_quantity > 12) {
            showDialog(Language::get('spike_quota_quantity_error'));
        }

        //获取当前价格
        $current_price = intval(C('promotion_spike_price'));

        //获取该用户已有套餐
        $model_spike_quota = Model('p_spike_quota');
        $current_spike_quota= $model_spike_quota->getSpikeQuotaCurrent($_SESSION['store_id']);
        $add_time = 86400 *30 * $spike_quota_quantity;
        if(empty($current_spike_quota)) {
            //生成套餐
            $param = array();
            $param['member_id'] = $_SESSION['member_id'];
            $param['member_name'] = $_SESSION['member_name'];
            $param['store_id'] = $_SESSION['store_id'];
            $param['store_name'] = $_SESSION['store_name'];
            $param['start_time'] = TIMESTAMP;
            $param['end_time'] = TIMESTAMP + $add_time;
            $model_spike_quota->addSpikeQuota($param);
        } else {
            $param = array();
            $param['end_time'] = array('exp', 'end_time + ' . $add_time);
            $model_spike_quota->editSpikeQuota($param, array('quota_id' => $current_spike_quota['quota_id']));
        }

        //记录店铺费用
        $this->recordStoreCost($current_price * $spike_quota_quantity, '购买秒杀');

        $this->recordSellerLog('购买'.$spike_quota_quantity.'份秒杀套餐，单价'.$current_price.$lang['nc_yuan']);

        showDialog(Language::get('spike_quota_add_success'),self::LINK_SPIKE_LIST,'succ');
    }

    /**
     * 选择活动商品
     **/
    public function goods_selectOp() {
        $model_goods = Model('goods');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['goods_name'] = array('like', '%'.$_GET['goods_name'].'%');
        $condition['goods_storage'] = array('gt',0);
        $goods_list = $model_goods->getGeneralGoodsOnlineList($condition, '*', 10);
        Tpl::output('goods_list', $goods_list);
        Tpl::output('show_page', $model_goods->showpage());
        $p_list = Model('p_time')->getPromotionList($goods_list, intval($_GET['t']));
        Tpl::output('p_list', $p_list);
        Tpl::showpage('store_promotion_spike.goods', 'null_layout');
    }

    /**
     * 秒杀商品添加
     **/
    public function spike_goods_addOp() {
        $goods_id = intval($_POST['goods_id']);
        $spike_id = intval($_POST['spike_id']);
        $spike_price = floatval($_POST['spike_price']);

        $model_goods = Model('goods');
        $model_spike = Model('p_spike');
        $model_spike_goods = Model('p_spike_goods');

        $data = array();
        $data['result'] = true;

        $goods_info = $model_goods->getGoodsInfoByID($goods_id);
        if(empty($goods_info) || $goods_info['store_id'] != $_SESSION['store_id']) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }
        $spike_amount = intval($goods_info['goods_storage']);
        $spike_info = $model_spike->getSpikeInfoByID($spike_id, $_SESSION['store_id']);
        if(!$spike_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }

        //检查商品是否已经参加同时段活动
        $condition = array();
        $condition['end_time'] = array('gt', $spike_info['start_time']);
        $condition['goods_id'] = $goods_id;
        $spike_goods = $model_spike_goods->getSpikeGoodsExtendList($condition);
        if(!empty($spike_goods)) {
            $data['result'] = false;
            $data['message'] = '该商品已经参加了同时段活动';
            echo json_encode($data);die;
        }
        $condition = array();
        $condition['end_time'] = array('gt', $spike_info['start_time']);
        $condition['goods_id'] = $goods_id;
        $spike_goods = Model('p_time')->getInfo($condition);
        if(!empty($spike_goods)) {
            $data['result'] = false;
            $data['message'] = '该商品已经参加了同时段其它促销活动';
            echo json_encode($data);die;
        }

        //添加到活动商品表
        $param = array();
        $param['spike_id'] = $spike_info['spike_id'];
        $param['spike_name'] = $spike_info['spike_name'];
        $param['spike_title'] = $spike_info['spike_title'];
        $param['spike_explain'] = $spike_info['spike_explain'];
        $param['goods_id'] = $goods_info['goods_id'];
        $param['store_id'] = $goods_info['store_id'];
        $param['goods_name'] = $goods_info['goods_name'];
        $param['goods_price'] = $goods_info['goods_price'];
        $param['spike_price'] = $spike_price;
        $param['goods_image'] = $goods_info['goods_image'];
        $param['start_time'] = $spike_info['start_time'];
        $param['end_time'] = $spike_info['end_time'];
        $param['upper_limit'] = $spike_info['upper_limit'];
        $param['order_limit'] = $spike_info['order_limit'];
        $param['gc_id_1'] = $goods_info['gc_id_1'];
        $param['spike_amount'] = $spike_amount;

        $spike_goods_info = $model_spike_goods->addSpikeGoods($param);

        if($spike_goods_info) {
            $data['message'] = '添加成功';
            $data['spike_goods'] = $spike_goods_info;
            Model('p_time')->addSpike($spike_goods_info);
            $this->recordSellerLog('添加秒杀商品，活动名称：'.$spike_info['spike_name'].'，商品名称：'.$goods_info['goods_name']);
        } else {
            $data['result'] = false;
            $data['message'] = L('param_error');
        }
        echo json_encode($data);die;
    }

    /**
     * 秒杀商品价格修改
     **/
    public function spike_goods_price_editOp() {
        $spike_goods_id = intval($_POST['spike_goods_id']);
        $spike_price = floatval($_POST['spike_price']);
        $spike_amount = intval($_POST['spike_amount']);

        $data = array();
        $data['result'] = true;

        $model_spike_goods = Model('p_spike_goods');

        $spike_goods_info = $model_spike_goods->getSpikeGoodsInfoByID($spike_goods_id, $_SESSION['store_id']);
        if(!$spike_goods_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }
        $goods_info = Model('goods')->getGoodsInfoByID($spike_goods_info['goods_id']);
        $spike_amount = intval($goods_info['goods_storage']);

        $update = array();
        $update['spike_price'] = $spike_price;
        $update['spike_amount'] = $spike_amount;
        $condition = array();
        $condition['spike_goods_id'] = $spike_goods_id;
        $result = $model_spike_goods->editSpikeGoods($update, $condition);

        if($result) {
            $spike_goods_info['spike_price'] = $spike_price;
            $spike_goods_info['spike_amount'] = $spike_amount;
            $spike_goods_info = $model_spike_goods->getSpikeGoodsExtendInfo($spike_goods_info);
            $data['spike_price'] = $spike_goods_info['spike_price'];
            $data['spike_discount'] = $spike_goods_info['spike_discount'];
            $data['spike_amount'] = $spike_goods_info['spike_amount'];

            // 添加对列修改商品促销价格
            QueueClient::push('updateGoodsPromotionPriceByGoodsId', $spike_goods_info['goods_id']);
            Model('p_time')->editSpikeGoods($spike_goods_info);
            $this->recordSellerLog('秒杀价格修改为：'.$spike_goods_info['spike_price'].'，商品名称：'.$spike_goods_info['goods_name']);
        } else {
            $data['result'] = false;
            $data['message'] = L('nc_common_op_succ');
        }
        echo json_encode($data);die;
    }

    /**
     * 秒杀商品删除
     **/
    public function spike_goods_deleteOp() {
        $model_spike_goods = Model('p_spike_goods');
        $model_spike = Model('p_spike');

        $data = array();
        $data['result'] = true;

        $spike_goods_id = intval($_POST['spike_goods_id']);
        $spike_goods_info = $model_spike_goods->getSpikeGoodsInfoByID($spike_goods_id);
        if(!$spike_goods_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }

        $spike_info = $model_spike->getSpikeInfoByID($spike_goods_info['spike_id'], $_SESSION['store_id']);
        if(!$spike_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }

        if(!$model_spike_goods->delSpikeGoods(array('spike_goods_id'=>$spike_goods_id))) {
            $data['result'] = false;
            $data['message'] = L('spike_goods_delete_fail');
            echo json_encode($data);die;
        }

        // 添加对列修改商品促销价格
        QueueClient::push('updateGoodsPromotionPriceByGoodsId', $spike_goods_info['goods_id']);
        Model('p_time')->delSpikeGoods($spike_goods_info);
        $this->recordSellerLog('删除秒杀商品，活动名称：'.$spike_info['spike_name'].'，商品名称：'.$spike_goods_info['goods_name']);
        echo json_encode($data);die;
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @param array     $array      附加菜单
     * @return
     */
    private function profile_menu($menu_key='') {
        $menu_array = array(
            1=>array('menu_key'=>'spike_list','menu_name'=>Language::get('promotion_active_list'),'menu_url'=>'index.php?act=store_promotion_spike&op=spike_list'),
        );
        switch ($menu_key){
            case 'spike_add':
                $menu_array[] = array('menu_key'=>'spike_add','menu_name'=>Language::get('promotion_join_active'),'menu_url'=>'index.php?act=store_promotion_spike&op=spike_add');
                break;
            case 'spike_edit':
                $menu_array[] = array('menu_key'=>'spike_edit','menu_name'=>'编辑活动','menu_url'=>'javascript:;');
                break;
            case 'spike_quota_add':
                $menu_array[] = array('menu_key'=>'spike_quota_add','menu_name'=>Language::get('promotion_buy_product'),'menu_url'=>'index.php?act=store_promotion_spike&op=spike_quota_add');
                break;
            case 'spike_manage':
                $menu_array[] = array('menu_key'=>'spike_manage','menu_name'=>Language::get('promotion_goods_manage'),'menu_url'=>'index.php?act=store_promotion_spike&op=spike_manage&spike_id='.$_GET['spike_id']);
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
