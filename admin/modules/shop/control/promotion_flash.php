<?php
/**
 * 闪购管理
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
class promotion_flashControl extends SystemControl{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 默认Op
     */
    public function indexOp() {

        $this->flash_listOp();

    }

    /**
     * 活动列表
     */
    public function flash_listOp()
    {
        $model_flash = Model('p_flash');
        Tpl::output('flash_state_array', $model_flash->getFlashStateArray());

        $this->show_menu('flash_list');
        Tpl::showpage('promotion_flash.list');
    }

    /**
     * 获取列表XML
     */
    public function get_xmlOp(){
        $condition = array();

        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['flash_name']))) {
                $condition['flash_name'] = array('like', '%' . $q . '%');
            }
            if (strlen($q = trim((string) $_REQUEST['store_name']))) {
                $condition['store_name'] = array('like', '%' . $q . '%');
            }
            if (($q = (int) $_REQUEST['state']) > 0) {
                $condition['state'] = $q;
            }

            $pdates = array();
            if (strlen($q = trim((string) $_REQUEST['pdate1'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "end_time >= {$q}";
            }
            if (strlen($q = trim((string) $_REQUEST['pdate2'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "start_time <= {$q}";
            }
            if ($pdates) {
                $condition['pdates'] = array(
                    'exp',
                    implode(' or ', $pdates),
                );
            }

        } else {
            if (strlen($q = trim($_REQUEST['query']))) {
                switch ($_REQUEST['qtype']) {
                    case 'flash_name':
                        $condition['flash_name'] = array('like', '%'.$q.'%');
                        break;
                    case 'store_name':
                        $condition['store_name'] = array('like', '%'.$q.'%');
                        break;
                }
            }
        }

        $model_flash = Model('p_flash');
        $flash_list = (array) $model_flash->getFlashList($condition, $_REQUEST['rp'], 'flash_state asc, end_time desc');

        $flippedOwnShopIds = array_flip(Model('store')->getOwnShopIds());

        $data = array();
        $data['now_page'] = $model_flash->shownowpage();
        $data['total_num'] = $model_flash->gettotalnum();

        foreach ($flash_list as $val) {
            $o  = '<a class="btn red confirm-on-click" href="javascript:;" data-href="' . urlAdminShop('promotion_flash', 'flash_del', array(
                    'flash_id' => $val['flash_id'],
                )) . '"><i class="fa fa-trash-o"></i>删除</a>';

            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';

            if ($val['editable']) {
                $o .= '<li><a class="confirm-on-click" href="javascript:;" data-href="' . urlAdminShop('promotion_flash', 'flash_cancel', array(
                        'flash_id' => $val['flash_id'],
                    )) . '">取消活动</a></li>';
            }

            $o .= '<li><a class="confirm-on-click" href="' . urlAdminShop('promotion_flash', 'flash_detail', array(
                    'flash_id' => $val['flash_id'],
                )) . '">活动详细</a></li>';

            if ($val['is_recommend'] == '1') {
                $o .= '<li><a href="javascript:;" data-href="' . urlAdminShop('promotion_flash', 'flash_ajax', array(
                    'id' => $val['flash_id'],
                    'val' => 0,
                )) . '">取消推荐</a></li>';
            } else {
                $o .= '<li><a href="javascript:;" data-href="' . urlAdminShop('promotion_flash', 'flash_ajax', array(
                    'id' => $val['flash_id'],
                    'val' => 1,
                )) . '">推荐活动</a></li>';
            }

            $o .= '</ul></span>';

            $i = array();
            $i['operation'] = $o;
            $i['flash_name'] = '<a target="_blank" href="' . urlShop('flash', 'brand', array(
                    'flash_id'=>$val['flash_id'],
                )) . '">' . $val['flash_name'] . '</a>';
            $i['store_name'] = '<a target="_blank" href="' . urlShop('show_store', 'index', array(
                    'store_id'=>$val['store_id'],
                )) . '">' . $val['store_name'] . '</a>';

            if (isset($flippedOwnShopIds[$val['store_id']])) {
                $i['store_name'] .= '<span class="ownshop">[自营]</span>';
            }

            $i['start_time_text'] = date('Y-m-d H:i', $val['start_time']);
            $i['end_time_text'] = date('Y-m-d H:i', $val['end_time']);
            $i['is_recommend'] = $val['is_recommend'] == '1'
                ? '<span class="yes"><i class="fa fa-check-circle"></i>是</span>'
                : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $i['upper_limit'] = $val['upper_limit'];
            $i['flash_state_text'] = $val['flash_state_text'];

            $data['list'][$val['flash_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 套餐管理
     */
    public function flash_quotaOp()
    {
        $this->show_menu('flash_quota');
        Tpl::showpage('promotion_flash_quota.list');
    }

    /**
     * 套餐管理XML
     */
    public function flash_quota_xmlOp()
    {
        $condition = array();

        if (strlen($q = trim($_REQUEST['query']))) {
            switch ($_REQUEST['qtype']) {
                case 'store_name':
                    $condition['store_name'] = array('like', '%'.$q.'%');
                    break;
            }
        }

        $model_flash_quota = Model('p_flash_quota');
        $list = (array) $model_flash_quota->getFlashQuotaList($condition, $_REQUEST['rp'], 'end_time desc');

        $data = array();
        $data['now_page'] = $model_flash_quota->shownowpage();
        $data['total_num'] = $model_flash_quota->gettotalnum();

        foreach ($list as $val) {
            $i = array();
            $i['operation'] = '<span>--</span>';

            $i['store_name'] = '<a target="_blank" href="' . urlShop('show_store', 'index', array(
                    'store_id' => $val['store_id'],
                )) . '">' . $val['store_name'] . '</a>';

            $i['start_time_text'] = date("Y-m-d", $val['start_time']);
            $i['end_time_text'] = date("Y-m-d", $val['end_time']);

            $data['list'][$val['quota_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 设置
     **/
    public function flash_settingOp() {

        $model_setting = Model('setting');
        $setting = $model_setting->GetListSetting();
        Tpl::output('setting',$setting);

        $this->show_menu('flash_setting');
        Tpl::showpage('promotion_flash.setting');
    }

    public function flash_setting_saveOp() {
        $promotion_flash_price = intval($_POST['promotion_flash_price']);
        if($promotion_flash_price < 0) {
            $promotion_flash_price = 20;
        }

        $model_setting = Model('setting');
        $update_array = array();
        $update_array['promotion_flash_price'] = $promotion_flash_price;

        $result = $model_setting->updateSetting($update_array);
        if ($result){
            $this->log('修改秒杀价格为'.$promotion_flash_price.'元');
            showMessage('保存成功','');
        }else {
            showMessage('保存失败','');
        }
    }


    /**
     * 活动取消
     **/
    public function flash_cancelOp() {
        $flash_id = intval($_REQUEST['flash_id']);
        $model_flash = Model('p_flash');
        $result = $model_flash->cancelFlash(array('flash_id' => $flash_id));
        if($result) {
            $this->log('取消秒杀活动，活动编号'.$flash_id);
            Model('p_time')->delFlash($flash_id);
            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 活动删除
     **/
    public function flash_delOp() {
        $flash_id = intval($_REQUEST['flash_id']);
        $model_flash = Model('p_flash');
        $result = $model_flash->delFlash(array('flash_id' => $flash_id));
        if($result) {
            $this->log('删除秒杀活动，活动编号'.$flash_id);
            Model('p_time')->delFlash($flash_id);
            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 活动详细信息
     **/
    public function flash_detailOp() {
        $flash_id = intval($_REQUEST['flash_id']);
        $model_flash = Model('p_flash');
        $model_flash_goods = Model('p_flash_goods');

        $flash_info = $model_flash->getFlashInfoByID($flash_id);
        if(empty($flash_info)) {
            showMessage('参数错误');
        }
        Tpl::output('flash_info',$flash_info);

        //获取闪购商品列表
        $condition = array();
        $condition['flash_id'] = $flash_id;
        $flash_goods_list = $model_flash_goods->getFlashGoodsExtendList($condition);
        Tpl::output('list',$flash_goods_list);

        $this->show_menu('flash_detail');
        Tpl::showpage('promotion_flash.detail');
    }

    /**
     * ajax修改信息
     */
    public function ajaxOp(){
        $result = true;
        $update_array = array();
        $where_array = array();

        switch ($_GET['branch']){
            case 'recommend':
                $model= Model('p_flash_goods');
                $update_array['flash_recommend'] = $_GET['value'];
                $where_array['flash_goods_id'] = $_GET['id'];
                $result = $model->editFlashGoods($update_array, $where_array);
                break;
        }

        if($result) {
            echo 'true';exit;
        } else {
            echo 'false';exit;
        }

    }

    /**
     * ajax修改信息
     */
    public function flash_ajaxOp(){
        $result = true;
        $update_array = array();
        $where_array = array();
        if (!empty($_GET['id'])) {
            $model= Model('p_flash');
            $update_array['is_recommend'] = $_GET['val'];
            $where_array['flash_id'] = $_GET['id'];
            $result = $model->editFlash($update_array, $where_array);
        }
        if($result) {
            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 头部图片
     */
    public function flash_picOp(){
        $this->show_menu('flash_pic');
        $size = 3;//上传显示图片总数
        $i = 1;
        $info = array();
        $model_flash = Model('p_flash');
        $code_info = $model_flash->getRecommendPic();
        if(!empty($code_info['code_info'])) {
            $info = unserialize($code_info['code_info']);
        }
        if (chksubmit()) {
            for ($i;$i <= $size;$i++) {
                $file = 'pic'.$i;
                $info[$i]['pic'] = $_POST['show_pic'.$i];
                $info[$i]['url'] = $_POST['url'.$i];
                if (!empty($_FILES[$file]['name'])) {//上传图片
                    $filename_tmparr = explode('.', $_FILES[$file]['name']);
                    $ext = end($filename_tmparr);
                    $file_name = 'flash_pic_'.$i.'.'.$ext;
                    $upload = new UploadFile();
                    $upload->set('default_dir',ATTACH_COMMON);
                    $upload->set('file_name',$file_name);
                    $result = $upload->upfile($file);
                    if ($result) {
                        $info[$i]['pic'] = $file_name;
                    }
                }
            }
            $result = $model_flash->updateRecommendPic($info);
            showMessage(Language::get('nc_common_save_succ'),'index.php?act=promotion_flash&op=flash_pic');
        }
        Tpl::output('size',$size);
        Tpl::output('list',$info);
        Tpl::showpage('promotion_flash.pic');
    }

    /**
     * 页面内导航菜单
     *
     * @param string    $menu_key   当前导航的menu_key
     * @param array     $array      附加菜单
     * @return
     */
    private function show_menu($menu_key) {
        $menu_array = array(
            'flash_list'=>array('menu_type'=>'link','menu_name'=>'闪购列表','menu_url'=>'index.php?act=promotion_flash&op=flash_list'),
            'flash_pic'=>array('menu_type'=>'link','menu_name'=>'头部图片','menu_url'=>'index.php?act=promotion_flash&op=flash_pic'),
            'flash_detail'=>array('menu_type'=>'link','menu_name'=>'闪购详情','menu_url'=>'index.php?act=promotion_flash&op=flash_detail'),
            'flash_quota'=>array('menu_type'=>'link','menu_name'=>'套餐管理','menu_url'=>'index.php?act=promotion_flash&op=flash_quota'),
            'flash_setting'=>array('menu_type'=>'link','menu_name'=>'设置','menu_url'=>'index.php?act=promotion_flash&op=flash_setting'),
        );
        if($menu_key != 'flash_detail') unset($menu_array['flash_detail']);
        $menu_array[$menu_key]['menu_type'] = 'text';
        Tpl::output('menu',$menu_array);
    }
}