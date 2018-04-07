<?php
/**
* 任务计划 - 分钟执行的任务
* 执行频率1-2分钟
*
*
*
* @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
* @license    http://www.shopnc.net
* @link       http://www.shopnc.net
* @since      File available since Release v1.1
*/
defined('InShopNC') or exit('Access Invalid!');

class realtimeControl extends BaseCronControl {
    public function indexOp() {
        $this->_realtime_msg();
        $this->_realtime_promotion();
    }

    /**
     * 向第三方发消息通知
     */
    private function _realtime_msg() {
        $sms = new Sms();
        $logic_wx_api = Logic('wx_api');
        $model_realtime_msg = Model('realtime_msg');
        $access_token = $logic_wx_api->getAccessToken();
        $list = $model_realtime_msg->getMsgList(array(), '', 60, 'log_id asc');//默认每次处理60个
        if(!empty($list) && is_array($list)) {
            foreach($list as $k => $v) {
                $msg_type = $v['msg_type'];//类型:1邮件,2短信,3微信
                $result = true;
                switch ($msg_type) {
                    case '1':
                        try {
                            $_v = C('email_pass');
                            if (!empty($_v)) $model_realtime_msg->send($v['to_id'],$v['subject'],$v['log_msg']);
                            $result = true;
                        } catch (Exception $ex) {
                            $result = false;
                        }
                        break;
                    case '2':
                        $_v = C('sms.serialNumber');
                        if (!empty($_v)) $result = $sms->send($v['to_id'],$v['log_msg']);
                        break;
                    case '3':
                        if (!empty($access_token)) $result = $logic_wx_api->sendTemplate($access_token,$v);
                        break;
                }
                if($result) $model_realtime_msg->delMsg($v);
            }
        }
    }

    /**
     * 价格促销时间(限时折扣、秒杀、闪购)
     */
    private function _realtime_promotion() {
        $model_p_time = Model('p_time');
        $model_goods = Model('goods');
        $condition = array();
        $condition['end_time'] = array('elt',TIMESTAMP);
        $model_p_time->del($condition);//过期处理
        Model('p_xianshi')->editExpireXianshi();
        Model('p_spike')->editExpireSpike();
        Model('p_flash')->editExpireFlash();
        
        $condition = array();
        $condition['is_update'] = 0;
        $condition['start_time'] = array('elt',TIMESTAMP);
        $list = $model_p_time->table('p_time')->where($condition)->select();
        if(!empty($list) && is_array($list)) {//更新处理
            foreach($list as $k => $v) {
                $model_goods->editGoodsPromotionPrice(array('goods_id' => $v['goods_id']),$v);
                $model_p_time->edit(array('log_id' => $v['log_id']),array('is_update' => 1));
            }
        }
    }
}
