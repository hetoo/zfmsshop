<?php
/**
 * 代金券管理
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class chain_voucherControl extends SystemControl{
    const SECONDS_OF_30DAY = 2592000;
    private $applystate_arr;
    private $quotastate_arr;
    private $templatestate_arr;

    public function __construct(){
        parent::__construct();
        Language::read('voucher');
        if (C('voucher_allow') != 1 || C('points_isuse')!=1){
            showMessage(Language::get('admin_voucher_unavailable'),'index.php?act=operation&op=point','html','succ',1,4000);
        }
        $this->applystate_arr = array('new'=>array(1,Language::get('admin_voucher_applystate_new')),'verify'=>array(2,Language::get('admin_voucher_applystate_verify')),'cancel'=>array(3,Language::get('admin_voucher_applystate_cancel')));
        $this->quotastate_arr = array('activity'=>array(1,Language::get('admin_voucher_quotastate_activity')),'cancel'=>array(2,Language::get('admin_voucher_quotastate_cancel')),'expire'=>array(3,Language::get('admin_voucher_quotastate_expire')));
        //代金券模板状态
        $this->templatestate_arr = array('usable'=>array(1,Language::get('admin_voucher_templatestate_usable')),'disabled'=>array(2,Language::get('admin_voucher_templatestate_disabled')));
        Tpl::output('applystate_arr',$this->applystate_arr);
        Tpl::output('quotastate_arr',$this->quotastate_arr);
        Tpl::output('templatestate_arr',$this->templatestate_arr);
    }

    /*
     * 默认操作列出代金券
     */
    public function indexOp(){
        $this->templatelistOp();
    }

    /**
     * 代金券列表
     */
    public function templatelistOp()
    {
        $model_voucher = Model('chain_voucher');
        //状态
        $templateState = $model_voucher->getTemplateState();
        TPL::output('templateState',$templateState);
        $this->show_menu('voucher', 'templatelist');
        Tpl::showpage('chain_voucher.templatelist');
    }

    /**
     * 代金券列表XML
     */
    public function templatelist_xmlOp()
    {
        $condition = array();

        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['voucher_t_title']))) {
                $condition['voucher_t_title'] = array('like', '%' . $q . '%');
            }
            if (strlen($q = trim((string) $_REQUEST['voucher_t_chain_name']))) {
                $condition['voucher_t_chain_name'] = array('like', '%' . $q . '%');
            }
            if (($q = (int) $_REQUEST['voucher_t_state']) > 0) {
                $condition['voucher_t_state'] = $q;
            }
            if (strlen($q = trim((string) $_REQUEST['voucher_t_recommend']))) {
                $condition['voucher_t_recommend'] = (int) $q;
            }

            if (trim($_GET['sdate']) && trim($_GET['edate'])) {
                $sdate = strtotime($_GET['sdate']);
                $edate = strtotime($_GET['edate']);
                $condition['voucher_t_add_date'] = array('between', "$sdate,$edate");
            } elseif (trim($_GET['sdate'])) {
                $sdate = strtotime($_GET['sdate']);
                $condition['voucher_t_add_date'] = array('egt', $sdate);
            } elseif (trim($_GET['edate'])) {
                $edate = strtotime($_GET['edate']);
                $condition['voucher_t_add_date'] = array('elt', $edate);
            }

            $pdates = array();
            if (strlen($q = trim((string) $_REQUEST['pdate1'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "voucher_t_end_date >= {$q}";
            }
            if (strlen($q = trim((string) $_REQUEST['pdate2'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "voucher_t_start_date <= {$q}";
            }
            if ($pdates) {
                $condition['pdates'] = array(
                    'exp',
                    implode(' and ', $pdates),
                );
            }

        } else {
            if (strlen($q = trim($_REQUEST['query']))) {
                switch ($_REQUEST['qtype']) {
                    case 'voucher_t_title':
                        $condition['voucher_t_title'] = array('like', '%'.$q.'%');
                        break;
                    case 'voucher_t_chain_name':
                        $condition['voucher_t_chain_name'] = array('like', '%'.$q.'%');
                        break;
                }
            }
        }

        switch ($_REQUEST['sortname']) {
            case 'voucher_t_price':
            case 'voucher_t_limit':
                $sort = $_REQUEST['sortname'];
                break;
            case 'add_time_text':
                $sort = 'voucher_t_add_date';
                break;
            case 'start_time_text':
                $sort = 'voucher_t_start_date';
                break;
            case 'end_time_text':
                $sort = 'voucher_t_end_date';
                break;
            default:
                $sort = 'voucher_t_id';
                break;
        }
        if ($_REQUEST['sortorder'] != 'asc') {
            $sort .= ' desc';
        }

        $model = Model('chain_voucher');
        $list = $model->getVoucherTemplateList($condition, '*', 0, $_REQUEST['rp'], 'voucher_t_state asc, ' . $sort);

        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();

        foreach ($list as $val) {
            $o = '<a class="btn blue" href="' . urlAdminShop('chain_voucher', 'templateedit', array(
                    'tid' => $val['voucher_t_id'],
                )) . '"><i class="fa fa-pencil-square-o"></i>编辑</a>';

            $i = array();
            $i['operation'] = $o;
            $i['voucher_t_title'] = $val['voucher_t_title'];

            $i['voucher_t_chain_name'] = '<a target="_blank" href="' . urlShop('show_chain', 'index', array(
                    'chain_id' => $val['voucher_t_chain_id'],
                )) . '">' . $val['voucher_t_chain_name'] . '</a>';

            $i['voucher_t_price'] = $val['voucher_t_price'];
            $i['voucher_t_limit'] = $val['voucher_t_limit'];

            $i['add_time_text'] = date('Y-m-d H:i', $val['voucher_t_add_date']);
            $i['start_time_text'] = date('Y-m-d H:i', $val['voucher_t_start_date']);
            $i['end_time_text'] = date('Y-m-d H:i', $val['voucher_t_end_date']);

            $i['voucher_t_state_text'] = $val['voucher_t_state_text'];

            $i['recommend'] = $val['voucher_t_recommend'] == '1'
                ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>'
                : '<span class="no"><i class="fa fa-ban"></i>否</span>';

            $data['list'][$val['voucher_t_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }

    /*
     * 代金券模版编辑
     */
    public function templateeditOp(){
        $t_id = intval($_GET['tid']);
        if ($t_id <= 0){
            $t_id = intval($_POST['tid']);
        }
        if ($t_id <= 0){
            showMessage('参数错误','index.php?act=chain_voucher&op=templatelist','','error');
        }
        $model = Model('chain_voucher');
        //查询模板信息
        $param = array();
        $param['voucher_t_id'] = $t_id;
        $t_info = $model->getVoucherTemplateInfo($param);
        if (empty($t_info)){
            showMessage('参数错误','index.php?act=chain_voucher&op=templatelist','html','error');
        }
        if(chksubmit()){
            $update_arr = array();
            $update_arr['voucher_t_state'] = intval($_POST['tstate']) == $this->templatestate_arr['usable'][0]?$this->templatestate_arr['usable'][0]:$this->templatestate_arr['disabled'][0];
            $update_arr['voucher_t_recommend'] = intval($_POST['recommend'])==1?1:0;
            $rs = $model->table('chain_voucher_template')->where(array('voucher_t_id'=>$t_info['voucher_t_id']))->update($update_arr);
            if($rs){
                $this->log('编辑门店代金券[ID:'.$t_id.']');
                showMessage('编辑成功','index.php?act=chain_voucher&op=templatelist','succ');
            }else{
                showMessage('编辑失败','index.php?act=chain_voucher&op=templatelist','error');
            }
        }else{
            TPL::output('t_info',$t_info);
            $this->show_menu('templateedit','templateedit');
            Tpl::showpage('chain_voucher.templateedit');
        }
    }

    /**
     * 页面内导航菜单
     * @param string    $menu_key   当前导航的menu_key
     * @param array     $array      附加菜单
     * @return
     */
    private function show_menu($menu_type,$menu_key='') {
        $menu_array     = array();
        switch ($menu_type) {
            case 'voucher':
                $menu_array = array(
                    3=>array('menu_key'=>'templatelist','menu_name'=>'门店代金券', 'menu_url'=>'index.php?act=voucher&op=templatelist'),
                );
                break;
            case 'templateedit':
                $menu_array = array(
                    1=>array('menu_key'=>'templatelist','menu_name'=>'门店代金券', 'menu_url'=>'index.php?act=voucher&op=templatelist'),
                    2=>array('menu_key'=>'templateedit','menu_name'=>'编辑代金券', 'menu_url'=>'')
                );
                break;
        }
        Tpl::output('menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
