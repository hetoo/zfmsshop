<?php
/**
 * 网站设置
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
class settingControl extends SystemControl{
    private $links = array(
        array('url'=>'act=setting&op=base','lang'=>'web_set'),
        array('url'=>'act=setting&op=dump','lang'=>'dis_dump'),
        array('url'=>'act=setting&op=inv_content','text'=>'发票内容设置'),
        array('url'=>'act=setting&op=date_content','text'=>'配送日期设置')
    );
    public function __construct(){
        parent::__construct();
        Language::read('setting');
    }

    public function indexOp() {
        $this->baseOp();
    }

    /**
     * 基本信息
     */
    public function baseOp(){
        $model_setting = Model('setting');
        if (chksubmit()){
            //上传网站Logo
            if (!empty($_FILES['site_logo']['name'])){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
                $result = $upload->upfile('site_logo');
                if ($result){
                    $_POST['site_logo'] = $upload->file_name;
                }else {
                    showMessage($upload->error,'','','error');
                }
            }
            if (!empty($_FILES['member_logo']['name'])){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
                $result = $upload->upfile('member_logo');
                if ($result){
                    $_POST['member_logo'] = $upload->file_name;
                }else {
                    showMessage($upload->error,'','','error');
                }
            }
            if (!empty($_FILES['seller_center_logo']['name'])){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
                $result = $upload->upfile('seller_center_logo');
                if ($result){
                    $_POST['seller_center_logo'] = $upload->file_name;
                }else {
                    showMessage($upload->error,'','','error');
                }
            }
            $list_setting = $model_setting->getListSetting();
            $update_array = array();
            $update_array['site_phone'] = $_POST['site_phone'];
            $update_array['site_email'] = $_POST['site_email'];
            if (!empty($_POST['site_logo'])){
                $update_array['site_logo'] = $_POST['site_logo'];
            }
            if (!empty($_POST['member_logo'])){
                $update_array['member_logo'] = $_POST['member_logo'];
            }
            if (!empty($_POST['seller_center_logo'])){
                $update_array['seller_center_logo'] = $_POST['seller_center_logo'];
            }
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                //判断有没有之前的图片，如果有则删除
                if (!empty($list_setting['site_logo']) && !empty($_POST['site_logo'])){
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_COMMON.DS.$list_setting['site_logo']);
                }
                if (!empty($list_setting['member_logo']) && !empty($_POST['member_logo'])){
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_COMMON.DS.$list_setting['member_logo']);
                }
                if (!empty($list_setting['seller_center_logo']) && !empty($_POST['seller_center_logo'])){
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_COMMON.DS.$list_setting['seller_center_logo']);
                }
                $this->log(L('nc_edit,web_set'),1);
                showMessage(L('nc_common_save_succ'));
            }else {
                $this->log(L('nc_edit,web_set'),0);
                showMessage(L('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);

        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'base'));

        Tpl::showpage('setting.base');
    }

    /**
     * 防灌水设置
     */
    public function dumpOp(){
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['guest_comment'] = $_POST['guest_comment'];
            $update_array['captcha_status_goodsqa'] = $_POST['captcha_status_goodsqa'];
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                $this->log(L('nc_edit,dis_dump'),1);
                showMessage(L('nc_common_save_succ'));
            }else {
                $this->log(L('nc_edit,dis_dump'),0);
                showMessage(L('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        Tpl::output('top_link',$this->sublink($this->links,'dump'));
        Tpl::showpage('setting.dump');
    }

    /**
     * 发票内容设置
     */
    public function inv_contentOp(){
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['inv_content'] = $_POST['inv_content'];
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                $this->log(L('nc_edit').'发票内容设置',1);
                showMessage(L('nc_common_save_succ'));
            }else {
                $this->log(L('nc_edit').'发票内容设置',0);
                showMessage(L('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        Tpl::output('top_link',$this->sublink($this->links,'inv_content'));
        Tpl::showpage('setting.inv_content');
    }

    /**
     * 配送日期设置
     */
    public function date_contentOp(){
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['order_date_content'] = $_POST['order_date_content'];
            $update_array['order_date_msg'] = $_POST['order_date_msg'];
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                $this->log(L('nc_edit').'配送日期设置',1);
                showMessage(L('nc_common_save_succ'));
            }else {
                $this->log(L('nc_edit').'配送日期设置',0);
                showMessage(L('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        Tpl::output('top_link',$this->sublink($this->links,'date_content'));
        Tpl::showpage('setting.date_content');
    }
}
