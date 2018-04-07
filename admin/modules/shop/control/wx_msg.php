<?php
/**
 * 微信通知接口设置
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
class wx_msgControl extends SystemControl{
    private $links = array(
        array('url'=>'act=wx_msg&op=index','text'=>'基本设置'),
        array('url'=>'act=wx_msg&op=msg_list','text'=>'模板列表')
    );
    public function __construct(){
        parent::__construct();
    }

    /**
     * 接口设置
     */
    public function indexOp(){
        Tpl::output('top_link',$this->sublink($this->links, 'index'));
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['weixin_mp_isuse']   = $_POST['weixin_mp_isuse'];
            $update_array['weixin_mp_appid']   = $_POST['weixin_mp_appid'];
            $update_array['weixin_mp_appsecret']  = $_POST['weixin_mp_appsecret'];
            $update_array['weixin_mp_token']   = $_POST['weixin_mp_token'];
            $update_array['weixin_mp_token_array']  = '';//清空微信的数据缓存
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                $this->log('微信通知接口设置');
                showMessage(Language::get('nc_common_save_succ'));
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        Tpl::showpage('wx_msg.edit');
    }

    /**
     * 接口模板列表
     */
    public function msg_listOp(){
        Tpl::output('top_link',$this->sublink($this->links, 'msg_list'));
        $logic_wx_api = Logic('wx_api');
        $model_wx_log = Model('wx_log');
        $list = $model_wx_log->getWxTpl();
        Tpl::output('list', $list);
        
        $access_token = $this->get_token();
        if ($access_token) {
            Tpl::output('access_token', $access_token);
            $wx_industry = $logic_wx_api->getIndustry($access_token);
            Tpl::output('wx_industry', $wx_industry);
            $tpl_list = $logic_wx_api->getAllTemplate($access_token);
            $template_list = array();
            if(!empty($tpl_list) && is_array($tpl_list)) {
                foreach($tpl_list as $k => $v) {
                    $title = $v['title'];
                    $template_list[$title] = $v['template_id'];
                }
            }
            Tpl::output('template_list', $template_list);
        }
        
        Tpl::showpage('wx_msg.list');
    }

    /**
     * 接口设置
     */
    public function wx_tplOp(){
        $logic_wx_api = Logic('wx_api');
        $access_token = $this->get_token();
        $state = 0;
        $id = $_GET['id'];
        $code = $_GET['code'];
        $t = $_GET['t'];
        if ($t == 1) {
            $state = $logic_wx_api->addTemplate($code,$id);
        }
        if ($t == 2) {
            $state = $logic_wx_api->updateTemplate($code,$id);
        }
        if ($state) {
            exit(json_encode(array('state'=>true,'msg'=>'操作成功')));
        } else {
            exit(json_encode(array('state'=>false,'msg'=>'操作失败')));
        }
    }

    /**
     * 获取access_token
     */
    public function get_token(){
        $logic_wx_api = Logic('wx_api');
        $access_token = $logic_wx_api->getAccessToken();
        if (empty($access_token)) $access_token = $logic_wx_api->getAccessToken(1);
        return $access_token;
    }

}
