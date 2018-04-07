<?php
/**
 * 向第三方发消息
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class realtime_msgLogic {

    /**
     * 更新使用的代金券状态
     * @param $voucher_list
     * @throws Exception
     */
    public function editVoucherState($voucher_list,$chain_id) {
        $model_voucher = Model('voucher');
        if ($chain_id) $model_voucher = Model('chain_voucher');
        $send = new sendMemberMsg();
        foreach ($voucher_list as $store_id => $voucher_info) {
            $update = $model_voucher->editVoucher(array('voucher_state'=>2),array('voucher_id'=>$voucher_info['voucher_id']),$voucher_info['voucher_owner_id']);
            if ($update) {
                $update = $model_voucher->editVoucherTemplate(array('voucher_t_id'=>$voucher_info['voucher_t_id']), array('voucher_t_used'=>array('exp','voucher_t_used+1')));
                if ($update) {
                    // 发送用户消息
                    $send->set('member_id', $voucher_info['voucher_owner_id']);
                    $send->set('code', 'voucher_use');
                    $param = array();
                    $param['voucher_code'] = $voucher_info['voucher_code'];
                    $param['voucher_url'] = urlMember('member_voucher', 'index');
                    $send->send($param);
                } else {
                    return callback(false,'更新代金券状态失败tpl:'.$voucher_info['voucher_t_id']);
                }
            } else {
                return callback(false,'更新代金券状态失败vcode:'.$voucher_info['voucher_code']);
            }
        }
        return callback(true);
    }

    /**
     * 更新使用的平台红包状态
     * @param $input_rpt_info
     * @throws Exception
     */
    public function editRptState($input_rpt_info, $pay_sn) {
        $model_rpt = Model('redpacket');
        $update = $model_rpt->editRedpacket(array('rpacket_id'=>$input_rpt_info['rpacket_id']),array('rpacket_state'=>2,'rpacket_order_id'=>$pay_sn),$input_rpt_info['rpacket_owner_id']);
        if ($update) {
            $update = $model_rpt->editRptTemplate(array('rpacket_t_id'=>$input_rpt_info['rpacket_t_id']), array('rpacket_t_used'=>array('exp','rpacket_t_used+1')));
            if ($update) {
                $send = new sendMemberMsg();
                // 发送用户店铺消息
                $send->set('member_id', $input_rpt_info['rpacket_owner_id']);
                $send->set('code', 'rpt_use');
                $param = array();
                $param['rpacket_code'] = $input_rpt_info['rpacket_code'];
                $param['rpacket_url'] = urlMember('member_redpacket', 'index');
                $send->send($param);
            } else {
                return callback(false,'更新红包状态失败tpl:'.$input_rpt_info['rpacket_t_id']);
            }
        } else {
            return callback(false,'更新红包状态失败vcode:'.$input_rpt_info['rpacket_code']);
        }
        return callback(true);
    }

    /**
     * 发送店铺消息
     */
    public function sendStoreMsg($param) {
        $send = new sendStoreMsg();
        $send->set('code', $param['code']);
        $send->set('store_id', $param['store_id']);
        $send->send($param['param']);
        return callback(true);
    }

    /**
     * 发送会员消息
     */
    public function sendMemberMsg($param) {
        $send = new sendMemberMsg();
        $send->set('code', $param['code']);
        $send->set('member_id', $param['member_id']);
        if (!empty($param['number']['mobile'])) $send->set('mobile', $param['number']['mobile']);
        if (!empty($param['number']['email'])) $send->set('email', $param['number']['email']);
        $send->send($param['param']);
        return callback(true);
    }

    /**
     * 发送兑换码
     * @param unknown $param
     * @return boolean
     */
    public function sendVrCode($param) {
        if (empty($param) && !is_array($param)) return callback(true);
        $condition = array();
        $condition['order_id'] = $param['order_id'];
        $condition['buyer_id'] = $param['buyer_id'];
        $condition['vr_state'] = 0;
        $condition['refund_lock'] = 0;
        $code_list = Model('vr_order')->getOrderCodeList($condition,'vr_code,vr_indate');
        if (empty($code_list)) return callback(true);

        $content = '';
        foreach ($code_list as $v) {
            $content .= $v['vr_code'].',';
        }

        $tpl_info = Model('mail_templates')->getTplInfo(array('code'=>'send_vr_code'));
        $data = array();
        $data['site_name']  = C('site_name');
        $data['goods_name']  = $param['goods_name'];
        $data['vr_code'] = rtrim($content,',');
        $message    = ncReplaceText($tpl_info['content'],$data);
        $result = Model('realtime_msg')->addShort($param["buyer_phone"],$message);
        if (!$result) {
            return callback(false,'兑换码发送失败order_id:'.$param['order_id']);
        } else {
            return callback(true);
        }
    }

    /**
     * 发送提货码短信消息
     */
    public function sendPickupcode($param) {
        $dorder_info = Model('chain_order')->getDeliveryOrderInfo(array('order_id' => $param['order_id']), 'reciver_mobphone');
        $tpl_info = Model('mail_templates')->getTplInfo(array('code'=>'send_pickup_code'));
        $data = array();
        $data['site_name'] = C('site_name');
        $data['pickup_code'] = $param['pickup_code'];
        $message = ncReplaceText($tpl_info['content'],$data);
        $result = Model('realtime_msg')->addShort($dorder_info['reciver_mobphone'],$message);
        if (!$result) {
            return callback(false,'发送提货码短信消息失败order_id:'.$param['order_id']);
        } else {
            return callback(true);
        }
    }

    /**
     * 发送门店提货码短信消息
     */
    public function sendChainCode($order_info) {
        $tpl_info = Model('mail_templates')->getTplInfo(array('code'=>'send_chain_code'));
        $data = array();
        $data['site_name'] = C('site_name');
        $data['chain_code'] = $order_info['chain_code'];
        $data['order_sn'] = $order_info['order_sn'];
        $message = ncReplaceText($tpl_info['content'],$data);
        $result = Model('realtime_msg')->addShort($order_info['buyer_phone'],$message);
        if (!$result) {
            return callback(false,'发送门店提货码短信消息失败order_sn:'.$order_info['order_sn']);
        } else {
            return callback(true);
        }
    }

}
