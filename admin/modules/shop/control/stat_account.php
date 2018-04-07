<?php
/**
 * 统计管理（商城账户统计）
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class stat_accountControl extends SystemControl
{

    public function __construct()
    {
        parent::__construct();
        Language::read('stat');
    }

    public function indexOp()
    {
        $this->accountOp();
    }

    public function accountOp(){

        Tpl::showpage('stat.account');
    }

    /**
     * 输出平台总数据
     */
    public function get_plat_incomeOp(){
        $model = Model('stat');
        $usable_amount_where = array();
        $store_amount_where = array();
        $order_amount_where = array();
        $vr_order_amount_where = array();
        $refund_return_where = array();
        $vr_refund_where = array();
        if (trim($_GET['query_start_date']) && trim($_GET['query_end_date'])) {
            $sdate = strtotime($_GET['query_start_date']);
            $edate = strtotime($_GET['query_end_date']) + 86400 -1;
            $usable_amount_where['lg_add_time'] = array('between', array($sdate,$edate));
            $store_amount_where['cost_time'] = array('between', array($sdate,$edate));
            $order_amount_where['add_time'] = array('between', array($sdate,$edate));
            $vr_order_amount_where['add_time'] = array('between', array($sdate,$edate));
            $refund_return_where['admin_time'] = array('between', array($sdate,$edate));
            $vr_refund_where['admin_time'] = array('between', array($sdate,$edate));
        } elseif (trim($_GET['query_start_date'])) {
            $sdate = strtotime($_GET['query_start_date']);
            $usable_amount_where['lg_add_time'] = array('egt', $sdate);
            $store_amount_where['cost_time'] = array('egt', $sdate);
            $order_amount_where['add_time'] = array('egt', $sdate);
            $vr_order_amount_where['add_time'] = array('egt', $sdate);
            $refund_return_where['admin_time'] = array('egt', $sdate);
            $vr_refund_where['admin_time'] = array('egt', $sdate);
        } elseif (trim($_GET['query_end_date'])) {
            $edate = strtotime($_GET['query_end_date']) + 86400 -1;
            $usable_amount_where['lg_add_time'] = array('elt', $edate);
            $store_amount_where['cost_time'] = array('elt', $edate);
            $order_amount_where['add_time'] = array('elt', $edate);
            $vr_order_amount_where['add_time'] = array('elt', $edate);
            $refund_return_where['admin_time'] = array('elt', $edate);
            $vr_refund_where['admin_time'] = array('elt', $edate);
        }else{
            $sdate = strtotime(date('Y-m-01',strtotime(date("Y-m-d"))));
            $edate = time();
            $usable_amount_where['lg_add_time'] = array('between', array($sdate,$edate));
            $store_amount_where['cost_time'] = array('between', array($sdate,$edate));
            $order_amount_where['add_time'] = array('between', array($sdate,$edate));
            $vr_order_amount_where['add_time'] = array('between', array($sdate,$edate));
            $refund_return_where['admin_time'] = array('between', array($sdate,$edate));
            $vr_refund_where['admin_time'] = array('between', array($sdate,$edate));
        }
        //获取平台总数据
        $order_amount_where['order_state'] = array('gt', ORDER_STATE_NEW);
        $order_amount_where['payment_code'] = array('exp',"(payment_code='offline' and order_state = '".ORDER_STATE_SUCCESS."') or (payment_code<>'offline')");
        $order_amount = $model->statByOrder($order_amount_where, 'sum(order_amount) as allnum');

        $vr_order_amount_where['order_state'] = array('gt', ORDER_STATE_NEW);
        $vr_order_amount = $model->statByFlowstat('vr_order', $vr_order_amount_where, 'sum(order_amount) as allnum');

        $refund_return_where['refund_state'] = 3;
        $refund_return = $model->statByFlowstat('refund_return', $refund_return_where, 'sum(refund_amount) as allnum');

        $vr_refund_where['admin_state'] = 2;
        $vr_refund = $model->statByFlowstat('vr_refund', $vr_refund_where, 'sum(refund_amount) as allnum');

        $usable_amount = $model->getPredepositInfo($usable_amount_where, 'sum(lg_av_amount+lg_freeze_amount) as allnum');
        $store_amount = $model->statByFlowstat('store_cost', $store_amount_where, 'sum(cost_price) as allnum');
        echo '<div class="title"><h3>平台商城账户情况一览</h3></div>';
        echo '<dl class="row"><dd class="opt"><ul class="nc-row">';
        echo '<li title="订单支付金额：'. number_format($order_amount[0]['allnum'] + $vr_order_amount[0]['allnum'],2).'元"><h4>订单支付金额</h4><h6>期间内包含实物和虚拟订单(元)</h6><h2 id="count-number" class="timer" data-speed="1500" data-to="'. ($order_amount[0]['allnum'] + $vr_order_amount[0]['allnum']).'"></h2></li>';
        echo '<li title="退款金额：'. number_format($refund_return[0]['allnum'] + $vr_refund[0]['allnum'],2).'元"><h4>退款金额</h4><h6>期间内包含实物和虚拟订单退款(元)</h6><h2 id="count-number" class="timer" data-speed="1500" data-to="'. ($refund_return[0]['allnum'] + $vr_refund[0]['allnum']).'"></h2></li>';
        echo '<li title="预存款金额：'. number_format($usable_amount[0]['allnum'],2).'元"><h4>预存款金额</h4><h6>期间内的总余额(元)</h6><h2 id="count-number" class="timer" data-speed="1500" data-to="'. ($usable_amount[0]['allnum']).'"></h2></li>';
        echo '<li title="店铺费用：'. number_format($store_amount[0]['allnum'],2).'元"><h4>店铺费用</h4><h6>期间内的总金额(元)</h6><h2 id="count-number" class="timer" data-speed="1500" data-to="'. ($store_amount[0]['allnum']).'"></h2></li>';
        echo '</ul></dd><dl>';
        exit();
    }
}