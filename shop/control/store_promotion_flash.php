<?php
/**
 * 用户中心-闪购
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
class store_promotion_flashControl extends BaseSellerControl {

    const LINK_SPIKE_LIST = 'index.php?act=store_promotion_flash&op=flash_list';
    const LINK_SPIKE_MANAGE = 'index.php?act=store_promotion_flash&op=flash_manage&flash_id=';

    public function __construct() {
        parent::__construct() ;

        //读取语言包
        Language::read('member_layout,promotion_flash');
        //检查闪购是否开启
        if (intval(C('promotion_allow')) !== 1){
            showMessage("商品促销功能尚未开启", urlShop('seller_center', 'index'),'','error');
        }

    }

    public function indexOp() {
        $this->flash_listOp();
    }

    /**
     * 发布的闪购活动列表
     **/
    public function flash_listOp() {
        $model_flash_quota = Model('p_flash_quota');
        $model_flash = Model('p_flash');

        if (checkPlatformStore()) {
            Tpl::output('isOwnShop', true);
        } else {
            $current_flash_quota = $model_flash_quota->getFlashQuotaCurrent($_SESSION['store_id']);
            Tpl::output('current_flash_quota', $current_flash_quota);
        }

        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        if(!empty($_GET['flash_name'])) {
            $condition['flash_name'] = array('like', '%'.$_GET['flash_name'].'%');
        }
        if(!empty($_GET['state'])) {
            $condition['flash_state'] = intval($_GET['state']);
        }
        $flash_list = $model_flash->getFlashList($condition, 10, 'flash_state asc, end_time desc');
        Tpl::output('list', $flash_list);
        Tpl::output('show_page', $model_flash->showpage());
        Tpl::output('flash_state_array', $model_flash->getFlashStateArray());

        self::profile_menu('flash_list');
        Tpl::showpage('store_promotion_flash.list');
    }

    /**
     * 添加闪购活动
     **/
    public function flash_addOp() {
        if (checkPlatformStore()) {
            Tpl::output('isOwnShop', true);
        } else {
            $model_flash_quota = Model('p_flash_quota');
            $current_flash_quota = $model_flash_quota->getFlashQuotaCurrent($_SESSION['store_id']);
            if(empty($current_flash_quota)) {
                showMessage(Language::get('flash_quota_current_error1'),'','','error');
            }
            Tpl::output('current_flash_quota',$current_flash_quota);
        }

        //输出导航
        self::profile_menu('flash_add');
        Tpl::showpage('store_promotion_flash.add');

    }

    /**
     * 保存添加的闪购活动
     **/
    public function flash_saveOp() {
        //验证输入
        $flash_name = trim($_POST['flash_name']);
        $start_time = strtotime($_POST['start_time']);
        $end_time = strtotime($_POST['end_time']);
        $upper_limit = intval($_POST['upper_limit']);
        if($upper_limit <= 0) {
            $upper_limit = 1;
        }
        if(empty($flash_name)) {
            showDialog(Language::get('flash_name_error'));
        }
        if($start_time >= $end_time) {
            showDialog(Language::get('greater_than_start_time'));
        }

        if (!checkPlatformStore()) {
            //获取当前套餐
            $model_flash_quota = Model('p_flash_quota');
            $current_flash_quota = $model_flash_quota->getFlashQuotaCurrent($_SESSION['store_id']);
            if(empty($current_flash_quota)) {
                showDialog('没有可用闪购套餐,请先购买套餐');
            }
            $quota_start_time = intval($current_flash_quota['start_time']);
            $quota_end_time = intval($current_flash_quota['end_time']);
            if($start_time < $quota_start_time) {
                showDialog(sprintf(Language::get('flash_add_start_time_explain'),date('Y-m-d',$current_flash_quota['start_time'])));
            }
            if($end_time > $quota_end_time) {
                showDialog(sprintf(Language::get('flash_add_end_time_explain'),date('Y-m-d',$current_flash_quota['end_time'])));
            }
        }

        //生成活动
        $model_flash = Model('p_flash');
        $param = array();
        $param['flash_name'] = $flash_name;
        $param['flash_title'] = $_POST['flash_title'];
        $param['flash_explain'] = $_POST['flash_explain'];
        $param['quota_id'] = $current_flash_quota['quota_id'] ? $current_flash_quota['quota_id'] : 0;
        $param['start_time'] = $start_time;
        $param['end_time'] = $end_time;
        $param['store_id'] = $_SESSION['store_id'];
        $param['store_name'] = $_SESSION['store_name'];
        $param['member_id'] = $_SESSION['member_id'];
        $param['member_name'] = $_SESSION['member_name'];
        $param['upper_limit'] = $upper_limit;
        $param = $this->upload_pic($param);//上传图片
        $result = $model_flash->addFlash($param);
        if($result) {
            $this->recordSellerLog('添加闪购活动，活动名称：'.$flash_name.'，活动编号：'.$result);
            showDialog(Language::get('flash_add_success'),self::LINK_SPIKE_MANAGE.$result,'succ','',3);
        }else {
            showDialog(Language::get('flash_add_fail'));
        }
    }

    /**
     * 编辑闪购活动
     **/
    public function flash_editOp() {
        $model_flash = Model('p_flash');

        $flash_info = $model_flash->getFlashInfoByID($_GET['flash_id']);
        if(empty($flash_info) || !$flash_info['editable']) {
            showMessage(L('param_error'),'','','error');
        }

        Tpl::output('flash_info', $flash_info);

        //输出导航
        self::profile_menu('flash_edit');
        Tpl::showpage('store_promotion_flash.add');
    }

    /**
     * 编辑保存闪购活动
     **/
    public function flash_edit_saveOp() {
        $flash_id = $_POST['flash_id'];

        $model_flash = Model('p_flash');
        $model_flash_goods = Model('p_flash_goods');

        $flash_info = $model_flash->getFlashInfoByID($flash_id, $_SESSION['store_id']);
        if(empty($flash_info) || !$flash_info['editable']) {
            showMessage(L('param_error'),'','','error');
        }

        //验证输入
        $flash_name = trim($_POST['flash_name']);
        $upper_limit = intval($_POST['upper_limit']);
        if($upper_limit <= 0) {
            $upper_limit = 1;
        }
        if(empty($flash_name)) {
            showDialog(Language::get('flash_name_error'));
        }

        //生成活动
        $param = array();
        $param['flash_name'] = $flash_name;
        $param['flash_title'] = $_POST['flash_title'];
        $param['flash_explain'] = $_POST['flash_explain'];
        $param['upper_limit'] = $upper_limit;
        $result1 = $model_flash_goods->editFlashGoods($param, array('flash_id'=>$flash_id));
        $param = $this->upload_pic($param);//上传图片
        $result = $model_flash->editFlash($param, array('flash_id'=>$flash_id));
        if($result && $result1) {
            $this->recordSellerLog('编辑闪购活动，活动名称：'.$flash_name.'，活动编号：'.$flash_id);
            showDialog(Language::get('nc_common_op_succ'),self::LINK_SPIKE_LIST,'succ','',3);
        }else {
            showDialog(Language::get('nc_common_op_fail'));
        }
    }

    /**
     * 闪购活动删除
     **/
    public function flash_delOp() {
        $flash_id = intval($_POST['flash_id']);

        $model_flash = Model('p_flash');

        $data = array();
        $data['result'] = true;

        $flash_info = $model_flash->getFlashInfoByID($flash_id, $_SESSION['store_id']);
        if(!$flash_info) {
            showDialog(L('param_error'));
        }

        $model_flash = Model('p_flash');
        $result = $model_flash->delFlash(array('flash_id'=>$flash_id));

        if($result) {
            Model('p_time')->delFlash($flash_id);
            $this->recordSellerLog('删除闪购活动，活动名称：'.$flash_info['flash_name'].'活动编号：'.$flash_id);
            showDialog(L('nc_common_op_succ'), urlShop('store_promotion_flash', 'flash_list'), 'succ');
        } else {
            showDialog(L('nc_common_op_fail'));
        }
    }

    /**
     * 闪购活动管理
     **/
    public function flash_manageOp() {
        $model_flash = Model('p_flash');
        $model_flash_goods = Model('p_flash_goods');

        $flash_id = intval($_GET['flash_id']);
        $flash_info = $model_flash->getFlashInfoByID($flash_id, $_SESSION['store_id']);
        if(empty($flash_info)) {
            showDialog(L('param_error'));
        }
        Tpl::output('flash_info',$flash_info);

        //获取闪购商品列表
        $condition = array();
        $condition['flash_id'] = $flash_id;
        $flash_goods_list = $model_flash_goods->getFlashGoodsExtendList($condition, 10);
        Tpl::output('show_page', $model_flash_goods->showpage());
        Tpl::output('flash_goods_list', $flash_goods_list);

        //输出导航
        self::profile_menu('flash_manage');
        Tpl::showpage('store_promotion_flash.manage');
    }


    /**
     * 闪购套餐购买
     **/
    public function flash_quota_addOp() {
        //输出导航
        self::profile_menu('flash_quota_add');
        Tpl::showpage('store_promotion_flash_quota.add');
    }

    /**
     * 闪购套餐购买保存
     **/
    public function flash_quota_add_saveOp() {

        $flash_quota_quantity = intval($_POST['flash_quota_quantity']);

        if($flash_quota_quantity <= 0 || $flash_quota_quantity > 12) {
            showDialog(Language::get('flash_quota_quantity_error'));
        }

        //获取当前价格
        $current_price = intval(C('promotion_flash_price'));

        //获取该用户已有套餐
        $model_flash_quota = Model('p_flash_quota');
        $current_flash_quota= $model_flash_quota->getFlashQuotaCurrent($_SESSION['store_id']);
        $add_time = 86400 *30 * $flash_quota_quantity;
        if(empty($current_flash_quota)) {
            //生成套餐
            $param = array();
            $param['member_id'] = $_SESSION['member_id'];
            $param['member_name'] = $_SESSION['member_name'];
            $param['store_id'] = $_SESSION['store_id'];
            $param['store_name'] = $_SESSION['store_name'];
            $param['start_time'] = TIMESTAMP;
            $param['end_time'] = TIMESTAMP + $add_time;
            $model_flash_quota->addFlashQuota($param);
        } else {
            $param = array();
            $param['end_time'] = array('exp', 'end_time + ' . $add_time);
            $model_flash_quota->editFlashQuota($param, array('quota_id' => $current_flash_quota['quota_id']));
        }

        //记录店铺费用
        $this->recordStoreCost($current_price * $flash_quota_quantity, '购买闪购');

        $this->recordSellerLog('购买'.$flash_quota_quantity.'份闪购套餐，单价'.$current_price.$lang['nc_yuan']);

        showDialog(Language::get('flash_quota_add_success'),self::LINK_SPIKE_LIST,'succ');
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
        Tpl::showpage('store_promotion_flash.goods', 'null_layout');
    }

    /**
     * 闪购商品添加
     **/
    public function flash_goods_addOp() {
        $goods_id = intval($_POST['goods_id']);
        $flash_id = intval($_POST['flash_id']);
        $flash_price = floatval($_POST['flash_price']);

        $model_goods = Model('goods');
        $model_flash = Model('p_flash');
        $model_flash_goods = Model('p_flash_goods');

        $data = array();
        $data['result'] = true;

        $goods_info = $model_goods->getGoodsInfoByID($goods_id);
        if(empty($goods_info) || $goods_info['store_id'] != $_SESSION['store_id']) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }
        $flash_amount = intval($goods_info['goods_storage']);
        $flash_info = $model_flash->getFlashInfoByID($flash_id, $_SESSION['store_id']);
        if(!$flash_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }

        //检查商品是否已经参加同时段活动
        $condition = array();
        $condition['end_time'] = array('gt', $flash_info['start_time']);
        $condition['goods_id'] = $goods_id;
        $flash_goods = $model_flash_goods->getFlashGoodsExtendList($condition);
        if(!empty($flash_goods)) {
            $data['result'] = false;
            $data['message'] = '该商品已经参加了同时段活动';
            echo json_encode($data);die;
        }
        $condition = array();
        $condition['end_time'] = array('gt', $flash_info['start_time']);
        $condition['goods_id'] = $goods_id;
        $flash_goods = Model('p_time')->getInfo($condition);
        if(!empty($flash_goods)) {
            $data['result'] = false;
            $data['message'] = '该商品已经参加了同时段其它促销活动';
            echo json_encode($data);die;
        }

        //添加到活动商品表
        $param = array();
        $param['flash_id'] = $flash_info['flash_id'];
        $param['flash_name'] = $flash_info['flash_name'];
        $param['flash_title'] = $flash_info['flash_title'];
        $param['flash_explain'] = $flash_info['flash_explain'];
        $param['goods_id'] = $goods_info['goods_id'];
        $param['store_id'] = $goods_info['store_id'];
        $param['goods_name'] = $goods_info['goods_name'];
        $param['goods_price'] = $goods_info['goods_price'];
        $param['flash_price'] = $flash_price;
        $param['goods_image'] = $goods_info['goods_image'];
        $param['start_time'] = $flash_info['start_time'];
        $param['end_time'] = $flash_info['end_time'];
        $param['upper_limit'] = $flash_info['upper_limit'];
        $param['gc_id_1'] = $goods_info['gc_id_1'];
        $param['flash_amount'] = $flash_amount;

        $result = array();
        $flash_goods_info = $model_flash_goods->addFlashGoods($param);
        if($flash_goods_info) {
            Model('p_time')->addFlash($flash_goods_info);
            $data['message'] = '添加成功';
            $data['flash_goods'] = $flash_goods_info;
            $this->recordSellerLog('添加闪购商品，活动名称：'.$flash_info['flash_name'].'，商品名称：'.$goods_info['goods_name']);
        } else {
            $data['result'] = false;
            $data['message'] = L('param_error');
        }
        echo json_encode($data);die;
    }

    /**
     * 闪购商品价格修改
     **/
    public function flash_goods_price_editOp() {
        $flash_goods_id = intval($_POST['flash_goods_id']);
        $flash_price = floatval($_POST['flash_price']);

        $data = array();
        $data['result'] = true;

        $model_flash_goods = Model('p_flash_goods');

        $flash_goods_info = $model_flash_goods->getFlashGoodsInfoByID($flash_goods_id, $_SESSION['store_id']);
        if(!$flash_goods_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }
        $goods_info = Model('goods')->getGoodsInfoByID($flash_goods_info['goods_id']);
        $flash_amount = intval($goods_info['goods_storage']);

        $update = array();
        $update['flash_price'] = $flash_price;
        $update['flash_amount'] = $flash_amount;
        $condition = array();
        $condition['flash_goods_id'] = $flash_goods_id;
        $result = $model_flash_goods->editFlashGoods($update, $condition);

        if($result) {
            $flash_goods_info['flash_price'] = $flash_price;
            $flash_goods_info = $model_flash_goods->getFlashGoodsExtendInfo($flash_goods_info);
            $data['flash_price'] = $flash_goods_info['flash_price'];
            $data['flash_discount'] = $flash_goods_info['flash_discount'];

            // 添加对列修改商品促销价格
            QueueClient::push('updateGoodsPromotionPriceByGoodsId', $flash_goods_info['goods_id']);
            Model('p_time')->editFlashGoods($flash_goods_info);
            $this->recordSellerLog('闪购价格修改为：'.$flash_goods_info['flash_price'].'，商品名称：'.$flash_goods_info['goods_name']);
        } else {
            $data['result'] = false;
            $data['message'] = L('nc_common_op_succ');
        }
        echo json_encode($data);die;
    }

    /**
     * 闪购商品删除
     **/
    public function flash_goods_deleteOp() {
        $model_flash_goods = Model('p_flash_goods');
        $model_flash = Model('p_flash');

        $data = array();
        $data['result'] = true;

        $flash_goods_id = intval($_POST['flash_goods_id']);
        $flash_goods_info = $model_flash_goods->getFlashGoodsInfoByID($flash_goods_id);
        if(!$flash_goods_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }

        $flash_info = $model_flash->getFlashInfoByID($flash_goods_info['flash_id'], $_SESSION['store_id']);
        if(!$flash_info) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }

        if(!$model_flash_goods->delFlashGoods(array('flash_goods_id'=>$flash_goods_id))) {
            $data['result'] = false;
            $data['message'] = L('flash_goods_delete_fail');
            echo json_encode($data);die;
        }

        // 添加对列修改商品促销价格
        QueueClient::push('updateGoodsPromotionPriceByGoodsId', $flash_goods_info['goods_id']);
        Model('p_time')->delFlashGoods($flash_goods_info);
        $this->recordSellerLog('删除闪购商品，活动名称：'.$flash_info['flash_name'].'，商品名称：'.$flash_goods_info['goods_name']);
        echo json_encode($data);die;
    }
    
    /**
     * 上传图片
     *
     */
    private function upload_pic($flash_array) {
        $pic_array = array();
        $pic_array[1] = 'flash_brand';
        $pic_array[2] = 'flash_pic';
        $pic_array[3] = 'flash_banner';
        $pic_array[4] = 'flash_recommend_pic';
        $upload = new UploadFile();
        $dir = ATTACH_STORE.DS.$_SESSION['store_id'];
        $upload->set('default_dir',$dir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png'));
        foreach($pic_array as $pic) {
            if (!empty($_FILES[$pic]['name'])){
                $result = $upload->upfile($pic);
                if ($result){
                    if (!empty($flash_array[$pic])){//删除旧图片
                        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$_SESSION['store_id'].DS.$flash_array[$pic]);
                    }
                    $flash_array[$pic] = $_SESSION['store_id'].DS.$upload->file_name;
                    $upload->file_name = '';
                } else {
                    $flash_array[$pic] = '';
                }
            }
        }
        return $flash_array;
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
            1=>array('menu_key'=>'flash_list','menu_name'=>Language::get('promotion_active_list'),'menu_url'=>'index.php?act=store_promotion_flash&op=flash_list'),
        );
        switch ($menu_key){
            case 'flash_add':
                $menu_array[] = array('menu_key'=>'flash_add','menu_name'=>Language::get('promotion_join_active'),'menu_url'=>'index.php?act=store_promotion_flash&op=flash_add');
                break;
            case 'flash_edit':
                $menu_array[] = array('menu_key'=>'flash_edit','menu_name'=>'编辑活动','menu_url'=>'javascript:;');
                break;
            case 'flash_quota_add':
                $menu_array[] = array('menu_key'=>'flash_quota_add','menu_name'=>Language::get('promotion_buy_product'),'menu_url'=>'index.php?act=store_promotion_flash&op=flash_quota_add');
                break;
            case 'flash_manage':
                $menu_array[] = array('menu_key'=>'flash_manage','menu_name'=>Language::get('promotion_goods_manage'),'menu_url'=>'index.php?act=store_promotion_flash&op=flash_manage&flash_id='.$_GET['flash_id']);
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
