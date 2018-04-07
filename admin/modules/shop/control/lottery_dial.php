<?php
/**
 * 大转盘抽奖管理
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

defined('InShopNC') or exit('Access Invalid!');

use Shopnc\Tpl;

class lottery_dialControl extends SystemControl{

    public function indexOp(){
        $this->dial_listOp();
    }

    /**
     * 显示活动列表
     */
    public function dial_listOp(){
        Tpl::showpage('lottery_dial.list');
    }

    /**
     * 添加活动
     */
    public function addOp(){
        if(chksubmit()){
            $data = $this->_checkData($_POST);
            $data['add_time'] = TIMESTAMP;
            $data['lot_state'] = 0;
            if($data['start_time'] <= $data['add_time']){
                $data['lot_state'] = 1;
            }
            $dail_model = Model('lottery_dial');
            $res = $dail_model->addDialActivity($data);
            if($res){
                showMessage('活动创建成功','index.php?act=lottery_dial&op=index');
            }else{
                showMessage('活动创建失败');
            }
        }
        Tpl::showpage('lottery_dial.add');
    }

    /**
     * 编辑活动
     */
    public function editOp(){
        $act_id = intval($_REQUEST['lot_id']);
        if($act_id <= 0){
            showMessage('参数错误');
        }
        $dail_model = Model('lottery_dial');
        $lot_info = $dail_model->getOneDialActivityById($act_id);
        if($lot_info['lot_state'] >= 1){
            showMessage('进行中或已完成活动不能编辑');
        }
        if(chksubmit()){
            $old_img = array();
            $old_img['lot_dial_bg'] = $lot_info['lot_dial_bg'];
            $old_img['lot_dial_pointer'] = $lot_info['lot_dial_pointer'];
            $data = $this->_checkData($_POST);
            $res = $dail_model->editDialActivity($data, array('lot_id'=>$act_id));
            if($res){
                if(!empty($data['lot_dial_bg'])){
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$old_img['lot_dial_bg']);
                }
                if(!empty($data['lot_dial_pointer'])){
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$old_img['lot_dial_pointer']);
                }
                if(!empty($data['lot_bg'])){
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$old_img['lot_bg']);
                }
                showMessage('活动编辑成功','index.php?act=lottery_dial&op=index');
            }else{
                showMessage('活动编辑失败');
            }
        }
        $lot_info['lot_info'] = unserialize($lot_info['lot_info']);
        Tpl::output('dial_info',$lot_info);
        Tpl::showpage('lottery_dial.add');
    }

    /**
     * 删除活动
     */
    public function delOp(){
        $ids = (array)explode(',',$_GET['lot_id']);
        if(empty($ids)){
            $this->jsonOutput('参数错误');
        }
        $condition = array();
        $condition['lot_id'] = array('in',$ids);
        $dial_model = Model('lottery_dial');
        $result = $dial_model->delDialActivity($condition);
        if($result){
            $this->jsonOutput();
        }else{
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 获取活动列表数据
     */
    public function dial_xmlOp(){
        $condition = array();

        if (strlen($q = trim($_REQUEST['query'])) > 0) {
            switch ($_REQUEST['qtype']) {
                case 'lot_name':
                    $condition['lot_name'] = $q;
                    break;
            }
        }

        switch ($_REQUEST['sortname']) {
            case 'lot_id':
            case 'lot_weight':
            case 'start_time':
            case 'end_time':
            case 'lot_type':
            case 'lot_count':
                $sort = $_REQUEST['sortname'];
                break;
            default:
                $sort = 'lot_id';
                break;
        }
        if ($_REQUEST['sortorder'] != 'asc') {
            $sort .= ' desc';
        }

        $page = $_REQUEST['rp'];

        $activity = Model('lottery_dial');
        $list = (array) $activity->getListDialActivity($condition, $page, '*', $sort);

        $data = array();
        $data['now_page'] = $activity->shownowpage();
        $data['total_num'] = $activity->gettotalnum();

        foreach ($list as $val) {
            $o = '';
            if (in_array($val['lot_state'],array(0,1))) {
                $o .= '<a class="btn orange confirm-del-on-click" href="javascript:;" data-href="index.php?act=lottery_dial&op=close_dial&lot_id=' .
                    $val['lot_id'] .
                    '"><i class="fa fa-ban"></i>关闭</a>';
            }
            if(in_array($val['lot_state'],array(2,3))){
                $o .= '<a class="btn red confirm-del-on-click" href="javascript:;" data-href="index.php?act=lottery_dial&op=del&lot_id=' .
                    $val['lot_id'] .
                    '"><i class="fa fa-trash-o"></i>删除</a>';
            }
            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
            if ($val['lot_state'] == 0) {
            $o .= '<li><a href="index.php?act=lottery_dial&op=edit&lot_id=' . $val['lot_id'] . '">编辑活动</a></li>';
            }
            $o .= '<li><a href="index.php?act=lottery_dial&op=detail_ainfo&lot_id=' . $val['lot_id'] . '">活动详情</a></li>';
            $o .= '<li><a href="index.php?act=lottery_dial&op=detail_list&lot_id=' . $val['lot_id'] . '">抽奖管理</a></li>';
            $o .= '</ul></span>';

            $i = array();
            $i['operation'] = $o;

            $i['lot_id'] =  $val['lot_id'];
            $i['lot_name'] =  $val['lot_name'];
            $i['lot_weight'] =  $val['lot_weight'].' %';
            $i['start_time'] = date('Y-m-d', $val['start_time']);
            $i['end_time'] = date('Y-m-d', $val['end_time']);
            $i['lot_type'] =  $val['lot_type'] == 1?'按订单抽取':'按会员抽取';
            $i['lot_count'] =  $val['lot_count'];

            $data['list'][$val['lot_id']] = $i;
        }
        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 活动详情
     */
    public function detail_ainfoOp(){
        $act_id = intval($_REQUEST['lot_id']);
        if($act_id <= 0){
            showMessage('参数错误');
        }
        $dail_model = Model('lottery_dial');
        $lot_info = $dail_model->getOneDialActivityById($act_id);
        $lot_info['lot_info'] = unserialize($lot_info['lot_info']);
        Tpl::output('dial_info',$lot_info);
        Tpl::showpage('lottery_dial.info');
    }

    /**
     * 抽奖详情列表
     */
    public function detail_listOp(){
        $act_id = intval($_REQUEST['lot_id']);
        $dail_model = Model('lottery_dial');
        $dial_info = $dail_model->getOneDialActivityById($act_id,'lot_id,lot_name');
        Tpl::output('dial_info',$dial_info);
        Tpl::showpage('lottery_dial.detail_list');
    }

    /**
     * 获取列表xml
     */
    public function detail_xmlOp(){
        $condition = array();

        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['rate_name']))) {
                $condition['rate_name'] = $q;
            }
            if (strlen($q = trim((string) $_REQUEST['rate_type']))) {
                $condition['rate_type'] = (int) $q;
            }
            if (strlen($q = trim((string) $_REQUEST['prize_state']))) {
                $condition['prize_state'] = (int) $q;
            }

            $pdates = array();
            if (strlen($q = trim((string) $_REQUEST['pdate1'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "add_time >= {$q}";
            }
            if (strlen($q = trim((string) $_REQUEST['pdate2'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "add_time <= {$q}";
            }
            if ($pdates) {
                $condition['pdates'] = implode(' and ', $pdates);
            }
        } else {
            if (strlen($q = trim($_REQUEST['query'])) > 0) {
                switch ($_REQUEST['qtype']) {
                    case 'rate_name':
                        $condition['rate_name'] = $q;
                        break;
                }
            }
        }

        switch ($_REQUEST['sortname']) {
            case 'member_name':
            case 'rate_type':
            case 'prize_state':
                $sort = $_REQUEST['sortname'];
                break;
            default:
                $sort = 'add_time';
                break;
        }
        if ($_REQUEST['sortorder'] != 'asc') {
            $sort .= ' desc';
        }

        $act_id = intval($_REQUEST['lot_id']);
        $condition['lot_id'] = $act_id;

        $page = $_REQUEST['rp'];

        $dail_model = Model('lottery_dial');
        $list = (array) $dail_model->getUnionListDialPrize($condition, $page, '*', $sort);

        $data = array();
        $data['now_page'] = $dail_model->shownowpage();
        $data['total_num'] = $dail_model->gettotalnum();

        foreach ($list as $val) {
            $o = '<a class="btn blue" href="index.php?act=lottery_dial&op=detail_info&lot_id='.$val['lot_id'].'&m_id='.$val['member_id'].'&act_id='.$val['act_id'].'"><i class="fa fa-list-alt"></i>查看</a>';
            $i = array();
            $i['operation'] = $o;

            $i['member_name'] =  $val['member_name'];
            $i['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
            $i['rate_name'] =  $val['rate_name'];
            $i['rate_type'] =  $this->_getPrizeType($val['rate_type']);
            $i['prize_state'] = $val['prize_state'] == 1
                ? '<span class="yes"><i class="fa fa-check-circle"></i>已派奖</span>'
                : '<span class="no"><i class="fa fa-ban"></i>未派奖</span>';

            $data['list'][] = $i;
        }
        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 查看中奖详情
     */
    public function detail_infoOp(){
        $lot_id = intval($_GET['lot_id']);
        $m_id = intval($_GET['m_id']);
        $act_id = intval($_GET['act_id']);
        $condition = array();
        $condition['act_id'] = $act_id;
        $condition['lot_id'] = $lot_id;
        $condition['member_id'] = $m_id;
        $dial_model = Model('lottery_dial');
        $dial_info = $dial_model->getOneDialActivityById($lot_id);
        $dial_info['lot_info'] = unserialize($dial_info['lot_info']);
        Tpl::output('dial_info',$dial_info);

        $lot_info = $dial_model->getOneDialPrize($m_id,$condition);

        Tpl::output('lot_info',$lot_info);
        Tpl::showpage('lottery_dial.detail_info');
    }

    /**
     * 关闭活动
     */
    public function close_dialOp(){
        $id = intval($_GET['lot_id']);
        if($id <= 0){
            $this->jsonOutput('参数错误');
        }
        $condition = array();
        $condition['lot_id'] = $id;
        $result = Model('lottery_dial')->editDialActivity(array('lot_state'=>3),$condition);
        if($result){
            $this->jsonOutput();
        }else{
            $this->jsonOutput('操作失败');
        }
    }


    /**
     * 更新发奖状态
     */
    public function change_stateOp(){
        $lot_id = intval($_POST['lot_id']);
        $m_id = intval($_POST['m_id']);
        $act_id = intval($_POST['act_id']);
        $condition = array();
        $condition['act_id'] = $act_id;
        $condition['lot_id'] = $lot_id;
        $condition['member_id'] = $m_id;
        $dial_model = Model('lottery_dial');
        $lot_info = $dial_model->getOneDialPrizeById($m_id,$act_id);
        $member_info = Model('member')->getMemberInfoByID($m_id);

        $send_state = $this->_sendPrize($lot_info,$member_info);
        if($send_state){
            $update_data = array();
            $update_data['prize_content'] = trim($_POST['prize_content']);
            $update_data['prize_time'] = time();
            $update_data['prize_member'] = $this->admin_info['name'];
            $update_data['prize_state'] = $send_state;
            $res = $dial_model->editDialprize($m_id, $condition, $update_data);
            if($res){
                showMessage('派奖成功','index.php?act=lottery_dial&op=detail_info&lot_id='.$lot_id.'&m_id='.$m_id.'&act_id='.$act_id);
            }else{
                showMessage('派奖失败','index.php?act=lottery_dial&op=detail_info&lot_id='.$lot_id.'&m_id='.$m_id.'&act_id='.$act_id);
            }
        }else{
            showMessage('派奖失败','index.php?act=lottery_dial&op=detail_info&lot_id='.$lot_id.'&m_id='.$m_id.'&act_id='.$act_id);
        }
    }

    //发奖
    private function _sendPrize($lot_info,$member_info){
        $return_data = 0;
        if($lot_info['rate_type'] == 1){
            $prize_info = unserialize($lot_info['prize_info']);
            $points = intval($prize_info['prize_num']);
            //增加积分

            $result = Model('points')->savePointsLog('lottery',array('pl_memberid'=>$member_info['member_id'],'pl_membername'=>$member_info['member_name'],'pl_points'=>$points));
            if ($result) {
                $return_data = 1;
            }
        }elseif($lot_info['rate_type'] == 2){ //平台红包发奖
            $prize_info = unserialize($lot_info['prize_info']);
            $t_id = intval($prize_info['coupon_id']);
            $model_redpacket = Model('redpacket');
            //验证是否可领取红包
            $data = $model_redpacket->getCanChangeTemplateInfo($t_id, $member_info['member_id']);
            if ($data['state'] == true){
                try {
                    $model_redpacket->beginTransaction();
                    //添加红包信息
                    $data = $model_redpacket->exchangeRedpacket($data['info'], $member_info['member_id'], $member_info['member_name']);
                    if ($data['state']) {
                        $model_redpacket->commit();
                        $return_data = 1;
                    }
                } catch (Exception $e) {
                    $model_redpacket->rollback();
                }
            }
        }else{
            $return_data = 1;
        }
        return $return_data;
    }


    /**
     * ajax 操作
     * 获取免费领取平台红包
     */
    public function get_couponOp(){
        $model_redpacket = Model('redpacket');
        $condition = array();
        $condition['rpacket_t_gettype'] = 3;
        $condition['rpacket_t_state'] = 1;
        $condition['rpacket_t_end_date'] = array('egt',time() + 86400);
        $end_time = trim($_REQUEST['end_time']);
        if (trim($end_time) != '') {
            $condition['rpacket_t_end_date'] = array('egt',$end_time);
        }
        $field = "rpacket_t_id,rpacket_t_title,rpacket_t_start_date,rpacket_t_end_date,rpacket_t_price,rpacket_t_limit,rpacket_t_total,rpacket_t_giveout,rpacket_t_customimg";
        $coupon_list = (array)$model_redpacket->getRptTemplateList($condition, $field, '', 6);
        Tpl::output('show_page', $model_redpacket->showpage());
        Tpl::output('index',$_REQUEST['index']);
        Tpl::output('coupon_list', $coupon_list);
        Tpl::showpage('lottery_dial.coupon','null_layout');
    }

    /**
     * 整理数据
     * @param $param
     * @return array
     */
    private function _checkData($param){
        $data = array();
        $data['lot_name'] = trim($param['wintips']);
        if($param['start_time'] != ''){
            $data['start_time'] = strtotime($param['start_time']);
        }
        if($param['end_time'] != ''){
            $data['end_time'] = strtotime($param['end_time']);
        }
        $data['lot_discription'] = $param['acexplain'];
        $data['lot_type'] = intval($param['show_type']);
        $data['lot_weight'] = floatval($param['rate_weight']);
        $data['lot_count'] = 1;
        if($data['lot_type'] == 0){
            $data['lot_count'] = intval($param['member_number']);
        }

        //上传指针图片
        if (!empty($_FILES['pointerimage']['name'])){
            $upload = new UploadFile();
            $upload->set('default_dir',ATTACH_LOTTERY_DIAL);
            $result = $upload->upfile('pointerimage');
            if ($result){
                $data['lot_dial_pointer'] = $upload->file_name;
            }else {
                showMessage($upload->error,'','','error');
            }
        }
        //上传转盘图片
        if (!empty($_FILES['turntableimage']['name'])){
            $upload = new UploadFile();
            $upload->set('default_dir',ATTACH_LOTTERY_DIAL);
            $result = $upload->upfile('turntableimage');
            if ($result){
                $data['lot_dial_bg'] = $upload->file_name;
            }else {
                showMessage($upload->error,'','','error');
            }
        }
        //上传活动背景图片
        if (!empty($_FILES['active_bg_image']['name'])){
            $upload = new UploadFile();
            $upload->set('default_dir',ATTACH_LOTTERY_DIAL);
            $result = $upload->upfile('active_bg_image');
            if ($result){
                $data['lot_bg'] = $upload->file_name;
            }else {
                showMessage($upload->error,'','','error');
            }
        }
        $lot_info = $this->_checkPrizeData($param);
        $data['lot_info'] = serialize($lot_info);

        return $data;
    }

    /**
     * 整理奖项数据
     * @param $param
     * @return array
     */
    private function _checkPrizeData($param){
        $data = array();
        if(empty($param['rate_name']) || empty($param['prize_type']) || empty($param['prize'])){
            showMessage('奖项信息错误');
        }
        foreach($param['rate_name'] as $k => $val){
            $data[$k]['rate_name'] = $val;
            $data[$k]['prize_type'] = $param['prize_type'][$k];
            $prize = array();
            switch($data[$k]['prize_type']){
                case 0:
                    $prize['unprize'] = $param['prize'][$k]['unprize'];
                    break;
                case 1:
                    $prize['prize_amount'] = abs($param['prize'][$k]['prize_amount']);
                    $prize['prize_num'] = ceil(abs($param['prize'][$k]['prize_num']));
                    break;
                case 2:
                    $prize['prize_amount'] = abs($param['prize'][$k]['prize_amount']);
                    $prize['coupon_title'] = $param['prize'][$k]['coupon_title'];
                    $prize['coupon_quota'] = $param['prize'][$k]['coupon_quota'];
                    $prize['coupon_id'] = $param['prize'][$k]['coupon_id'];
                    break;
                case 3:
                    $prize['prize_amount'] = abs($param['prize'][$k]['prize_amount']);
                    $prize['prize_name'] = $param['prize'][$k]['prize_name'];
                    break;
            }
            $data[$k]['prize'] = $prize;
        }
        return $data;
    }

    private function _getPrizeType($param = 0){
        $return_str = '未中奖';
        switch($param){
            case 1:
                $return_str = '积分';break;
            case 2:
                $return_str = '平台红包';break;
            case 3:
                $return_str = '实物';break;
        }
        return $return_str;
    }
}