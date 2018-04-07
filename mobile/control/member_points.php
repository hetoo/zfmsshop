<?php
/**
 * 积分
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class member_pointsControl extends mobileMemberControl {
    public function __construct(){
        parent::__construct();
    }
    /**
     * 积分日志列表
     */
    public function pointslogOp(){
        $where = array();
        $where['pl_memberid'] = $this->member_info['member_id'];
        //查询积分日志列表
        $points_model = Model('points');
        $log_list = $points_model->getPointsLogList($where, '*', 0, $this->page);
        $page_count = $points_model->gettotalpage();
        output_data(array('log_list' => $log_list), mobile_page($page_count));
    }

    /**
     * 兑换代金券
     *
     */
    public function voucherexchangeOp(){
        if (C('voucher_allow') != 1){
            output_error('系统未开启代金券功能');
        }
        $vid = intval($_POST['id']);
        if ($vid <= 0){
            output_error('参数错误');
        }
        $model_voucher = Model('voucher');
        //验证是否可以兑换代金券
        $data = $model_voucher->getCanChangeTemplateInfo($vid,intval($this->member_info['member_id']),intval($this->member_info['store_id']));
        if ($data['state'] == false){
            output_error($data['msg']);
        }
        //添加代金券信息
        $data = $model_voucher->exchangeVoucher($data['info'],$this->member_info['member_id'],$this->member_info['member_name']);
        if ($data['state'] == true){
            output_data('1');
        } else {
            output_error($data['msg']);
        }
    }

    /**
     * 兑换红包
     */
    public function redpacketexchangeOp(){
        if (C('redpacket_allow') != 1){
            output_error('系统未开启红包功能');
        }
        $tid = intval($_POST['id']);
        if ($tid <= 0){
            output_error('参数错误');
        }
        $model_redpacket = Model('redpacket');
        //验证是否可以兑换红包
        $data = $model_redpacket->getCanChangeTemplateInfo($tid,intval($this->member_info['member_id']));
        if ($data['state'] == false){
            output_error($data['msg']);
        }
        //添加红包信息
        $data = $model_redpacket->exchangeRedpacket($data['info'],$this->member_info['member_id'],$this->member_info['member_name']);
        if ($data['state'] == true){
            output_data('1');
        } else {
            output_error($data['msg']);
        }
    }

    /**
     * 购物车添加礼品
     */
    public function prodexchangeOp() {
        if (C('pointprod_isuse') != 1){
            output_error('系统未开启积分兑换功能');
        }
        $pgid   = intval($_POST['id']);
        $quantity   = 1;
        if($pgid <= 0 || $quantity <= 0) {
            output_error('兑换失败');
        }

        //验证积分礼品是否存在购物车中
        $model_pointcart = Model('pointcart');
        $check_cart = $model_pointcart->getPointCartInfo(array('pgoods_id'=>$pgid,'pmember_id'=>$this->member_info['member_id']));
        if(!empty($check_cart)) {
            output_error('兑换礼品列表已有此礼品');
        }
        //验证是否能兑换
        $data = $model_pointcart->checkExchange($pgid, $quantity, $this->member_info['member_id']);
        if (!$data['state']){
            output_error($data['msg']);
        }
        $prod_info = $data['data']['prod_info'];

        $insert_arr = array();
        $insert_arr['pmember_id']       = $this->member_info['member_id'];
        $insert_arr['pgoods_id']        = $prod_info['pgoods_id'];
        $insert_arr['pgoods_name']      = $prod_info['pgoods_name'];
        $insert_arr['pgoods_points']    = $prod_info['pgoods_points'];
        $insert_arr['pgoods_choosenum'] = $prod_info['quantity'];
        $insert_arr['pgoods_image']     = $prod_info['pgoods_image_old'];
        $cart_state = $model_pointcart->addPointCart($insert_arr);
        if ($cart_state) {
            output_data('1');
        }else{
            output_error('兑换失败');
        }
    }

    /**
     * 积分礼品购物车首页
     */
    public function prod_cartOp() {
        $cart_goods = array();
        $model_pointcart = Model('pointcart');
        $data = $model_pointcart->getPCartListAndAmount(array('pmember_id'=>$this->member_info['member_id']));
        output_data(array('cart_list' => $data['data']['cartgoods_list'],'pgoods_pointall' => $data['data']['cartgoods_pointall']));
    }

    /**
     * 积分礼品购物车删除单个礼品
     */
    public function prod_dropOp() {
        $pcart_id   = intval($_POST['pc_id']);
        if($pcart_id <= 0) {
            output_error('删除失败');
        }
        $model_pointcart = Model('pointcart');
        $drop_state = $model_pointcart->delPointCartById($pcart_id,$this->member_info['member_id']);
        if ($drop_state){
            output_data('1');
        } else {
            output_error('删除失败');
        }
    }


    /**
     * 积分礼品购物车更新礼品数量
     */
    public function updateOp() {
        $pcart_id   = intval($_POST['cart_id']);
        $quantity   = intval($_POST['quantity']);
        //兑换失败提示
        $msg = '修改失败';
        if($pcart_id <= 0 || $quantity <= 0) {
            output_error($msg);
        }
        //验证礼品购物车信息是否存在
        $model_pointcart    = Model('pointcart');
        $cart_info  = $model_pointcart->getPointCartInfo(array('pcart_id'=>$pcart_id,'pmember_id'=>$this->member_info['member_id']));
        if (!$cart_info){
            output_error($msg);
        }

        //验证是否能兑换
        $data = $model_pointcart->checkExchange($cart_info['pgoods_id'], $quantity, $this->member_info['member_id']);
        if (!$data['state']){
            output_error($data['msg']);
        }
        $prod_info = $data['data']['prod_info'];
        $quantity = $prod_info['quantity'];

        $cart_state = true;
        //如果数量发生变化则更新礼品购物车内单个礼品数量
        if ($cart_info['pgoods_choosenum'] != $quantity){
            $cart_state = $model_pointcart->editPointCart(array('pcart_id'=>$pcart_id,'pmember_id'=>$this->member_info['member_id']),array('pgoods_choosenum'=>$quantity));
        }
        if ($cart_state) {
            //计算总积分
            $amount= $model_pointcart->getPointCartAmount($this->member_info['member_id']);
            output_data(array('done'=>'true','subtotal'=>$prod_info['pointsamount'],'amount'=>$amount,'quantity'=>$quantity));
        }else{
            output_error($msg);
        }
    }

    /**
     * 兑换订单流程第一步
     */
    public function pointcart_step1Op(){
        //获取符合条件的兑换礼品和总积分
        $data = Model('pointcart')->getCartGoodsList($this->member_info['member_id']);
        if (!$data['state']){
            output_error($data['msg']);
        }

        if (intval($_POST['address_id']) > 0) {
            $result['address_info'] = Model('address')->getDefaultAddressInfo(array('address_id'=>intval($_POST['address_id']),'member_id'=>$this->member_info['member_id']));
        }else{
            $result['address_info'] = Model('address')->getDefaultAddressInfo(array('member_id'=>$this->member_info['member_id']));
        }

        $prod_list['pointprod_arr'] = $data['data'];
        $prod_list['address_info'] = $result['address_info'];

        output_data($prod_list);
    }
    public function pointcart_step2Op(){
        $model_pointcart = Model('pointcart');
        //获取符合条件的兑换礼品和总积分
        $data = $model_pointcart->getCartGoodsList($this->member_info['member_id']);
        if (!$data['state']){
            output_error($data['msg']);
        }
        $pointprod_arr = $data['data'];
        unset($data);

        //验证积分数是否足够
        $data = $model_pointcart->checkPointEnough($pointprod_arr['pgoods_pointall'], $this->member_info['member_id']);
        if (!$data['state']){
            output_error($data['msg']);
        }
        unset($data);

        //创建兑换订单
        $data = Model('pointorder')->createOrder($_POST, $pointprod_arr, array('member_id'=>$this->member_info['member_id'],'member_name'=>$this->member_info['member_name'],'member_email'=>$this->member_info['member_email']));
        if (!$data['state']){
            output_error($data['msg']);
        }

        $where = array();
        $where['point_orderid'] = $data['data']['order_id'];
        $where['point_buyerid'] = $this->member_info['member_id'];
        $order_info = Model('pointorder')->getPointOrderInfo($where);
        if (!$order_info){
            output_error('记录信息错误');
        }
        output_data($order_info);
    }

    /**
     * 兑换信息列表
     */
    public function point_orderOp() {
        //兑换信息列表
        $where = array();
        $where['point_buyerid'] = $this->member_info['member_id'];

        $model_pointorder = Model('pointorder');
        $order_list = $model_pointorder->getPointOrderList($where, '*', $this->page, 0, 'point_orderid desc');
        $order_idarr = array();
        $order_listnew = array();
        if (is_array($order_list) && count($order_list)>0){
            foreach ($order_list as $k => $v){
                $order_listnew[$v['point_orderid']] = $v;
                $order_listnew[$v['point_orderid']]['add_time'] = date('Y-m-d H:i:s', $v['point_addtime']);
                $order_idarr[] = $v['point_orderid'];
            }
        }

        //查询兑换商品
        if (is_array($order_idarr) && count($order_idarr)>0){
            $prod_list = $model_pointorder->getPointOrderGoodsList(array('point_orderid'=>array('in',$order_idarr)));
            if (is_array($prod_list) && count($prod_list)>0){
                foreach ($prod_list as $v){
                    if (isset($order_listnew[$v['point_orderid']])){
                        $order_listnew[$v['point_orderid']]['prodlist'][] = $v;
                    }
                }
            }
        }
        $page_count = $model_pointorder->gettotalpage();
        output_data(array('order_list' => array_values($order_listnew)), mobile_page($page_count));
    }
}
