<?php
/**
 * 门店管理
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class chainControl extends SystemControl{

    const EXPORT_SIZE = 1000;

    private $_links = array(
        array('url'=>'act=chain&op=chainlist','text'=>'门店管理'),
        array('url'=>'act=chain&op=chain_joinin','text'=>'待审核门店'),
        array('url'=>'act=chain&op=chain_setting','text'=>'运营设置')
    );


    public function __construct()
    {
        parent::__construct();
        if(C('chain_earnest_money') > 0){
            $this->_links[] = array('url'=>'act=chain&op=chain_unpay','text'=>'待付款门店');
        }
    }

    /**
    * 默认操作列出门店
    */
    public function indexOp(){
        $this->chainlistOp();
    }

    /**
     * 门店列表
     */
    public function chainlistOp(){
        Tpl::output('top_link',$this->sublink($this->_links,'chainlist'));
        Tpl::showpage('chain.index');
    }

    /***
     * 门店申请列表
     */
    public function chain_joininOp(){
        Tpl::output('top_link',$this->sublink($this->_links,'chain_joinin'));
        Tpl::showpage('chain_joinin');
    }

    public function chain_unpayOp(){
        Tpl::output('top_link',$this->sublink($this->_links,'chain_unpay'));
        Tpl::showpage('chain_unpay');
    }

    /**
     * 门店运营设置
     */
    public function chain_settingOp(){
        $model_setting = Model('setting');
        if(chksubmit()){
            $update_array = array();
            $update_array['chain_allow'] = $_POST['chain_allow'];
            $update_array['chain_check_allow'] = $_POST['chain_check_allow'];
            $update_array['chain_earnest_money'] = intval($_POST['chain_earnest_money']);
            $update_array['chain_hot_keyword'] = trim($_POST['chain_hot_keyword']);
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                $this->log('编辑门店设置',1);
                showMessage(L('nc_common_save_succ'));
            }else {
                showMessage(L('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('top_link',$this->sublink($this->_links,'chain_setting'));
        Tpl::output('list_setting',$list_setting);
        Tpl::showpage('chain.setting');
    }

    /**
     * 门店入驻详细信息&审核
     */
    public function chain_joinin_detailOp(){
        $chain_id = intval($_REQUEST['chain_id']);
        if($chain_id <= 0){
            showMessage('参数错误');
        }

        $model_chain = Model('chain');
        $condition = array('chain_id'=>$chain_id);
        $chain_info = $model_chain->getChainInfo($condition, '*');
        if(chksubmit()){
            $param = array();
            $param['chain_check_info'] = trim($_POST['chain_check_info']);
            //审核结果处理
            switch(trim($_POST['verify_type'])){
                case 'pass':
                    if(intval($chain_info['chain_state']) == 2){
                        $param['chain_state'] = 4;
                        if(floatval(C('chain_earnest_money')) == 0){
                            $param['chain_state'] = 1;
                        }
                    }else if(intval($chain_info['chain_state']) == 5){
                        $param['chain_state'] = 1;
                    }
                    break;
                case 'fail':
                    $param['chain_state'] = 3;
                    break;
            }
        }
        //店铺信息
        $model_store = Model('store');
        $store_info = $model_store->getStoreInfoByID($chain_info['store_id']);
        //店铺等级
        $model_grade = Model('store_grade');
        $grade_info = $model_grade->getOneGrade($store_info['grade_id']);
        Tpl::output('grade_info',$grade_info);
        Tpl::output('store_info',$store_info);

        unset($chain_info['chain_pwd']);
        Tpl::output('chain_info',$chain_info);
        Tpl::showpage('chain_joinin_detail');
    }

    /**
     * 门店编辑
     */
    public function chain_editOp(){
        $chain_id = intval($_REQUEST['chain_id']);
        if($chain_id <= 0){
            showMessage('参数错误');
        }

        $model_chain = Model('chain');
        $condition = array('chain_id'=>$chain_id);
        $chain_info = $model_chain->getChainInfo($condition, '*');
        if(chksubmit()){
            /**
             * 上传图片
             */
            $upload = new UploadFile();
            if (!empty($_FILES['chain_img']['name'])){
                $upload->set('default_dir', ATTACH_CHAIN.DS.$chain_info['store_id']);
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
                $upload->set('default_dir', ATTACH_CHAIN.DS.$chain_info['store_id']);
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
                $upload->set('default_dir', ATTACH_CHAIN.DS.$chain_info['store_id']);
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
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$chain_info['store_id'].DS.$chain_info['chain_img']);
            }

            if (!empty($_POST['chain_banner']) && !empty($chain_info['chain_banner'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$chain_info['store_id'].DS.$chain_info['chain_banner']);
            }

            if (!empty($_POST['chain_logo']) && !empty($chain_info['chain_logo'])){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_CHAIN.DS.$chain_info['store_id'].DS.$chain_info['chain_logo']);
            }
            $update = array();
            if (!empty($_POST['chain_img'])) {
                $update['chain_img']    = $_POST['chain_img'];
            }
            if (!empty($_POST['chain_banner'])) {
                $update['chain_banner']    = $_POST['chain_banner'];
            }
            if (!empty($_POST['chain_logo'])) {
                $update['chain_logo']    = $_POST['chain_logo'];
            }
            $update['chain_name']    = trim($_POST['chain_name']);
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
            $update['chain_state'] = intval($_POST['chain_state']);
            $update['chain_close_info'] = $_POST['chain_close_info'];


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
            $result = $model_chain->editChain($update, array('chain_id' => $chain_id, 'store_id' => $chain_info['store_id']));
            if ($result) {
                showMessage('编辑成功','index.php?act=chain&op=index','succ');
            } else {
                showMessage('编辑失败', 'reload');
            }
        }
        $this->_getChianTransportAreas($chain_info);
        $chain_info['transport_areas'] = $chain_info['transport_areas'] !='' ? unserialize($chain_info['transport_areas']) : array();
        unset($chain_info['chain_pwd']);
        Tpl::output('chain_info',$chain_info);
        Tpl::showpage('chain.edit');
    }

    /**
     * 读取门店XML数据[管理]
     */
    public function get_xml_chain_listOp(){
        $chain_model = Model('chain');
        // 设置页码参数名称
        $condition = array();
        $condition['chain_state'] = array('in',array('0','1','3'));
        if ($_GET['chain_name'] != '') {
            $condition['chain_name'] = array('like', '%' . $_GET['chain_name'] . '%');
        }
        if ($_GET['chain_user'] != '') {
            $condition['chain_user'] = array('like', '%' . $_GET['chain_user'] . '%');
        }
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_GET['is_own'] != '') {
            $condition['is_own'] = $_GET['is_own'];
        }
        if ($_GET['chain_state'] != '') {
            $condition['chain_state'] = $_GET['chain_state'];
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('chain_id','chain_name','chain_user','chain_state','is_transport','is_forward_order','is_self_take','is_collection','chain_time','chain_close_time');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        $fields = "chain_id,store_id,chain_user,chain_name,chain_img,area_info,chain_address,chain_state,chain_logo,is_transport,is_forward_order,is_self_take,is_collection,chain_time,chain_close_time,store_name,is_own";

        //门店列表
        $chain_list =$chain_model->getChainList($condition, $fields, $page, $order);

        $data = array();
        $data['now_page'] = $chain_model->shownowpage();
        $data['total_num'] = $chain_model->gettotalnum();

        foreach ($chain_list as $value) {
            $param = array();
            $chain_state = $this->_getChainState($value['chain_state']);
            $operation = "<a class='btn green' href='index.php?act=chain&op=chain_joinin_detail&chain_id=".$value['chain_id']."'><i class='fa fa-list-alt'></i>查看</a><span class='btn'><em><i class='fa fa-cog'></i>" . L('nc_set') . " <i class='arrow'></i></em><ul><li><a href='index.php?act=chain&op=chain_edit&chain_id=" . $value['chain_id'] . "'>编辑门店信息</a></li>";

            $operation .= "</ul></span>";
            $param['operation'] = $operation;
            $param['chain_id'] = $value['chain_id'];
            $chain_name = "<a href='". urlShop('show_chain', 'index', array('chain_id' => $value['chain_id'])) ."' target='blank'>";
            $chain_name .= $value['chain_name'] . "<i class='fa fa-external-link ' title='新窗口打开'></i></a>";
            $param['chain_name'] = $chain_name;
            $param['is_own'] = $value['is_own'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['chain_user'] = $value['chain_user'];
            $param['chain_img'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getChainImage($value['chain_img'],$value['store_id']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['chain_logo'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getChainImage($value['chain_logo'], $value['store_id']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['chain_state'] = $chain_state;
            $param['is_transport'] = $value['is_transport'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_forward_order'] = $value['is_forward_order'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_self_take'] = $value['is_self_take'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_collection'] = $value['is_collection'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['chain_time'] = date('Y-m-d', $value['chain_time']);
            $param['chain_close_time'] = $value['chain_close_time']?date('Y-m-d', $value['chain_close_time']):'--';
            $param['area_info'] = $value['area_info'];
            $param['chain_address'] = $value['chain_address'];
            $param['store_name'] = $value['store_name'];
            $data['list'][$value['chain_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 读取门店XML数据[待审核门店]
     */
    public function get_xml_chain_check_listOp(){
        $chain_model = Model('chain');
        // 设置页码参数名称
        $condition = array();
        $condition['chain_state'] = array('in',array('2','5'));
        if ($_GET['chain_name'] != '') {
            $condition['chain_name'] = array('like', '%' . $_GET['chain_name'] . '%');
        }
        if ($_GET['chain_user'] != '') {
            $condition['chain_user'] = array('like', '%' . $_GET['chain_user'] . '%');
        }
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('chain_id','chain_name','chain_user','is_transport','is_forward_order','is_self_take','is_collection','chain_apply_time');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        $fields = "chain_id,store_id,chain_user,chain_name,chain_img,area_info,chain_address,chain_state,chain_logo,is_transport,is_forward_order,is_self_take,is_collection,chain_time,chain_close_time,store_name";

        //门店列表
        $chain_list =$chain_model->getChainList($condition, $fields, $page, $order);

        $data = array();
        $data['now_page'] = $chain_model->shownowpage();
        $data['total_num'] = $chain_model->gettotalnum();

        foreach ($chain_list as $value) {
            $param = array();
            $operation = "<a class='btn orange' href='index.php?act=chain&op=chain_joinin_detail&chain_id=".$value['chain_id']."'><i class='fa fa-check-circle'></i>审核</a>";
            $param['operation'] = $operation;
            $param['chain_id'] = $value['chain_id'];
            $param['chain_name'] = $value['chain_name'];
            $param['chain_user'] = $value['chain_user'];
            $param['chain_img'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getChainImage($value['chain_img'],$value['store_id']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['chain_logo'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getChainImage($value['chain_logo'], $value['store_id']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['is_transport'] = $value['is_transport'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_forward_order'] = $value['is_forward_order'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_self_take'] = $value['is_self_take'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_collection'] = $value['is_collection'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['chain_apply_time'] = date('Y-m-d H:i:s', $value['chain_apply_time']);
            $param['area_info'] = $value['area_info'];
            $param['chain_address'] = $value['chain_address'];
            $param['store_name'] = $value['store_name'];
            $data['list'][$value['chain_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 读取门店XML数据[待付款门店]
     */
    public function get_xml_chain_unpay_listOp(){
        $chain_model = Model('chain');
        // 设置页码参数名称
        $condition = array();
        $condition['chain_state'] = 4;
        if ($_GET['chain_name'] != '') {
            $condition['chain_name'] = array('like', '%' . $_GET['chain_name'] . '%');
        }
        if ($_GET['chain_user'] != '') {
            $condition['chain_user'] = array('like', '%' . $_GET['chain_user'] . '%');
        }
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('chain_id','chain_name','chain_user','is_transport','is_forward_order','is_self_take','is_collection','chain_apply_time');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        $fields = "chain_id,store_id,chain_user,chain_name,chain_img,area_info,chain_address,chain_state,chain_logo,is_transport,is_forward_order,is_self_take,is_collection,chain_time,chain_close_time,store_name";

        //门店列表
        $chain_list =$chain_model->getChainList($condition, $fields, $page, $order);

        $data = array();
        $data['now_page'] = $chain_model->shownowpage();
        $data['total_num'] = $chain_model->gettotalnum();

        foreach ($chain_list as $value) {
            $param = array();
            $operation = "<a class='btn green' href='index.php?act=chain&op=chain_joinin_detail&chain_id=".$value['chain_id']."'><i class='fa fa-list-alt'></i>查看</a>";
            $param['operation'] = $operation;
            $param['chain_id'] = $value['chain_id'];
            $param['chain_name'] = $value['chain_name'];
            $param['chain_user'] = $value['chain_user'];
            $param['chain_img'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getChainImage($value['chain_img'],$value['store_id']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['chain_logo'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getChainImage($value['chain_logo'], $value['store_id']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['is_transport'] = $value['is_transport'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_forward_order'] = $value['is_forward_order'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_self_take'] = $value['is_self_take'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_collection'] = $value['is_collection'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['chain_apply_time'] = date('Y-m-d H:i:s', $value['chain_apply_time']);
            $param['area_info'] = $value['area_info'];
            $param['chain_address'] = $value['chain_address'];
            $param['store_name'] = $value['store_name'];
            $data['list'][$value['chain_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 导出门店CSV列表
     */
    public function export_csvOp(){
        $chain_model = Model('chain');
        // 设置页码参数名称
        $condition = array();
        $condition['chain_state'] = array('in',array('0','1','3'));
        if ($_GET['chain_name'] != '') {
            $condition['chain_name'] = array('like', '%' . $_GET['chain_name'] . '%');
        }
        if ($_GET['chain_user'] != '') {
            $condition['chain_user'] = array('like', '%' . $_GET['chain_user'] . '%');
        }
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_GET['is_own'] != '') {
            $condition['is_own'] = $_GET['is_own'];
        }
        if ($_GET['chain_state'] != '') {
            $condition['chain_state'] = $_GET['chain_state'];
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('chain_id','chain_name','chain_user','chain_state','is_transport','is_forward_order','is_self_take','is_collection','chain_time','chain_close_time');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $fields = "chain_id,store_id,chain_user,chain_name,chain_img,area_info,chain_address,chain_state,chain_logo,is_transport,is_forward_order,is_self_take,is_collection,chain_time,chain_close_time,store_name";

        if (!is_numeric($_GET['curpage'])){
            $count = $chain_model->getChainCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=chain&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $chain_list = $chain_model->getChainList($condition, $fields, null, $order, $limit);
        $this->createCsv($chain_list);
    }

    /**
     * 生成csv文件
     */
    private function createCsv($chain_list) {

        $data = array();
        foreach ($chain_list as $value) {
            $param = array();
            $chain_state = $this->_getChainState($value['chain_state']);
            $param['chain_id'] = $value['chain_id'];
            $param['chain_name'] = $value['chain_name'];
            $param['is_own'] = $value['is_own'] ==  '1' ? '是' : '否';
            $param['chain_user'] = $value['chain_user'];
            $param['chain_state'] = $chain_state;
            $param['is_transport'] = $value['is_transport'] ==  '1' ? '是' : '否';
            $param['is_forward_order'] = $value['is_forward_order'] ==  '1' ? '是' : '否';
            $param['is_self_take'] = $value['is_self_take'] ==  '1' ? '是' : '否';
            $param['is_collection'] = $value['is_collection'] ==  '1' ? '是' : '否';
            $param['chain_time'] = date('Y-m-d', $value['chain_time']);
            $param['chain_close_time'] = $value['chain_close_time'] ? date('Y-m-d', $value['chain_close_time']) : '--';
            $param['area_info'] = $value['area_info'];
            $param['chain_address'] = $value['chain_address'];
            $param['store_name'] = $value['store_name'];
            $data[$value['chain_id']] = $param;
        }

        $header = array(
            'chain_id' => '门店ID',
            'chain_name' => '门店名称',
            'is_own' => '自营',
            'chain_user' => '门店账号',
            'chain_state' => '当前状态',
            'is_transport' => '支持配送',
            'is_forward_order' => '转接订单',
            'is_self_take' => '支持自提',
            'is_collection' => '支持代收货',
            'chain_time' => '开店时间',
            'chain_close_time' => '关闭时间',
            'area_info' => '所在地区',
            'chain_address' => '详细地址',
            'store_name' => '所属店铺'
        );
        \Shopnc\Lib::exporter()->output('chain_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }




    //获取门店状态
    private function _getChainState($state){
        $return = '正常';
        switch($state){
            case 0:
                $return = "关闭";
                break;
            case 2:
                $return = "审核中";
                break;
            case 3:
                $return = "未通过";
                break;
            case 4:
                $return = "待付保证金";
                break;
            case 5:
                $return = "待保证金审核";
                break;
        }
        return $return;
    }

    //获取门店配送区域数据
    private function _getChianTransportAreas(& $chain_info){
        $chain_info['transport_area_arr'] = array();
        $model_area = Model('area');
        $area_all_info = $model_area->getAreas();
        $area_children_arr = $area_all_info['children'];
        if ($chain_info['area_id'] == $chain_info['area_id_3'] || $chain_info['area_id'] == $chain_info['area_id_4']) {
            $area_ids_3 = $area_children_arr[$chain_info['area_id_2']];
            $chain_info['transport_area_arr'] = $this->_makeAreaData($area_ids_3);
        } elseif ($chain_info['area_id'] == $chain_info['area_id_2']) {
            $area_ids_2 = $area_children_arr[$chain_info['area_id_1']];
            $chain_info['transport_area_arr'] = $this->_makeAreaData($area_ids_2);
        } else {
            $chain_info['transport_area_arr'] = $this->_makeAreaData(array($chain_info['area_id_1']));
        }
    }
    private function _makeAreaData($area_ids){
        $model_area = Model('area');
        $area_list = $model_area->getAreaNames();
        $return_data = array();
        foreach($area_ids as $v){
            $tmp_area = array();
            $tmp_area['area_id'] = $v;
            $tmp_area['area_name'] = $area_list[$v];
            $return_data[] = $tmp_area;
        }
        return $return_data;
    }
}