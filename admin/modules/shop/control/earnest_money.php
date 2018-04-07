<?php
/**
 * 保证金管理
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class earnest_moneyControl extends SystemControl{

    const EXPORT_SIZE = 1000;
    private $_links = array(
        array('url'=>'act=earnest_money&op=index','text'=>'保证金支付记录'),
        array('url'=>'act=earnest_money&op=log_list','text'=>'保证金明细'),
    );

    function __construct()
    {
        parent::__construct();
    }

    public function indexOp(){
        Tpl::output('top_link',$this->sublink($this->_links,'index'));
        Tpl::showpage('earnest_money.list');
    }

    public function get_xmlOp(){
        $model = Model('earnest_money');
        $condition  = array();
        if (strlen($q = trim((string) $_REQUEST['etm_member_name']))) {
            $condition['etm_member_name'] = array('like', '%' . $q . '%');
        }
        if (($q = (int) $_REQUEST['etm_member_id']) > 0) {
            $condition['etm_member_id'] = $q;
        }
        if (($q = (int) $_REQUEST['etm_payment_state']) > 0) {
            $condition['etm_payment_state'] = $q;
        }

        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['etm_add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

        if ($_POST['query'] != '') {
            if($_POST['qtype'] == 'etm_member_name'){
                $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
            }else{
                $condition[$_POST['qtype']] = $_POST['query'];
            }
        }

        $sort_fields = array('etm_id','etm_member_id','etm_member_name','etm_payment_code','etm_amount','etm_payment_state','etm_sn','etm_trade_sn');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }

        $_list = $model->getEarnestMoneyList($condition,$_POST['rp'],'*',$order);
        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        foreach ($_list as $value) {
            $param = array();
            $operation_detail = '';
            $param['operation'] = "<a class='btn blue' href='index.php?act=earnest_money&op=earnest_edit&etm_id=" . $value['etm_id'] . "'><i class='fa fa-list-alt'></i>查看</a>";
            if($value['etm_payment_state'] == 0){
                $operation_detail .= "<li><a href=\"index.php?act=earnest_money&op=earnest_edit&state_type=receive_pay&etm_id={$value['etm_id']}\">确认收款</a></li>";
            }
            if ($operation_detail) {
                $param['operation'] .= "<span class='btn'><em><i class='fa fa-cog'></i>设置 <i class='arrow'></i></em><ul>{$operation_detail}</ul>";
            }
            $param['etm_id'] = $value['etm_id'];
            $param['etm_content'] = $value['etm_id']?'开通门店,门店编号:'.$value['etm_id']:'店铺入驻';
            $param['etm_sn'] = $value['etm_sn'];
            $param['etm_add_time'] = date('Y-m-d', $value['etm_add_time']);
            $param['etm_amount'] = $value['etm_amount'];
            $param['etm_payment_state'] = $value['etm_payment_state'] ? '<span class="yes"><i class="fa fa-check-circle"></i>已支付</span>' : '<span class="no"><i class="fa fa-ban"></i>未支付</span>';
            $param['etm_payment_name'] = $value['etm_payment_name'];
            $param['trade_sn'] = $value['etm_trade_sn'];
            $param['etm_payment_time'] = date('Y-m-d', $value['etm_payment_time']);
            $param['etm_member_id'] = $value['etm_member_id'];
            $param['etm_member_name'] = $value['etm_member_name'];
            $data['list'][$value['etm_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 添加保证金记录
     */
    public function earnest_addOp(){
        if(chksubmit()){
            $param = array();
            $param['etm_sn'] = trim($_POST['etm_sn']);
            $param['etm_member_name'] = trim($_POST['member_name']);
            $param['etm_amount'] = floatval($_POST['etm_amount']);
            $param['etm_add_time'] = TIMESTAMP;
            $param['etm_chain_id'] = intval($_POST['etm_chain_id']);
            $param['etm_payment_state'] = intval($_POST['etm_payment_state']);
            $param['etm_member_id'] = $this->_checkChainForMember($param['etm_member_name'],$param['etm_chain_id']);
            $param['etm_admin'] = $this->admin_info['name'];

            if($param['etm_payment_state'] > 0){
                $param['etm_payment_code'] = trim($_POST['etm_payment_code']);
                $param['etm_payment_name'] = trim($_POST['etm_payment_name']);
                $param['etm_trade_sn'] = trim($_POST['trade_no']);
                $param['etm_payment_time'] = strtotime(trim($_POST['payment_time']));
            }
            $model = Model('earnest_money');
            $res = $model->addEarnestMoney($param);
            if($res){
                if($param['etm_payment_state'] > 0){ //添加保证金日志
                    $data_param = array();
                    $data_param['member_id'] = $param['etm_member_id'];
                    $data_param['member_name'] = $param['etm_member_name'];
                    $data_param['amount'] = $param['etm_amount'];
                    $data_param['etm_resion'] = '添加缴费记录，订单号：'.$param['etm_sn'];
                    $model->changeEarnestMoney('admin_addition',$data_param);
                }
                showMessage('添加成功','index.php?act=earnest_money');
            }else{
                showMessage('添加失败');
            }
        }
        //获取付款方式
        $payment_list = Model('payment')->getPaymentOpenList();
        //去掉预存款和货到付款
        foreach ($payment_list as $key => $value){
            if ($value['payment_code'] == 'predeposit' || $value['payment_code'] == 'offline') {
                unset($payment_list[$key]);
            }
        }
        Tpl::output('payment_list',$payment_list);
        $this->_links[] = array('url'=>'act=earnest_money&op=earnest_add','text'=>'添加支付记录');
        Tpl::output('top_link',$this->sublink($this->_links,'earnest_add'));
        Tpl::showpage('earnest_money.add');
    }

    /**
     * 查看编辑保证金记录
     */
    public function earnest_editOp(){
        $etm_id = $_REQUEST['etm_id'];
        if($etm_id <= 0){
            showMessage('参数错误','index.php?act=earnest_money');
        }
        $model = Model('earnest_money');
        //获取保证金信息
        $info = $model->getEarnestMoneyInfo(array('etm_id'=>$etm_id));

        $state_type = '';
        $text = '查看记录';
        if($_GET['state_type'] != ''){
            $state_type = $_GET['state_type'];
            $text = '编辑支付记录';
        }

        if($info['etm_payment_state'] > 0 && $state_type != ''){
            showMessage('无权编辑该记录','index.php?act=earnest_money');
        }
        if(chksubmit()){
            $param = array();
            $param['etm_payment_state'] = intval($_POST['etm_payment_state']);
            $param['etm_admin'] = $this->admin_info['name'];
            if($param['etm_payment_state'] > 0){
                $param['etm_payment_code'] = trim($_POST['etm_payment_code']);
                $param['etm_payment_name'] = trim($_POST['etm_payment_name']);
                $param['etm_trade_sn'] = trim($_POST['trade_no']);
                $param['etm_payment_time'] = strtotime(trim($_POST['payment_time']));
            }
            $model = Model('earnest_money');
            $res = $model->editEarnestMoney($param,array('etm_id'=>$etm_id));
            if($res){
                if($param['etm_payment_state'] > 0){ //添加保证金日志
                    $data_param = array();
                    $data_param['member_id'] = $info['etm_member_id'];
                    $data_param['member_name'] = $info['etm_member_name'];
                    $data_param['amount'] = $info['etm_amount'];
                    $data_param['etm_resion'] = '确认收到保证金，订单号：'.$param['etm_sn'];
                    $model->changeEarnestMoney('admin_addition',$data_param);
                }
                showMessage('更改成功','index.php?act=earnest_money');
            }else{
                showMessage('更改失败');
            }
        }
        //获取付款方式
        $payment_list = Model('payment')->getPaymentOpenList();
        //去掉预存款和货到付款
        foreach ($payment_list as $key => $value){
            if ($value['payment_code'] == 'predeposit' || $value['payment_code'] == 'offline') {
                unset($payment_list[$key]);
            }
        }
        Tpl::output('payment_list',$payment_list);
        Tpl::output('earnest_info',$info);


        Tpl::output('state_type',$state_type);

        $this->_links[] = array('url'=>'act=earnest_money&op=earnest_edit','text'=>$text);
        Tpl::output('top_link',$this->sublink($this->_links,'earnest_edit'));
        Tpl::showpage('earnest_money.edit');
    }

    /**
     * 导出保证金支付记录CSV
     */
    public function export_csvOp(){
        $model = Model('earnest_money');
        $condition  = array();
        if (strlen($q = trim((string) $_REQUEST['etm_member_name']))) {
            $condition['etm_member_name'] = array('like', '%' . $q . '%');
        }
        if (($q = (int) $_REQUEST['etm_member_id']) > 0) {
            $condition['etm_member_id'] = $q;
        }
        if (($q = (int) $_REQUEST['etm_payment_state']) > 0) {
            $condition['etm_payment_state'] = $q;
        }

        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['etm_add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

        if ($_REQUEST['query'] != '') {
            if($_REQUEST['qtype'] == 'etm_member_name'){
                $condition[$_REQUEST['qtype']] = array('like', '%' . $_REQUEST['query'] . '%');
            }else{
                $condition[$_REQUEST['qtype']] = $_REQUEST['query'];
            }
        }

        $sort_fields = array('etm_id','etm_member_id','etm_member_name','etm_payment_code','etm_amount','etm_payment_state','etm_sn','etm_trade_sn');
        if ($_GET['sortorder'] != '' && in_array($_GET['sortname'],$sort_fields)) {
            $order = $_GET['sortname'].' '.$_GET['sortorder'];
        }
        if (!is_numeric($_GET['curpage'])){
            $count = $model->getEarnestMoneyCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=earnest_money&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $_list = $model->getEarnestMoneyList($condition,null,'*',$order,$limit);
        $this->_createCsv($_list);
    }

    /**
     * 生成csv文件
     */
    private function _createCsv($_list) {
        $data = array();
        foreach ($_list as $value) {
            $param = array();
            $param['etm_id'] = $value['etm_id'];
            $param['etm_sn'] = $value['etm_sn'];
            $param['etm_content'] = $value['etm_id']?'开通门店,门店编号:'.$value['etm_id']:'店铺入驻';
            $param['etm_add_time'] = date('Y-m-d', $value['etm_add_time']);
            $param['etm_amount'] = $value['etm_amount'];
            $param['etm_payment_state'] = $value['etm_payment_state'] ? '<span class="yes"><i class="fa fa-check-circle"></i>已支付</span>' : '<span class="no"><i class="fa fa-ban"></i>未支付</span>';
            $param['etm_payment_name'] = $value['etm_payment_name'];
            $param['trade_sn'] = $value['etm_trade_sn'];
            $param['etm_payment_time'] = date('Y-m-d', $value['etm_payment_time']);
            $param['etm_member_id'] = $value['etm_member_id'];
            $param['etm_member_name'] = $value['etm_member_name'];
            $param['etm_admin'] = $value['etm_admin']!=''?$value['etm_admin']:'--';
            $data[$value['etm_id']] = $param;
        }

        $header = array(
            'etm_id' => '编号',
            'etm_sn' => '缴费单号',
            'etm_content' => '缴费事由',
            'etm_add_time' => '提交申请时间',
            'etm_amount' => '缴费金额',
            'etm_payment_state' => '支付状态',
            'etm_payment_name' => '支付方式',
            'trade_sn' => '第三支付单号',
            'etm_payment_time' => '支付时间',
            'etm_member_id' => '会员编号',
            'etm_member_name' => '会员名称',
            'etm_admin' => '管理员'
        );
        \Shopnc\Lib::exporter()->output('earnest_money_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 记录明细列表
     */
    public function log_listOp(){
        Tpl::output('top_link',$this->sublink($this->_links,'log_list'));
        Tpl::showpage('earnest_money_log.list');
    }

    /**
     * 获取明细XML
     */
    public function log_xmlOp(){
        $model = Model('earnest_money');
        $condition  = array();
        if (strlen($q = trim((string) $_REQUEST['lg_member_name']))) {
            $condition['lg_member_name'] = array('like', '%' . $q . '%');
        }
        if (($q = (int) $_REQUEST['lg_member_id']) > 0) {
            $condition['lg_member_id'] = $q;
        }

        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['lg_add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

        if ($_POST['query'] != '') {
            if($_POST['qtype'] == 'lg_member_name'){
                $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
            }else{
                $condition[$_POST['qtype']] = $_POST['query'];
            }
        }

        $sort_fields = array('lg_id','lg_member_id','lg_member_name','lg_type','lg_av_amount','lg_add_time');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }

        $_list = $model->getEarnestMoneyLogList($condition,$_POST['rp'],'*',$order);
        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        foreach ($_list as $value) {
            $param = array();
            $param['lg_id'] = $value['lg_id'];
            $param['lg_add_time'] = date('Y-m-d', $value['lg_add_time']);
            $param['lg_content'] = $value['lg_desc'];
            $param['lg_av_amount'] = $value['lg_av_amount'];
            $param['lg_member_id'] = $value['lg_member_id'];
            $param['lg_member_name'] = $value['lg_member_name'];
            $param['lg_admin_name'] = $value['lg_admin_name'] != ''?$value['lg_admin_name']:'--';
            $data['list'][$value['lg_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 添加明细记录
     */
    public function add_logOp(){
        if(chksubmit()){
            $param = array();
            $param['lg_member_name'] = trim($_POST['member_name']);
            $param['lg_av_amount'] = floatval($_POST['etm_amount']) * intval($_POST['pay_type']);
            $param['lg_add_time'] = TIMESTAMP;
            $param['lg_member_id'] = $this->_checkChainForMember($param['lg_member_name']);
            if($param['lg_member_id'] <= 0){
                showMessage('用户不存在');
            }
            $param['lg_admin_name'] = $this->admin_info['name'];
            $param['lg_type'] = 'admin_addition';
            if(intval($_POST['pay_type']) < 0){
                $param['lg_type'] = 'admin_reduction';
            }
            $param['lg_desc'] = trim($_POST['lg_desc']);

            $m_data = array();
            $m_data['earnest_money'] = array('exp','earnest_money+'.$param['lg_av_amount']);

            $insert = Model('earnest_money')->table('earnest_money_log')->insert($param);
            if($insert){
                Model('member')->editMember(array('member_id'=>$param['lg_member_id']),$m_data);
                showMessage('添加成功','index.php?act=earnest_money&op=log_list');
            }else{
                showMessage('添加失败','index.php?act=earnest_money&op=log_list');
            }
        }
        $this->_links[] = array('url'=>'act=earnest_money&op=add_log','text'=>'添加记录明细');
        Tpl::output('top_link',$this->sublink($this->_links,'add_log'));
        Tpl::showpage('earnest_money_log.add');
    }


    public function ajaxOp(){
        $branch = trim($_GET['branch']);
        switch ($branch){
            case 'check_user_name':
                $model = Model('member');
                $member_info = $model->getMemberInfo(array('member_name'=>trim($_GET['user_name'])));
                if(empty($member_info)){
                    echo 'false';
                }else{
                    echo 'true';
                }
                break;
            case 'check_etm_sn':
                $model = Model('earnest_money');
                $earnest_info = $model->getEarnestMoneyInfo(array('etm_sn'=>trim($_GET['etm_sn'])));
                if(empty($earnest_info)){
                    echo 'true';
                }else{
                    echo 'false';
                }
                break;
            case 'check_chain_id':
                $model = Model('earnest_money');
                if(intval($_GET['chain_id']) == 0){
                    echo 'true';
                }else{
                    $earnest_info = $model->getEarnestMoneyInfo(array('etm_chain_id'=>intval($_GET['chain_id'])));
                    if(empty($earnest_info)){
                        echo 'true';
                    }else{
                        echo 'false';
                    }
                }
                break;
            case 'check_trade_no':
                $model = Model('earnest_money');
                $earnest_info = $model->getEarnestMoneyInfo(array('etm_trade_sn'=>trim($_GET['trade_no'])));
                if(empty($earnest_info)){
                    echo 'true';
                }else{
                    echo 'false';
                }
                break;
        }
    }

    //用户门店数据校验并返回会员编号
    private function _checkChainForMember($member_name,$chain_id = 0){
        $field = "member.member_id";
        $condition = array();
        $condition['member.member_name'] = $member_name;
        $condition['seller.is_admin'] = 1;
        if($chain_id > 0){
            $condition['chain.chain_id'] = $chain_id;
            $member_info = Model()->table('member,seller,chain')->field($field)->join('left')->on('member.member_id=seller.member_id,seller.store_id=chain.store_id')->where($condition)->find();
        }else{
            $member_info = Model()->table('member,seller')->field($field)->join('left')->on('member.member_id=seller.member_id')->where($condition)->find();
        }

        $member_id = 0;
        if(!empty($member_info)){
            $member_id = $member_info['member_id'];
        }
        return $member_id;
    }
}