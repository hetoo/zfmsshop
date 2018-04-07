<?php
/**
 * 大转盘抽奖
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

class lottery_dialControl extends BaseHomeControl{
    private $member_info;
    function __construct() {
        parent::__construct();
        if(intval($_SESSION['member_id']) > 0){
            $this->member_info = Model('member')->getMemberInfoByID($_SESSION['member_id']);
        }
        loadfunc('json_output');
    }

    /**
     * 获取抽奖活动详情
     */
    public function dial_infoOp() {
        $dial_id = intval($_POST['lot_id']);
        if($dial_id <= 0){
            output_error('活动不存在或已结束');
        }
        $dial_model = Model('lottery_dial');
        $dial_info = $dial_model->getOneDialActivityById($dial_id);
        if(empty($dial_info)){
            output_error('活动不存在');
        }
        $dial_info['prize_size'] = count(unserialize($dial_info['lot_info']));
        $dial_info['lot_dial_bg'] = file_exists(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$dial_info['lot_dial_bg'])?UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$dial_info['lot_dial_bg']:UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.'/images/default_dial.png';
        $dial_info['lot_dial_pointer'] = file_exists(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$dial_info['lot_dial_pointer'])?UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$dial_info['lot_dial_pointer']:UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.'/images/default_pointer.png';
        if($dial_info['lot_bg'] != ''){
            $dial_info['lot_bg'] = UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$dial_info['lot_bg'];
        }
        unset($dial_info['lot_info']);
        unset($dial_info['add_time']);
        $return_arr = array();
        $return_arr['dial_info'] = $dial_info;

        // 获取抽奖资格
        $dial_qua = 0;
        if(!empty($this->member_info)){
            $qua_count = $this->_getPrizeQualifications($dial_info);
            $dial_qua = intval($qua_count['lot_amount']);
        }

        //获取中奖列表
        $condition = array();
        $condition['rate_type'] = array('gt',0);
        $condition['lot_id'] = $dial_id;
        $prize_list = new ArrayObject();
        $t_prize_list = $dial_model->getUnionListDialPrize($condition,100);
        if(!empty($t_prize_list)){
            $tmp_arr = array();
            foreach($t_prize_list as $key => $value){
                $tmp_arr[$key]['member_name'] = mb_substr($value['member_name'],0,1).'****'.mb_substr($value['member_name'],-1);
                $prize = unserialize($value['prize_info']);
                switch($value['rate_type']){
                    case 1:
                        $tmp_arr[$key]['prize_info'] = $prize['prize_num'].'积分';
                        break;
                    case 2:
                        $tmp_arr[$key]['prize_info'] = $prize['coupon_title'];
                        break;
                    case 3:
                        $tmp_arr[$key]['prize_info'] = $prize['prize_name'];
                        break;
                }
            }
            $prize_list = $tmp_arr;
        }
        output_data(array('dial_info'=>$dial_info,'dail_qua'=>$dial_qua,'prize_list'=>$prize_list));
    }

    /**
     * 获取抽奖结果
     */
    public function dial_prizeOp(){
        $dial_id = intval($_POST['lot_id']);
        if($dial_id <= 0){
            output_error('活动不存在或已结束');
        }
        $dial_model = Model('lottery_dial');
        $field = "lot_id,start_time,end_time,lot_type,lot_state,lot_weight,lot_count,lot_info";
        $dial_info = $dial_model->getOneDialActivityById($dial_id, $field);
        if(empty($dial_info)){
            output_error('活动不存在');
        }elseif(time() < $dial_info['start_time'] || $dial_info['lot_state'] == 0){
            output_error('活动尚未开始');
        }elseif($dial_info['end_time'] < time() || in_array($dial_info['lot_state'],array(2,3))){
            output_error('活动已结束或已关闭');
        }
        $qua_condition = array();
        $qua_condition['lot_id'] = $dial_info['lot_id'];
        $qua_condition['member_id'] = $this->member_info['member_id'];
        $qua_condition['lot_type'] = 'dial';
        $qua_info = $dial_model->getOneDialQualifications($this->member_info['member_id'], $qua_condition);
        if(empty($qua_info) || $qua_info['lot_amount'] == 0){
            output_error('您暂无抽奖机会');
        }
        //生成抽奖结果
        list($prize_grade,$lot_detial) = $this->_getPrizeNum($dial_info);

        if($prize_grade > -1){
            switch($lot_detial['prize_type']){
                case 1:
                    $lot_detial['prize_detial'] = $lot_detial['prize']['prize_num'].'积分';
                    break;
                case 2:
                    $lot_detial['prize_detial'] = $lot_detial['prize']['coupon_title'];
                    break;
                case 3:
                    $lot_detial['prize_detial'] = $lot_detial['prize']['prize_name'];
                    break;
                case 0:
                    $lot_detial['prize_detial'] = $lot_detial['prize']['unprize'];
                    break;
            }
            unset($lot_detial['prize']);
        }else{
            $lot_detial = array();
            $lot_detial['prize_detial'] = '奖品已经抽完了，下次再来吧';
            $lot_detial['prize_type'] = -1;
        }

        output_data(array('prize_grade'=>$prize_grade,'prize_info'=>$lot_detial));
    }

    // 获取抽奖资格
    private function _getPrizeQualifications($dial_info){
        $return_data = array();
        if($this->member_info['member_id'] > 0){
            $member_id = $this->member_info['member_id'];
            $model_dial = Model('lottery_dial');
            //抽奖方式处理
            $lot_amount = 0;
            if($dial_info['lot_type'] > 0){
                //获取活动期间完成的订单数
                $order_model = Model('order');
                $o_condition = array();
                $o_condition['buyer_id'] = $member_id;
                $o_condition['order_state'] = 40;
                $o_condition['finnshed_time'] = array('time',array($dial_info['start_time'],$dial_info['end_time']));
                $order_count = $order_model->getOrderCount($o_condition);
                //获取已经抽奖的次数
                $prize_count = $model_dial->getCountDialPrize($member_id,array('lot_id'=>$dial_info['lot_id']));
                $lot_amount = ($order_count - $prize_count > 0) ? $order_count - $prize_count : 0;
            }else{
                $lot_amount = $dial_info['lot_count'] > 0 ? $dial_info['lot_count'] : 999999;
            }

            $condition = array();
            $condition['lot_id'] = $dial_info['lot_id'];
            $condition['member_id'] = $member_id;
            $condition['lot_type'] = 'dial';
            //获取抽奖资格记录
            $qual_data = $model_dial->getOneDialQualifications($member_id,$condition);

            //整理数据
            $return_data['lot_id'] = $dial_info['lot_id'];
            $return_data['member_id'] = $member_id;
            $return_data['lot_type'] = 'dial';
            $return_data['lot_amount'] = $lot_amount;

            if(empty($qual_data)){
                //添加抽奖资格记录
                $model_dial->addDialQualifications($member_id,$return_data);
            } else {
                if($dial_info['lot_type'] > 0) {
                    //更新抽奖资格记录
                    $model_dial->editDialQualifications($member_id, array('qua_id' => $qual_data['qua_id']), array('lot_amount' => $lot_amount));
                }else{
                    $return_data['lot_amount'] = $qual_data['lot_amount'];
                }
            }
        }
        return $return_data;
    }

    //生成抽奖结果
    private function _getPrizeNum($dial_info){
        $lot_info = unserialize($dial_info['lot_info']);
        $prize_count = 0;  //各奖项奖品总数
        $prize_num_arr = array(); // 奖项区间
        $prize_count_arr = array(); // 奖品数
        $unprize_arr = array(); // 未中奖奖项索引
        $return_prize = -1; // 奖项索引

        foreach($lot_info as $k => $val){
            if($val['prize_type'] != 0){
                $prize_count += $val['prize']['prize_amount'];
                $prize_num_arr[$k] = $prize_count;
                $prize_count_arr[$k] = $val['prize']['prize_amount'];
            } else {
                $unprize_arr[] = $k;
            }
        }
        $dial_model = Model('lottery_dial');
        $condition = array();
        $condition['lot_id'] = $dial_info['lot_id'];
        $cout = $dial_model->getUnionCountDialPrize($condition);

        if($prize_count > $cout){
            $lot_weight = $dial_info['lot_weight'] /100.0;  //中奖率
            if(empty($unprize_arr))$lot_weight = 1;
            $sample_size = $prize_count / $lot_weight; //抽奖样本大小
            $lot_number = mt_rand(1, ceil($sample_size)); //获取抽奖结果
            if($prize_count < $lot_number){
                $return_prize = array_rand(array_flip($unprize_arr));
            }else{
                $pre_num = 0;
                foreach($prize_num_arr as $k => $val){
                    if($pre_num < $lot_number && $lot_number <= $val){
                        $return_prize = $k;
                        break;
                    }
                    $pre_num = $val;
                }
            }
        }elseif($prize_count <= $cout && !empty($unprize_arr)){
            $return_prize = array_rand(array_flip($unprize_arr));
        }

        // 验证抽奖结果
        if($return_prize != -1 && !in_array($return_prize,$unprize_arr)){
            $this->_checkPrizeNum($dial_info,$lot_info[$return_prize]);
        }

        // 添加抽奖记录
        if($return_prize != -1){
            $this->_addPrizeInfo($dial_info,$lot_info[$return_prize]);
        }
        return array($return_prize,$lot_info[$return_prize]);
    }

    //添加抽奖记录
    private function _addPrizeInfo($dial_info, $lot_info){
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        $data = array();
        $data['lot_id'] = $dial_info['lot_id'];
        $data['member_id'] = $member_info['member_id'];
        $data['member_name'] = $member_info['member_name'];
        $data['add_time'] = time();
        $data['rate_name'] = $lot_info['rate_name'];
        $data['rate_type'] = $lot_info['prize_type'];
        $data['prize_info'] = serialize($lot_info['prize']);
        $data['prize_state'] = $this->_sendPrize($lot_info,$member_info);
        $dial_model = Model('lottery_dial');
        $res = $dial_model->addDialPrize($member_info['member_id'],$data);
        if(!$res){
            output_error('服务器忙请稍后再试');
        }else{
            $condition = array();
            $condition['lot_id'] = $dial_info['lot_id'];
            $condition['member_id'] = $this->member_info['member_id'];
            $condition['lot_type'] = 'dial';
            $update_data = array();
            $update_data['lot_amount'] = array('exp','lot_amount-1');
            $dial_model->editDialQualifications($this->member_info['member_id'], $condition, $update_data);
        }
    }

    //派发虚拟奖品
    private function _sendPrize($lot_info,$member_info){
        $return_data = 0;
        if($lot_info['prize_type'] == 3) return $return_data;
        if($lot_info['prize_type'] == 1){
            $prize_info = $lot_info['prize'];
            $points = intval($prize_info['prize_num']);
            //增加积分
            $result = Model('points')->savePointsLog('lottery',array('pl_memberid'=>$member_info['member_id'],'pl_membername'=>$member_info['member_name'],'pl_points'=>$points));
            if ($result) {
                $return_data = 1;
            }
        }elseif($lot_info['prize_type'] == 2){ //平台红包发奖
            $prize_info = $lot_info['prize'];
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

    // 抽奖结果验证
    private function _checkPrizeNum($dial_info,$lot_info){
        $dial_model = Model('lottery_dial');
        $condition = array();
        $condition['lot_id'] = $dial_info['lot_id'];
        $condition['rate_name'] = $lot_info['rate_name'];
        $condition['rate_type'] = $lot_info['prize_type'];
        $cout = $dial_model->getUnionCountDialPrize($condition);
        if($cout >= $lot_info['prize']['prize_amount']){
            $this->_getPrizeNum($dial_info);
        }
    }

}