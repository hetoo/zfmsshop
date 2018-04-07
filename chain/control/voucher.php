<?php
/**
 * 物流自提服务站代金券
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class voucherControl extends BaseChainCenterControl{
    //定义代金券类常量
    const SECONDS_OF_30DAY = 2592000;
    private $templatestate_arr;
    //每次导出订单数量
    const EXPORT_SIZE = 1000;

    public function __construct(){
        parent::__construct();
        if (C('voucher_allow') != 1){
            showDialog("请联系平台管理员开启代金券功能",'index.php?act=goods');
        }
        //代金券模板状态
        $this->templatestate_arr = array('usable'=>array(1,'有效'),'disabled'=>array(2,'失效'));
        Tpl::output('templatestate_arr',$this->templatestate_arr);
    }

    //门店代金券列表
    public function indexOp(){
        $model_voucher = Model('chain_voucher');
        $condition = array();
        if (strlen($q = trim((string) $_REQUEST['keyword']))) {
            $condition['voucher_t_title'] = array('like', '%' . $q . '%');
        }
        if (($q = (int) $_REQUEST['search_state']) > 0) {
            $condition['voucher_t_state'] = $q;
        }


        $pdates = array();
        if (strlen($q = trim((string) $_REQUEST['end_time'])) && ($q = strtotime($q . ' 00:00:00'))) {
            $pdates[] = "voucher_t_end_date >= {$q}";
        }
        if (strlen($q = trim((string) $_REQUEST['start_time'])) && ($q = strtotime($q . ' 00:00:00'))) {
            $pdates[] = "voucher_t_start_date <= {$q}";
        }
        if ($pdates) {
            $condition['pdates'] = array(
                'exp',
                implode(' or ', $pdates),
            );
        }
        $condition['voucher_t_chain_id'] = $_SESSION['chain_id'];
        $voucher_list = $model_voucher->getVoucherTemplateList($condition, '*', 0, 20, 'voucher_t_state asc,voucher_t_recommend desc,voucher_t_id desc');
        Tpl::output('voucher_list',$voucher_list);
        Tpl::output('show_page',$model_voucher->showpage());
        Tpl::showpage('voucher.list');
    }

    /*
    * 代金券模版添加
    */
    public function templateaddOp(){
        $model = Model('chain_voucher');
        if(chksubmit()){
            //验证提交的内容面额不能大于限额
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST['txt_template_title'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>Language::get('voucher_template_title_error')),
                array("input"=>$_POST['txt_template_price'], "require"=>"true","validator"=>"Number","message"=>Language::get('voucher_template_price_error')),
                array("input"=>$_POST['txt_template_limit'], "require"=>"true","validator"=>"Double","message"=>Language::get('voucher_template_limit_error')),
                array("input"=>$_POST['txt_template_describe'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"255","message"=>Language::get('voucher_template_describe_error')),
            );
            $error = $obj_validate->validate();
            //金额验证
            $price = intval($_POST['txt_template_price'])>0?intval($_POST['txt_template_price']):0;
            $limit = floatval($_POST['txt_template_limit'])>0?floatval($_POST['txt_template_limit']):0;
            if($price >= $limit) $error .= '抵扣金额必须大于订单金额';

            if ($error){
                showDialog($error,'','error');
            }else {
                $insert_arr = array();
                $insert_arr['voucher_t_title'] = trim($_POST['txt_template_title']);
                $insert_arr['voucher_t_desc'] = trim($_POST['txt_template_describe']);
                $insert_arr['voucher_t_start_date'] = time();//默认代金券模板的有效期为当前时间
                if($_POST['txt_template_enddate']){
                    $insert_arr['voucher_t_end_date'] = strtotime($_POST['txt_template_enddate']);
                }else{
                    $insert_arr['voucher_t_end_date'] = time() + 2592000;
                }
                $insert_arr['voucher_t_price'] = $price;
                $insert_arr['voucher_t_limit'] = $limit;
                $insert_arr['voucher_t_chain_id'] = $_SESSION['chain_id'];
                $insert_arr['voucher_t_chain_name'] = $_SESSION['chain_name'];
                $insert_arr['voucher_t_state'] = $this->templatestate_arr['usable'][0];
                $insert_arr['voucher_t_giveout'] = 0;
                $insert_arr['voucher_t_used'] = 0;
                $insert_arr['voucher_t_add_date'] = time();
                $insert_arr['voucher_t_eachlimit'] = intval($_POST['eachlimit'])>0?intval($_POST['eachlimit']):0;

                $rs = $model->table('chain_voucher_template')->insert($insert_arr);
                if($rs){
                    showDialog('添加成功','index.php?act=voucher','succ');
                }else{
                    showDialog('添加失败','index.php?act=voucher','error');
                }
            }
        }else{
            Tpl::output('type','add');
            Tpl::showpage('voucher.add');
        }
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
            showDialog('参数错误','index.php?act=voucher','html','error');
        }
        $model = Model('chain_voucher');
        //查询模板信息
        $param = array();
        $param['voucher_t_id'] = $t_id;
        $param['voucher_t_chain_id'] = $_SESSION['chain_id'];
        $param['voucher_t_state'] = $this->templatestate_arr['usable'][0];
        $param['voucher_t_giveout'] = array('elt','0');
        $param['voucher_t_end_date'] = array('gt',time());
        $t_info = $model->table('chain_voucher_template')->where($param)->find();
        if (empty($t_info)){
            showDialog('参数错误','index.php?act=voucher','html','error');
        }

        if(chksubmit()){
            //验证提交的内容面额不能大于限额
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST['txt_template_title'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>Language::get('voucher_template_title_error')),
                array("input"=>$_POST['txt_template_price'], "require"=>"true","validator"=>"Number","message"=>Language::get('voucher_template_price_error')),
                array("input"=>$_POST['txt_template_limit'], "require"=>"true","validator"=>"Double","message"=>Language::get('voucher_template_limit_error')),
                array("input"=>$_POST['txt_template_describe'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"255","message"=>Language::get('voucher_template_describe_error')),
            );
            $error = $obj_validate->validate();
            //金额验证
            $price = intval($_POST['txt_template_price'])>0?intval($_POST['txt_template_price']):0;

            $limit = floatval($_POST['txt_template_limit'])>0?floatval($_POST['txt_template_limit']):0;
            if($price >= $limit) $error .= '抵扣金额必须大于订单金额';

            if ($error){
                showDialog($error,'reload','error');
            }else {
                $update_arr = array();
                $update_arr['voucher_t_title'] = trim($_POST['txt_template_title']);
                $update_arr['voucher_t_desc'] = trim($_POST['txt_template_describe']);
                $update_arr['voucher_t_end_date'] = strtotime($_POST['txt_template_enddate']);
                $update_arr['voucher_t_price'] = $price;
                $update_arr['voucher_t_limit'] = $limit;
                $update_arr['voucher_t_state'] = intval($_POST['tstate']) == $this->templatestate_arr['usable'][0]?$this->templatestate_arr['usable'][0]:$this->templatestate_arr['disabled'][0];
                $update_arr['voucher_t_add_date'] = time();
                $update_arr['voucher_t_eachlimit'] = intval($_POST['eachlimit'])>0?intval($_POST['eachlimit']):0;

                $rs = $model->table('chain_voucher_template')->where(array('voucher_t_id'=>$t_info['voucher_t_id']))->update($update_arr);
                if($rs){
                    showDialog(Language::get('nc_common_op_succ'),'index.php?act=voucher','succ');
                }else{
                    showDialog(Language::get('nc_common_op_fail'),'index.php?act=voucher','error');
                }
            }
        }else{
            TPL::output('type','edit');
            TPL::output('t_info',$t_info);
            Tpl::showpage('voucher.add');
        }
    }

    /**
     * 删除代金券
     */
    public function templatedelOp(){
        $t_id = intval($_GET['tid']);
        if ($t_id <= 0){
            showMessage('参数错误','index.php?act=voucher','html','error');
        }
        $model = Model();
        //查询模板信息
        $param = array();
        $param['voucher_t_id'] = $t_id;
        $param['voucher_t_chain_id'] = $_SESSION['chain_id'];
        $param['voucher_t_giveout'] = array('elt','0');//会员没领取过代金券才可删除
        $t_info = $model->table('chain_voucher_template')->where($param)->find();
        if (empty($t_info)){
            showMessage('参数错误','index.php?act=voucher','html','error');
        }
        $rs = $model->table('chain_voucher_template')->where(array('voucher_t_id'=>$t_info['voucher_t_id']))->delete();
        if ($rs){
            showDialog('删除成功','reload','succ');
        }else {
            showDialog('删除失败');
        }
    }
}