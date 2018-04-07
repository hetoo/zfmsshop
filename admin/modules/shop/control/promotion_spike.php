<?php
/**
 * 秒杀管理
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
class promotion_spikeControl extends SystemControl{

    public function __construct(){
        parent::__construct();
    }

    /**
     * 默认Op
     */
    public function indexOp() {
        $this->spike_listOp();
    }

    /**
     * 活动列表
     */
    public function spike_listOp(){
        $model_spike = Model('p_spike');
        Tpl::output('spike_state_array', $model_spike->getSpikeStateArray());

        $this->show_menu('spike_list');
        Tpl::showpage('promotion_spike.list');
    }

    /**
     * 获取列表XML
     */
    public function get_xmlOp(){
        $condition = array();

        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['spike_name']))) {
                $condition['spike_name'] = array('like', '%' . $q . '%');
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
                    case 'spike_name':
                        $condition['spike_name'] = array('like', '%'.$q.'%');
                        break;
                    case 'store_name':
                        $condition['store_name'] = array('like', '%'.$q.'%');
                        break;
                }
            }
        }

        $model_spike = Model('p_spike');
        $spike_list = (array) $model_spike->getSpikeList($condition, $_REQUEST['rp'], 'spike_state asc, end_time desc');

        $flippedOwnShopIds = array_flip(Model('store')->getOwnShopIds());

        $data = array();
        $data['now_page'] = $model_spike->shownowpage();
        $data['total_num'] = $model_spike->gettotalnum();

        foreach ($spike_list as $val) {
            $o  = '<a class="btn red confirm-on-click" href="javascript:;" data-href="' . urlAdminShop('promotion_spike', 'spike_del', array(
                    'spike_id' => $val['spike_id'],
                )) . '"><i class="fa fa-trash-o"></i>删除</a>';

            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';

            if ($val['editable']) {
                $o .= '<li><a class="confirm-on-click" href="javascript:;" data-href="' . urlAdminShop('promotion_spike', 'spike_cancel', array(
                        'spike_id' => $val['spike_id'],
                    )) . '">取消活动</a></li>';
            }

            $o .= '<li><a class="confirm-on-click" href="' . urlAdminShop('promotion_spike', 'spike_detail', array(
                    'spike_id' => $val['spike_id'],
                )) . '">活动详细</a></li>';

            $o .= '</ul></span>';

            $i = array();
            $i['operation'] = $o;
            $i['spike_name'] = '<a target="_blank" href="' . urlShop('spike', 'brand', array(
                    'spike_id'=>$val['spike_id'],
                )) . '">' . $val['spike_name'] . '</a>';
            $i['store_name'] = '<a target="_blank" href="' . urlShop('show_store', 'index', array(
                    'store_id'=>$val['store_id'],
                )) . '">' . $val['store_name'] . '</a>';

            if (isset($flippedOwnShopIds[$val['store_id']])) {
                $i['store_name'] .= '<span class="ownshop">[自营]</span>';
            }

            $i['start_time_text'] = date('Y-m-d H:i', $val['start_time']);
            $i['end_time_text'] = date('Y-m-d H:i', $val['end_time']);

            $i['upper_limit'] = $val['upper_limit'];
            $i['spike_state_text'] = $val['spike_state_text'];

            $data['list'][$val['spike_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 套餐管理
     */
    public function spike_quotaOp()
    {
        $this->show_menu('spike_quota');
        Tpl::showpage('promotion_spike_quota.list');
    }

    /**
     * 套餐管理XML
     */
    public function spike_quota_xmlOp()
    {
        $condition = array();

        if (strlen($q = trim($_REQUEST['query']))) {
            switch ($_REQUEST['qtype']) {
                case 'store_name':
                    $condition['store_name'] = array('like', '%'.$q.'%');
                    break;
            }
        }

        $model_spike_quota = Model('p_spike_quota');
        $list = (array) $model_spike_quota->getSpikeQuotaList($condition, $_REQUEST['rp'], 'end_time desc');

        $data = array();
        $data['now_page'] = $model_spike_quota->shownowpage();
        $data['total_num'] = $model_spike_quota->gettotalnum();

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
    public function spike_settingOp() {

        $model_setting = Model('setting');
        $setting = $model_setting->GetListSetting();
        Tpl::output('setting',$setting);

        $this->show_menu('spike_setting');
        Tpl::showpage('promotion_spike.setting');
    }

    public function spike_setting_saveOp() {
        $promotion_spike_price = intval($_POST['promotion_spike_price']);
        if($promotion_spike_price < 0) {
            $promotion_spike_price = 20;
        }

        $model_setting = Model('setting');
        $update_array = array();
        $update_array['promotion_spike_price'] = $promotion_spike_price;

        $result = $model_setting->updateSetting($update_array);
        if ($result){
            $this->log('修改秒杀价格为'.$promotion_spike_price.'元');
            showMessage('保存成功','');
        }else {
            showMessage('保存失败','');
        }
    }


    /**
     * 活动取消
     **/
    public function spike_cancelOp() {
        $spike_id = intval($_REQUEST['spike_id']);
        $model_spike = Model('p_spike');
        $result = $model_spike->cancelSpike(array('spike_id' => $spike_id));
        if($result) {
            $this->log('取消秒杀活动，活动编号'.$spike_id);
            Model('p_time')->delSpike($spike_id);
            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 活动删除
     **/
    public function spike_delOp() {
        $spike_id = intval($_REQUEST['spike_id']);
        $model_spike = Model('p_spike');
        $result = $model_spike->delSpike(array('spike_id' => $spike_id));
        if($result) {
            $this->log('删除秒杀活动，活动编号'.$spike_id);
            Model('p_time')->delSpike($spike_id);
            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 活动详细信息
     **/
    public function spike_detailOp() {
        $spike_id = intval($_REQUEST['spike_id']);
        $model_spike = Model('p_spike');
        $model_spike_goods = Model('p_spike_goods');

        $spike_info = $model_spike->getSpikeInfoByID($spike_id);
        if(empty($spike_info)) {
            showMessage('参数错误');
        }
        Tpl::output('spike_info',$spike_info);

        //获取秒杀商品列表
        $condition = array();
        $condition['spike_id'] = $spike_id;
        $spike_goods_list = $model_spike_goods->getSpikeGoodsExtendList($condition);
        Tpl::output('list',$spike_goods_list);

        $this->show_menu('spike_detail');
        Tpl::showpage('promotion_spike.detail');
    }

    /**
     * ajax修改团购信息
     */
    public function ajaxOp(){
        $result = true;
        $update_array = array();
        $where_array = array();

        switch ($_GET['branch']){
            case 'recommend':
                $model= Model('p_spike_goods');
                $update_array['spike_recommend'] = $_GET['value'];
                $where_array['spike_goods_id'] = $_GET['id'];
                $result = $model->editSpikeGoods($update_array, $where_array);
                break;
        }

        if($result) {
            echo 'true';exit;
        } else {
            echo 'false';exit;
        }

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
            'spike_list'=>array('menu_type'=>'link','menu_name'=>'活动列表','menu_url'=>'index.php?act=promotion_spike&op=spike_list'),
            'spike_detail'=>array('menu_type'=>'link','menu_name'=>'活动详情','menu_url'=>'index.php?act=promotion_spike&op=spike_detail'),
            'spike_quota'=>array('menu_type'=>'link','menu_name'=>'套餐管理','menu_url'=>'index.php?act=promotion_spike&op=spike_quota'),
            'spike_setting'=>array('menu_type'=>'link','menu_name'=>'设置','menu_url'=>'index.php?act=promotion_spike&op=spike_setting'),
        );
        if($menu_key != 'spike_detail') unset($menu_array['spike_detail']);
        $menu_array[$menu_key]['menu_type'] = 'text';
        Tpl::output('menu',$menu_array);
    }
}