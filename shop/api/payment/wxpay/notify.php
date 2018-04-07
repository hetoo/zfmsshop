<?php
// ini_set('date.timezone','Asia/Shanghai');
// error_reporting(E_ERROR);

defined('InShopNC') or exit('Access Invalid!');

require_once BASE_PATH.'/api/payment/wxpay/lib/WxPay.Api.php';
require_once BASE_PATH.'/api/payment/wxpay/lib/WxPay.Notify.php';
require_once BASE_PATH.'/api/payment/wxpay/log.php';

//初始化日志
$logHandler= new CLogFileHandler(BASE_DATA_PATH.'/log/wxpay/'.date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}

		if (in_array($data['attach'],array('v','r','p','s','c'))) {
		    $order_type = $data['attach'];
		} else {
		    $order_pay_info = Model('order')->getOrderPayInfo(array('pay_sn'=> $data['out_trade_no']));
		    if(empty($order_pay_info)){
		        $order_type = 'v';
		    } else {
		        $order_type = 'r';
		    }
		}

		$logic_payment = Logic('payment');

		if ($order_type == 'r') {
		    $result = $logic_payment->getRealOrderInfo($data['out_trade_no']);
		    if(!$result['state']) {
		        return false;
		    }
		    if ($result['data']['api_pay_state']) {
		        return true;
		    }
		    $order_list = $result['data']['order_list'];
		    $result = $logic_payment->updateRealOrder($data['out_trade_no'], 'wx_saoma', $order_list, $data["transaction_id"]);
		    if (!$result['state']) {
		        return false;
		    }
		    $api_pay_amount = 0;
		    if (!empty($order_list)) {
		        foreach ($order_list as $order_info) {
		            $api_pay_amount += $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
		        }
		    }
		} elseif ($order_type == 'p') {
		    $result = $logic_payment->getPdOrderInfo($data['out_trade_no']);
            $order_info = $result['data'];
            if ($order_info['pdr_payment_state'] == 1) {
                return true;
            }
            $result = $logic_payment->getPaymentInfo('wxpay');
		    $result = $logic_payment->updatePdOrder($data['out_trade_no'],$data["transaction_id"],$result['data'],$order_info);
		    if (!$result['state']) {
		        return false;
		    }
		    $api_pay_amount = $order_info['api_pay_amount'];
        } elseif ($order_type == 's') {
            $result = $logic_payment->getSEarnetMoneyOrderInfo($data['out_trade_no']);
            $order_info = $result['data'];
            if ($order_info['etm_payment_state'] == 1) {
                return true;
            }
            $result = $logic_payment->getPaymentInfo('wxpay');
            $result = $logic_payment->updateSEarnetMoneyOrder($data['out_trade_no'],$data["transaction_id"],$result['data'],$order_info);
            if (!$result['state']) {
                return false;
            }
            $api_pay_amount = $order_info['api_pay_amount'];
        } elseif ($order_type == 'c') {
            $result = $logic_payment->getCEarnetMoneyOrderInfo($data['out_trade_no']);
            $order_info = $result['data'];
            if ($order_info['etm_payment_state'] == 1) {
                return true;
            }
            $result = $logic_payment->getPaymentInfo('wxpay');
            $result = $logic_payment->updateCEarnetMoneyOrder($data['out_trade_no'],$data["transaction_id"],$result['data'],$order_info);
            if (!$result['state']) {
                return false;
            }
            $api_pay_amount = $order_info['api_pay_amount'];
		} else {
		    $result = $logic_payment->getVrOrderInfo($data['out_trade_no']);
		    if (!in_array($result['data']['order_state'],array(ORDER_STATE_NEW,ORDER_STATE_CANCEL))) {
		        return true;
		    }
		    $order_info = $result['data'];
		    $result = $logic_payment->updateVrOrder($data['out_trade_no'], 'wx_saoma', $order_info, $data["transaction_id"]);
		    if (!$result['state']) {
		        return false;
		    }
		    $api_pay_amount = $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
		}
		//记录消费日志
		if ($order_type == 'r') {
		    $log_buyer_id = $order_list[0]['buyer_id'];
		    $log_buyer_name = $order_list[0]['buyer_name'];
		    $log_desc = '实物订单使用微信扫码成功支付，支付单号：'.$data['out_trade_no'];
		} elseif ($order_type == 'p') {
            $log_buyer_id = $order_info['pdr_member_id'];
            $log_buyer_name = $order_info['pdr_member_name'];
            $log_desc = '预存款充值成功，使用微信扫码成功支付，充值单号：'.$data['out_trade_no'];
		} else {
		    $log_buyer_id = $order_info['buyer_id'];
		    $log_buyer_name = $order_info['buyer_name'];
		    $log_desc = '虚拟订单使用微信扫码成功支付，支付单号：'.$data['out_trade_no'];
		}
        if(!in_array($order_type,array('s','c')))
		QueueClient::push('addConsume', array('member_id'=>$log_buyer_id,'member_name'=>$log_buyer_name,
		'consume_amount'=>ncPriceFormat($api_pay_amount),'consume_time'=>TIMESTAMP,'consume_remark'=>$log_desc));
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
