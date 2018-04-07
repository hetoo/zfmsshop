<?php
/**
 * 关联版式
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


defined('InShopNC') or exit ('Access Invalid!');
class store_chainControl extends BaseSellerControl {
    private $model_chain;
    public function __construct() {
        parent::__construct();
        if(!C('chain_allow')){
            if (!checkPlatformStore()) {
                showMessage('该功能只有平台自营店铺使用', urlShop('seller_center', 'index'), '', 'error');
            }
        }
        $this->model_chain = Model('chain');
    }

    public function indexOp() {
        $this->chain_listOp();
    }

    /**
     * 门店列表
     */
    public function chain_listOp() {
        $chain_list = $this->model_chain->getChainList(array('store_id' => $_SESSION['store_id']), '*', 10);
        Tpl::output('show_page', $this->model_chain->showpage());
        Tpl::output('chain_list', $chain_list);
        $this->profile_menu('chain_list', 'chain_list');
        Tpl::showpage('store_chain.list');
    }

    /**
     * 待审核门店列表
     */
    public function waitting_checkOp() {
        $chain_list = $this->model_chain->getChainList(array('store_id' => $_SESSION['store_id'],'chain_state'=>array('in',array(2,5))), '*', 10);
        Tpl::output('show_page', $this->model_chain->showpage());
        Tpl::output('chain_list', $chain_list);
        $this->profile_menu('chain_list', 'waitting_check');
        Tpl::showpage('store_chain.list');
    }

    /**
     * 待付款门店列表
     */
    public function waitting_payOp() {
        $chain_list = $this->model_chain->getChainList(array('store_id' => $_SESSION['store_id'],'chain_state'=>4), '*', 10);
        Tpl::output('show_page', $this->model_chain->showpage());
        Tpl::output('chain_list', $chain_list);
        $this->profile_menu('chain_list', 'waitting_pay');
        Tpl::showpage('store_chain.list');
    }
    
    /**
     * 添加门店
     */
    public function chain_addOp() {
        $store_info = Model('store')->getStoreInfoByID($_SESSION['store_id']);
        $chain_count = Model('chain')->getChainCount(array('store_id'=>$_SESSION['store_id']));
        if($chain_count >= $store_info['allow_chain_count']){
            showDialog("您最多可开启{$chain_count}个门店", '', 'error');
        }
        /**
         * 新增保存
         */
        if (chksubmit()){
            /**
             * 上传图片
             */
            $upload = new UploadFile();
            if (!empty($_FILES['chain_img']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$_SESSION['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_img');
                if ($result){
                    $_POST['chain_img'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            $upload = new UploadFile();
            if (!empty($_FILES['chain_banner']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$_SESSION['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_banner');
                if ($result){
                    $_POST['chain_banner'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            $upload = new UploadFile();
            if (!empty($_FILES['chain_logo']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$_SESSION['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_logo');
                if ($result){
                    $_POST['chain_logo'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }
            
            $insert = array();
            $insert['store_id']     = $_SESSION['store_id'];
            $insert['store_name']   = $_SESSION['store_name'];
            $insert['chain_user']   = $_POST['chain_user'];
            $insert['chain_pwd']    = md5($_POST['chain_pwd']);
            $insert['chain_name']   = $_POST['chain_name'];
            $insert['chain_img']    = $_POST['chain_img'];
            $insert['chain_banner']    = $_POST['chain_banner'];
            $insert['chain_logo']    = $_POST['chain_logo'];
            $insert['area_id_1']    = intval($_POST['area_id_1']);
            $insert['area_id_2']    = intval($_POST['area_id_2']);
            $insert['area_id_3']    = intval($_POST['area_id_3']);
            $insert['area_id_4']    = intval($_POST['area_id_4']);
            $insert['area_id']      = intval($_POST['area_id']);
            $insert['area_info']    = $_POST['area_info'];
            $insert['chain_address']= $_POST['chain_address'];
            $insert['chain_phone']  = $_POST['chain_phone'];
            $insert['chain_opening_hours']  = $_POST['chain_opening_hours'];
            $insert['chain_traffic_line']   = $_POST['chain_traffic_line'];
            $insert['chain_apply_time'] = TIMESTAMP;
            $insert['chain_cycle'] = intval($_POST['chain_cycle']);
            $insert['transport_areas'] = serialize(array());
            $insert['express_city'] = serialize(array());

            //门店状态处理
            if(checkPlatformStore()){
                $insert['is_own'] = 1;
                $insert['chain_state'] = 1;
                $insert['chain_time'] = TIMESTAMP;
            }else{
                $insert['is_own'] = 0;
                $insert['chain_state'] = 2;
                if(!C('chain_check_allow')){
                    if(floatval(C('chain_earnest_money')) == 0){
                        $insert['chain_state'] = 1;
                        $insert['chain_time'] = TIMESTAMP;
                    }else{
                        $insert['chain_state'] = 4;
                    }
                }
            }

            //处理门店地址坐标信息
            $area_info = explode(" ", $insert['area_info']);
            $city = $area_info[1];
            $area = $area_info[1] . $area_info[2];
            $address = $area . $insert['chain_address'];
            $location = getGeoByAddress($address, $city);
            if(!empty($location)){
                $insert['chain_lat'] = $location['location']['lat'];
                $insert['chain_lng'] = $location['location']['lng'];
            }

            $result = $this->model_chain->addChain($insert);
            if ($result) {
                showDialog('操作成功', urlShop('store_chain', 'index'), 'succ');
            } else {
                showDialog('操作失败', 'reload');
            }
        }
        $this->profile_menu('chain_add', 'chain_add');
        Tpl::showpage('store_chain.add');
    }
    
    /**
     * 编辑门店
     */
    public function chain_editOp() {
        $chain_id = $_GET['chain_id'];
        $chain_info = $this->model_chain->getChainInfo(array('chain_id' => $chain_id, 'store_id' => $_SESSION['store_id']));
        
        if (chksubmit()){
            /**
             * 上传图片
             */
            $upload = new UploadFile();
            if (!empty($_FILES['chain_img']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$_SESSION['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_img');
                if ($result){
                    $_POST['chain_img'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            $upload = new UploadFile();
            if (!empty($_FILES['chain_banner']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$_SESSION['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_banner');
                if ($result){
                    $_POST['chain_banner'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            $upload = new UploadFile();
            if (!empty($_FILES['chain_logo']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$_SESSION['store_id']);
                $upload->set('thumb_ext',   '');
                $upload->set('file_name','');
                $upload->set('ifremove',false);
                $result = $upload->upfile('chain_logo');
                if ($result){
                    $_POST['chain_logo'] = $upload->file_name;
                }else {
                    showDialog($upload->error);
                }
            }

            //删除旧图片
            if (!empty($_POST['chain_img']) && !empty($chain_info['chain_img'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$_SESSION['store_id'].DS.$chain_info['chain_img']);
            }

            if (!empty($_POST['chain_banner']) && !empty($chain_info['chain_banner'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$_SESSION['store_id'].DS.$chain_info['chain_banner']);
            }

            if (!empty($_POST['chain_logo']) && !empty($chain_info['chain_logo'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$_SESSION['store_id'].DS.$chain_info['chain_logo']);
            }
        
            $update = array();
            $update['chain_user']   = $_POST['chain_user'];
            if ($_POST['chain_pwd'] != '') {
                $update['chain_pwd']    = md5($_POST['chain_pwd']);
            }
            $update['chain_name']   = $_POST['chain_name'];
            if (!empty($_POST['chain_img'])) {
                $update['chain_img']    = $_POST['chain_img'];
            }
            if (!empty($_POST['chain_banner'])) {
                $update['chain_banner']    = $_POST['chain_banner'];
            }
            if (!empty($_POST['chain_logo'])) {
                $update['chain_logo']    = $_POST['chain_logo'];
            }
            $update['area_id_1']    = $_POST['area_id_1'];
            $update['area_id_2']    = $_POST['area_id_2'];
            $update['area_id_3']    = $_POST['area_id_3'];
            $update['area_id_4']    = $_POST['area_id_4'];
            $update['area_id']      = $_POST['area_id'];
            $update['area_info']    = $_POST['area_info'];
            $update['chain_address']= $_POST['chain_address'];
            $update['chain_phone']  = $_POST['chain_phone'];
            $update['chain_opening_hours']  = $_POST['chain_opening_hours'];
            $update['chain_traffic_line']   = $_POST['chain_traffic_line'];
            $update['chain_cycle'] = intval($_POST['chain_cycle']);

            //处理门店地址坐标信息
            if($chain_info['chain_lat'] == 0 || $chain_info['area_info'] != $update['area_info'] || $chain_info['chain_address'] != $update['chain_address']){
                $area_info = explode(" ", $update['area_info']);
                $city = $area_info[1];
                $area = $area_info[1] . $area_info[2];
                $address = $area . $update['chain_address'];
                $location = getGeoByAddress($address, $city);
                if(!empty($location)){
                    $update['chain_lat'] = $location['location']['lat'];
                    $update['chain_lng'] = $location['location']['lng'];
                }
            }
            $result = $this->model_chain->editChain($update, array('chain_id' => $chain_id, 'store_id' => $_SESSION['store_id']));
            if ($result) {
                showDialog('编辑成功', urlShop('store_chain', 'index'), 'succ');
            } else {
                showDialog('编辑失败', 'reload');
            }
        }
        
        Tpl::output('chain_info', $chain_info);
        $this->profile_menu('chain_edit', 'chain_edit');
        Tpl::showpage('store_chain.add');
    }
    /**
     * 删除门店
     */
    public function chain_delOp() {
        $chain_id = intval($_GET['chain_id']);
        if ($chain_id <= 0) {
            showDialog('参数错误', '', 'error');
        }
        $result = $this->model_chain->delChain(array('chain_id' => $chain_id, 'store_id' => $_SESSION['store_id'],'chain_state'=>array('in',array(0,3))));
        if ($result) {
            //删除相关联的收货地址
            Model('address')->delAddress(array('dlyp_id' => $chain_id));
            //获取保证金支付信息
            $model = Model('earnest_money');
            $m_info = $model->getEarnestMoneyInfo(array('etm_chain_id'=>$chain_id,'etm_member_id'=>$_SESSION['member_id']));
            if(!empty($m_info)){ //扣减账户保证金金额
                $m_data = array();
                $m_data['member_id'] = $_SESSION['member_id'];
                $m_data['member_name'] = $_SESSION['member_name'];
                $m_data['amount'] = $m_info['etm_amount'];
                $m_data['etm_sn'] = $m_info['etm_sn'];
                $model->changeEarnestMoney('close_chain',$m_data);
            }
            showDialog('删除成功', urlShop('store_chain', 'index'), 'succ');
        } else {
            showDialog('删除失败', 'reload');
        }
    }

    /**
     * 关闭门店
     */
    public function chain_closeOp(){
        $chain_id = $_GET['chain_id'];
        if (!preg_match('/^[\d,]+$/i', $chain_id)) {
            showDialog(L('wrong_argument'), '', 'error');
        }
        $chain_id = explode(',', $chain_id);
        $result = $this->model_chain->editChain(array('chain_state'=>0),array('chain_id' => array('in', $chain_id), 'store_id' => $_SESSION['store_id'],'chain_state'=>array('in',array(1,2,3,4))));
        if ($result) {
            showDialog('关闭成功', 'reload', 'succ');
        } else {
            showDialog('关闭失败', '','error');
        }
    }
    
    /**
     * ajax验证用户名是否存在
     */
    public function check_userOp() {
        $where = array();
        if ($_GET['chain_user'] != '') {
            $where['chain_user'] = $_GET['chain_user'];
        }
        if ($_GET['no_id'] != '') {
            $where['chain_id'] = array('neq', $_GET['no_id']);
        }
        $chain_info = $this->model_chain->getChainInfo($where);
        if (empty($chain_info)) {
            echo 'true';die;
        } else {
            echo 'false';die;
        }
    }

    /**
     * 支付门店保证金
     */
    public function chain_payOp(){
        $chain_id = intval($_GET['chain_id']);
        if($chain_id <= 0){
            showDialog(L('wrong_argument'), '', 'error');
        }
        $chain_info = $this->model_chain->getChainInfo(array('chain_id'=>$chain_id,'chain_state'=>array('in',array(4,6))));
        unset($chain_info['chain_pwd']);

        //获取当前店铺的门店保证金
        $model_emoney = Model('earnest_money');
        $chain_pay_info = (array)$model_emoney->getEarnestMoneyInfo(array('etm_chain_id'=>$chain_id));
        if(!empty($chain_pay_info)){
            $earnest_money = $chain_pay_info['etm_amount'];
        }else{
            $store_info = Model('store')->getStoreInfoByID($chain_info['store_id']);
            $earnest_money = $store_info['earnest_money'] > 0 ? $store_info['earnest_money'] : C('chain_earnest_money');
        }
        if($earnest_money <= 0){
            $this->model_chain->editChain(array('chain_state'=>1),array('chain_id'=>$chain_id));
            showDialog('当前门店不需要支付保证金', '', 'error');
        }
        Tpl::output('earnest_money',$earnest_money);
        Tpl::output('store_info',$store_info);

        $model_payment = Model('payment');
        $condition = array();
        $payment_list = (array)$model_payment->getPaymentOpenList($condition);
        if (!empty($payment_list)) {
            unset($payment_list['offline']);
            unset($payment_list['predeposit']);
        }
        Tpl::output('payment_list',$payment_list);
        $this->profile_menu('chain_pay', 'chain_pay');
        Tpl::output('chain_info',$chain_info);
        Tpl::showpage('store_chain.pay');
    }
    public function pay_saveOp() {
        $chain_id = intval($_POST['chain_id']);
        if($chain_id <= 0){
            showDialog('参数错误','','error');
        }
        $model_emoney = Model('earnest_money');
        $chain_pay_info = $model_emoney->getEarnestMoneyInfo(array('etm_chain_id'=>$chain_id));
        $pay_code = trim($_POST['payment_code']);
        $param = array();
        $member_id = intval($_SESSION['member_id']);
        if(empty($chain_pay_info)){
            $chain_info = $this->model_chain->getChainInfo(array('chain_id'=>$chain_id));
            $store_info = Model('store')->getStoreInfoByID($chain_info['store_id']);
            $earnest_money = $store_info['earnest_money'] > 0 ? $store_info['earnest_money'] : C('chain_earnest_money');
            $param['etm_member_id'] = $member_id;
            $param['etm_member_name'] = $_SESSION['member_name'];
            $param['etm_amount'] = $earnest_money;
            $param['etm_add_time'] = TIMESTAMP;
            $param['etm_sn'] = $this->_makePaySn($member_id);
            $param['etm_chain_id'] = $chain_id;
            $res = $model_emoney->addEarnestMoney($param);
        }else{
            $param['etm_sn'] = $chain_pay_info['etm_sn'];
            $res = true;
        }
        if($res){
            $html = '<html><head></head><body>';
            $html .= '<form method="post" name="E_FORM" action="'.urlShop('payment','chain_pay').'">';
            $html .= "<input type='hidden' name='payment_code' value='".$pay_code."' />";
            $html .= "<input type='hidden' name='etm_sn' value='".$param['etm_sn']."' />";
            $html .= '</form><script type="text/javascript">document.E_FORM.submit();</script>';
            $html .= '</body></html>';
            echo $html;
            exit;
        }else{
            showMessage('支付失败','index.php?act=store_chain');
        }

        @header('location: index.php?act=store_chain');
    }

    
    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @param array     $array      附加菜单
     * @return
     */
    private function profile_menu($menu_type,$menu_key,$array=array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'chain_list':
                $menu_array = array(
                    array('menu_key' => 'chain_list', 'menu_name' => '门店列表', 'menu_url' => urlShop('store_chain', 'chain_list')),
                    array('menu_key' => 'waitting_check', 'menu_name' => '待审核门店', 'menu_url' => urlShop('store_chain', 'waitting_check')),
                    array('menu_key' => 'waitting_pay', 'menu_name' => '待付款门店', 'menu_url' => urlShop('store_chain', 'waitting_pay')),
                );
                break;
            case 'chain_add':
                $menu_array = array(
                    array('menu_key' => 'chain_list', 'menu_name' => '门店列表', 'menu_url' => urlShop('store_chain', 'chain_list')),
                    array('menu_key' => 'waitting_check', 'menu_name' => '待审核门店', 'menu_url' => urlShop('store_chain', 'waitting_check')),
                    array('menu_key' => 'waitting_pay', 'menu_name' => '待付款门店', 'menu_url' => urlShop('store_chain', 'waitting_pay')),
                    array('menu_key' => 'chain_add', 'menu_name' => '添加门店', 'menu_url' => urlShop('store_chain', 'chain_add'))
                );
                break;
            case 'chain_edit':
                $menu_array = array(
                    array('menu_key' => 'chain_list', 'menu_name' => '门店列表', 'menu_url' => urlShop('store_chain', 'chain_list')),
                    array('menu_key' => 'waitting_check', 'menu_name' => '待审核门店', 'menu_url' => urlShop('store_chain', 'waitting_check')),
                    array('menu_key' => 'waitting_pay', 'menu_name' => '待付款门店', 'menu_url' => urlShop('store_chain', 'waitting_pay')),
                    array('menu_key' => 'chain_add', 'menu_name' => '添加门店', 'menu_url' => urlShop('store_chain', 'chain_add')),
                    array('menu_key' => 'chain_edit', 'menu_name' => '编辑门店', 'menu_url' => urlShop('store_chain', 'chain_edit'))
                );
                break;
            case 'chain_pay':
                $menu_array = array(
                    array('menu_key' => 'chain_pay', 'menu_name' => '保证金支付', 'menu_url' => urlShop('store_chain', 'chain_pay')),
                );
                break;
        }
        if(!empty($array)) {
            $menu_array[] = $array;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }

    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    private function _makePaySn($member_id) {
        return mt_rand(10,99)
            . sprintf('%010d',time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%03d', (int) $member_id % 1000);
    }
}
