<?php
/**
 * 敏感词设置
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class sensitiveControl extends SystemControl{

    private $_links = array(
        array('url'=>'act=sensitive&op=index','text'=>'敏感词设置'),
        array('url'=>'act=sensitive&op=sensitive_set','text'=>'功能设置')
    );

    public function __construct(){
        parent::__construct();
    }

    public function indexOp() {
        $this->sensitiveOp();
    }

    /**
     * 敏感词列表
     */
    public function sensitiveOp(){
        Tpl::output('top_link',$this->sublink($this->_links,'index'));
        Tpl::showpage('sensitive.index');
    }

    /**
     * 敏感词列表
     */
    public function get_sensitive_xmlOp(){
        $model = Model('sensitive_word');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('word_id','word_name','is_open','insert_time');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $list = $model->getWordList($condition, '*', $page, $order);

        $out_list = array();
        if (!empty($list) && is_array($list)){
            $fields_array = array('word_name','is_open');
            foreach ($list as $k => $v){
                $out_array = getFlexigridArray(array(),$fields_array,$v);
                $out_array['is_open'] = $v['is_open'] ==  '1' ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
                $operation = '';
                if ($v['admin_is_super'] != 1) {
                    $operation .= '<a class="btn red" href="javascript:fg_operation_del('.$v['word_id'].');"><i class="fa fa-trash-o"></i>删除</a>';
                    $operation .= '<a class="btn blue" href="index.php?act=sensitive&op=sensitive_edit&word_id='.$v['word_id'].'"><i class="fa fa-pencil-square-o"></i>'.L('nc_edit').'</a>';
                }else {
                    $operation = '--';
                }
                $out_array['operation'] = $operation;
                $out_list[$v['word_id']] = $out_array;
            }
        }

        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        $data['list'] = $out_list;
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 敏感词添加
     */
    public function sensitive_addOp(){
        $model = Model('sensitive_word');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["word_name"], "require"=>"true", "message"=>'敏感词不能为空')
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['word_name']    = trim($_POST['word_name']);
                $insert_array['is_open']   = trim($_POST['is_open']);
                $insert_array['insert_time']    = TIMESTAMP;

                $result = $model->addWord($insert_array);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=sensitive',
                            'msg'=>'返回敏感词列表',
                        ),
                        array(
                            'url'=>'index.php?act=sensitive&op=sensitive_add',
                            'msg'=>'继续新增敏感词',
                        ),
                    );
                    dkcache('sensitive_word');
                    showMessage('新增敏感词成功',$url);
                }else {
                    showMessage('新增敏感词失败');
                }
            }
        }
        Tpl::showpage('sensitive.add');
    }

    /**
     * 删除
     */
    public function sensitive_delOp() {
        $model = Model('sensitive_word');
        if (intval($_GET['word_id']) > 0){
            $model->delWord(intval($_GET['word_id']));
            dkcache('sensitive_word');
            showMessage('删除成功');
        }
        showMessage('删除失败');
    }

    /**
     * 编辑
     */
    public function sensitive_editOp() {
        $model = Model('sensitive_word');
        $current_info = $model->getOneWord(intval($_GET['word_id']));

        if (!chksubmit()) {
            Tpl::output('current_info',is_array($current_info) ? $current_info : array());
            Tpl::showpage('sensitive.edit');
        } else {
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["word_name"], "require"=>"true", "message"=>'敏感词不能为空')
            );
            $error = $obj_validate->validate();
            if($error != ''){
                showMessage($error);
            }else{
                $data = array();
                $data['word_id'] = intval($_POST['word_id']);
                $data['word_name'] = $_POST['word_name'];
                $data['is_open'] = $_POST['is_open'];
                $result = $model->updateWord($data);
                if ($result){
                    dkcache('sensitive_word');
                    showMessage('编辑成功','index.php?act=sensitive');
                }
                showMessage('编辑失败');
            }
        }
    }
    /**
     * 功能设置
     */
    public function sensitive_setOp() {
        $model_setting = Model('setting');
        $sensitive_info = $model_setting->getRowSetting('sensitive_set');
        if ($sensitive_info !== false) {
            $sensitive_list = @unserialize($sensitive_info['value']);
        }
        if (!$sensitive_list && !is_array($sensitive_list)) {
            $sensitive_list = array();
        }
        Tpl::output('sensitive_list',$sensitive_list);
        Tpl::output('top_link',$this->sublink($this->_links,'sensitive_set'));
        Tpl::showpage('sensitive_set');
    }

    /**
     * 功能编辑
     */
    public function sensitive_set_editOp() {
        $model_setting = Model('setting');
        $sensitive_info = $model_setting->getRowSetting('sensitive_set');
        if ($sensitive_info !== false) {
            $sensitive_list = @unserialize($sensitive_info['value']);
        }
        if (!is_array($sensitive_list)) {
            $sensitive_list = array();
        }
        if (!chksubmit()) {
            $current_info = array();
            foreach ($sensitive_list as $v) {
                if($v['name'] == trim($_GET['code'])){
                    $current_info = $v;
                    break;
                }
            }
            Tpl::output('current_info',is_array($current_info) ? $current_info : array());
            Tpl::showpage('sensitive_set.edit');
        } else {
            if ($_POST['name'] != '') {
                $sensitive_list_new = array();
                foreach ($sensitive_list as $v) {
                    if($v['name'] == trim($_POST['name'])){
                        $v['is_open'] = intval($_POST['is_open']);
                    }
                    $sensitive_list_new[] = $v;
                }
                $result = $model_setting->updateSetting(array('sensitive_set'=>serialize($sensitive_list_new)));
                if ($result){
                    showMessage('编辑成功','index.php?act=sensitive&op=sensitive_set');
                }
            }
            showMessage('编辑失败');
        }

    }
}