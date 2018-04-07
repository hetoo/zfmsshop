<?php
/**
 * 交易统计
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit('Access Invalid!');

class statistics_tradeControl extends BaseSellerControl
{
    private $search_arr;//处理后的参数

    public function __construct()
    {
        parent::__construct();
        Language::read('stat');
        import('function.datehelper');
        $model = Model('stat');
        //存储参数
        $this->search_arr = $_REQUEST;
        //处理搜索时间
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'month';
        }
        $this->search_arr = $model->dealwithSearchTime($this->search_arr);
        //获得系统年份
        $year_arr = getSystemYearArr();
        //获得系统月份
        $month_arr = getSystemMonthArr();
        //获得本月的周时间段
        $week_arr = getMonthWeekArr($this->search_arr['week']['current_year'], $this->search_arr['week']['current_month']);
        Tpl::output('year_arr', $year_arr);
        Tpl::output('month_arr', $month_arr);
        Tpl::output('week_arr', $week_arr);
        Tpl::output('search_arr', $this->search_arr);
    }

    /**
     * 店铺流量统计
     */
    public function indexOp()
    {
        $store_id = intval($_SESSION['store_id']);

        $model = Model('stat');
        //获得搜索的开始时间和结束时间
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        //实体订单
        $where = array();
        $where['store_id'] = $store_id;
        $where['add_time'] = array('between', $searchtime_arr);
        $where['order_state'] = array('gt', ORDER_STATE_NEW);
        $where['payment_code'] = array('exp',"(payment_code='offline' and order_state = '".ORDER_STATE_SUCCESS."') or (payment_code<>'offline')");
        $order_amount = $model->statByOrder($where, 'sum(order_amount) as allnum');
        //虚拟订单
        $where = array();
        $where['store_id'] = $store_id;
        $where['add_time'] = array('between', $searchtime_arr);
        $where['order_state'] = array('gt', ORDER_STATE_NEW);
        $vr_order_amount = $model->statByFlowstat('vr_order', $where, 'sum(order_amount) as allnum');
        $order_amount_sum = number_format($order_amount[0]['allnum'] + $vr_order_amount[0]['allnum'],2);
        Tpl::output('order_amount_sum', $order_amount_sum);
        //实体订单退款
        $where = array();
        $where['store_id'] = $store_id;
        $where['refund_state'] = 3;
        $where['admin_time'] = array('between', $searchtime_arr);
        $refund_return = $model->statByFlowstat('refund_return', $where, 'sum(refund_amount) as allnum');
        //虚拟订单退款
        $where = array();
        $where['store_id'] = $store_id;
        $where['admin_state'] = 2;
        $where['admin_time'] = array('between', $searchtime_arr);
        $vr_refund = $model->statByFlowstat('vr_refund', $where, 'sum(refund_amount) as allnum');
        $refund_amount_sum = number_format($refund_return[0]['allnum'] + $vr_refund[0]['allnum'],2);
        Tpl::output('refund_amount_sum', $refund_amount_sum);
        //店铺费用
        $where = array();
        $where['cost_store_id'] = $store_id;
        $where['cost_time'] = array('between', $searchtime_arr);
        $store_amount = $model->statByFlowstat('store_cost', $where, 'sum(cost_price) as allnum');
        Tpl::output('store_amount_sum', number_format($store_amount[0]['allnum'],2));

        Tpl::showpage('stat.trade.store');
    }
}