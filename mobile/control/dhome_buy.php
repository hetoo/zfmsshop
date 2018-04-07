<?php
/**
 * 门店商品购买流程
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class dhome_buyControl extends mobileHomeControl {
    private $_chain_info = array();
    private $_token_info = array();
    private $_chain_id = 0;
    private $_member_id = 0;
    public function __construct() {
        parent::__construct();
        $this->_chain_id = intval($_POST['chain_id']);
        $this->_member_id = $_POST['cart_member_id'];
        $key = $_POST['key'];
        if(!empty($key)) {//已登录
            $model_mb_user_token = Model('mb_user_token');
            $this->_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);
            $this->_member_id = $this->_token_info['member_id'];
        }
        if($this->_chain_id) $this->_chain_info = Model('chain_cart')->getChainInfoByID($this->_chain_id);
    }
    /**
     * 购物车
     */
    public function indexOp(){
        $model_chain_cart = Model('chain_cart');
        $cart_array = $model_chain_cart->getChainCartByMember($this->_member_id,$this->_chain_info);
        if(intval($this->_member_id) == 0) $model_chain_cart->delChainCart();//删除15天前的未登录状态的数据
        output_data($cart_array);
    }

    /**
     * 加入购物车
     */
    public function add_cartOp(){
        $goods_id = intval($_POST['goods_id']);
        $chain_id = $this->_chain_id;
        $num = intval($_POST['num']);
        $key = $_POST['key'];
        $cart_member_id = '';
        $model_chain_cart = Model('chain_cart');
        $model_goods = Model('goods');
        $goods_info = $model_goods->getGoodsOnlineInfoByID($goods_id);
        $chain_info = $this->_chain_info;
        $stock_info = $model_chain_cart->getStockInfo(array('goods_id' => $goods_id, 'chain_id' => $chain_id));
        if(empty($goods_info) || empty($chain_info) || intval($stock_info['stock']) < $num) {
            output_error('库存不足');
        }
        if($this->_token_info['member_id']) {//已登录
            $model_member = Model('member');
            $member_info = $model_member->getMemberInfoByID($this->_token_info['member_id']);
            if(!$member_info['is_buy']) output_error('您没有商品购买的权限,如有疑问请联系客服人员');
            $cart_member_id = $member_info['member_id'];
        } else {
            $cart_member_id = $_POST['cart_member_id'];
            if(empty($cart_member_id)) $cart_member_id = 'u'.mt_rand(100,999).substr(100+$chain_id,-3).date('dHis');
        }
        $_info = $model_chain_cart->getChainCartInfo(array('member_id' => $cart_member_id, 'goods_id' => $goods_id, 'chain_id' => $chain_id));
        if(!empty($_info)) {
            if($num) {
                $model_chain_cart->editChainCart(array('goods_num' => $num, 'goods_selected' => 1, 'add_time' => TIMESTAMP, 'goods_price' => $stock_info['chain_price']),array('cart_id' => $_info['cart_id']));
            } else {
                $model_chain_cart->delChainCart(array('cart_id' => $_info['cart_id']));
            }
        } else {
            $_array = array();
            $_array['member_id'] = $cart_member_id;
            $_array['chain_id'] = $chain_id;
            $_array['chain_name'] = $chain_info['chain_name'];
            $_array['add_time'] = TIMESTAMP;
            $_array['store_id'] = $goods_info['store_id'];
            $_array['store_name'] = $goods_info['store_name'];
            $_array['goods_id'] = $goods_id;
            $_array['goods_name'] = $goods_info['goods_name'];
            $_array['goods_price'] = $stock_info['chain_price'];
            $_array['goods_num']   = $num;
            $_array['goods_image'] = $goods_info['goods_image'];
            $model_chain_cart->addChainCart($_array);
        }
        $cart_array = $model_chain_cart->getChainCartByMember($cart_member_id,$this->_chain_info);
        $cart_array['cart_member_id'] = $cart_member_id;
        output_data($cart_array);
    }

    /**
     * 更新购物车
     */
    public function update_cartOp(){
        $chain_id = $this->_chain_id;
        $cart_member_id = $this->_member_id;
        $goods_id = intval($_POST['goods_id']);
        $model_chain_cart = Model('chain_cart');
        
        $condition = array();
        $condition['member_id'] = $cart_member_id;
        $condition['chain_id'] = $chain_id;
        if($goods_id) $condition['goods_id'] = $goods_id;
        $model_chain_cart->editChainCart(array('goods_selected' => intval($_POST['cart_check'])),$condition);
        $cart_array = $model_chain_cart->getChainCartByMember($cart_member_id,$this->_chain_info);
        output_data($cart_array);
    }

    /**
     * 删除购物车
     */
    public function del_cartOp(){
        $chain_id = $this->_chain_id;
        $cart_member_id = $this->_member_id;
        $goods_id = intval($_POST['goods_id']);
        $model_chain_cart = Model('chain_cart');
        
        $condition = array();
        $condition['member_id'] = $cart_member_id;
        $condition['chain_id'] = $chain_id;
        if($goods_id) $condition['goods_id'] = $goods_id;
        $model_chain_cart->delChainCart($condition);
        $cart_array = $model_chain_cart->getChainCartByMember($cart_member_id,$this->_chain_info);
        output_data($cart_array);
    }

    /**
     * 同步购物车
     */
    public function merge_cartOp(){
        $model_chain_cart = Model('chain_cart');
        $cart_member_id = $_POST['cart_member_id'];
        if(intval($cart_member_id) == 0 && $this->_member_id) {
            $condition = array();
            $condition['member_id'] = $this->_member_id;
            $cart_list = $model_chain_cart->getChainCartByKey($condition);//已登录时添加的商品
            $id_list = array_keys($cart_list);
            $condition = array();
            $condition['member_id'] = $cart_member_id;
            $condition['goods_id'] = array('in', $id_list);
            $model_chain_cart->delChainCart($condition);//删除同时存在的商品
            $condition = array();
            $condition['member_id'] = $cart_member_id;
            $model_chain_cart->editChainCart(array('member_id' => $this->_member_id),$condition);
        }
        output_data($this->_member_id);
    }

    /**
     * 地址列表
     */
    public function address_listOp() {
        $chain_id = $this->_chain_id;
        $member_id = $this->_token_info['member_id'];
        $model_address = Model('address');
        $model_chain_cart = Model('chain_cart');
        $condition = array();
        $condition['member_id'] = $member_id;
        $condition['dlyp_id'] = 0;//不能用门店自提的收货地址
        $address_list = $model_address->getAddressList($condition);
        if (!empty($address_list) && is_array($address_list)) {
            if($chain_id) $chain_info = $this->_chain_info;
            $_valid_list = array();
            $_invalid_list = array();
            foreach ($address_list as $k => $v) {
                if ($v['area_lat'] == 0) {
                    $location = getGeoByAddress($v['area_info'].$v['address']);
                    if (!empty($location['location']) && is_array($location['location'])) {
                        $v['area_lat'] = $location['location']['lat'];
                        $v['area_lng'] = $location['location']['lng'];
                        $model_address->editAddress(array('area_lat'=> $v['area_lat'],'area_lng'=> $v['area_lng']), array('address_id'=> $v['address_id']));
                        $address_list[$k] = $v;
                    }
                }
                if (!empty($chain_info)) {
                    $v['chain_valid'] = $this->valid_address($v);
                    if ($v['chain_valid'] == 1) {
                        $_valid_list[] = $v;
                    } else {
                        $_invalid_list[] = $v;
                    }
                }
            }
            if($chain_id) $address_list = array_merge($_valid_list, $_invalid_list);
        }
        output_data(array('address_list' => $address_list));
    }

    /**
     * 收货地址是否在门店配送范围内
     */
    public function buy_addressOp(){
        $chain_valid = 0;
        $location = getGeoByAddress($_POST['area_info'].$_POST['address']);
        if (!empty($location['location']) && is_array($location['location'])) {
            $v = array();
            $v['area_lat'] = $location['location']['lat'];
            $v['area_lng'] = $location['location']['lng'];
            $chain_valid = $this->valid_address($v);
        }
        output_data($chain_valid);
    }

    /**
     * 购买第一步
     */
    public function buy_step1Op(){
        $cart_array = $this->cart_check();
        $chain_id = $this->_chain_id;
        $member_id = $this->_token_info['member_id'];
        if($cart_array['cart_num'] < $cart_array['selected_num']) {
            output_error('部分库商品存不足，返回修改');
        } else {
            $model_voucher = Model('chain_voucher');
            $condition = array();
            $condition['voucher_chain_id'] = $chain_id;
            $condition['voucher_owner_id'] = $member_id;
            $voucher_list = $model_voucher->getCurrentAvailableVoucher($condition,$cart_array['buy_goods_amount'],'voucher_price desc');
            if (!empty($voucher_list) && is_array($voucher_list)) {
                reset($voucher_list);
                $voucher_info = current($voucher_list);
                $voucher_info['voucher_price'] = ncPriceFormat($voucher_info['voucher_price']);
                $cart_array['voucher_info'] = $voucher_info;
                $cart_array['chain_buy_amount'] = ncPriceFormat($cart_array['chain_buy_amount']-$voucher_info['voucher_price']);
            }
            $rpt_list = Logic('buy_1')->getStoreAvailableRptList($member_id,$cart_array['chain_buy_amount'],'rpacket_price desc');
            if (!empty($rpt_list) && is_array($rpt_list)) {
                reset($rpt_list);
                $cart_array['rpt_info'] = current($rpt_list);
            }
            $member_info = Model('member')->getMemberInfoByID($member_id);
            if (floatval($member_info['available_predeposit']) > 0) $cart_array['available_predeposit'] = $member_info['available_predeposit'];
            if (floatval($member_info['available_rc_balance']) > 0) $cart_array['available_rc_balance'] = $member_info['available_rc_balance'];
        }
        
        output_data($cart_array);
    }

    /**
     * 购买第二步
     */
    public function buy_step2Op(){
        $cart_array = $this->cart_check();
        $chain_id = $this->_chain_id;
        $member_id = $this->_token_info['member_id'];
        if($cart_array['cart_num'] < $cart_array['selected_num']) {
            output_error('部分商品库存不足，返回修改');
        } else {
            $address_id = intval($_POST['address_id']);
            $voucher_t_id = intval($_POST['voucher_t_id']);
            $rpacket_t_id = intval($_POST['rpacket_t_id']);
            $model_order = Model('order');
            $model_chain_cart = Model('chain_cart');
            try {
                $model_order->beginTransaction();
                $order = $model_order->table('orders')->where(array('buyer_id'=> $member_id,'add_time'=> array('egt',TIMESTAMP-3)))->find();//防止误操作,单个会员3秒内只能提交一个订单
                if (is_array($order) && !empty($order)) {
                    throw new Exception('请勿多次提交订单');
                }
                if ($cart_array['buy_goods_amount'] < $cart_array['start_amount_price']) {
                    throw new Exception('订单商品总金额不到门店的起送金额');
                }
                $member_info = Model('member')->table('member')->where(array('member_id'=> $member_id))->master(true)->lock(true)->find();//锁定当前会员记录
                $voucher_info = array();
                if ($voucher_t_id) {
                    $condition = array();
                    $condition['voucher_chain_id'] = $chain_id;
                    $condition['voucher_owner_id'] = $member_id;
                    $condition['voucher_t_id'] = $voucher_t_id;
                    $condition['voucher_state'] = 1;
                    $condition['voucher_limit'] = array('lt',$cart_array['buy_goods_amount']);
                    $condition['voucher_end_date'] = array('gt',TIMESTAMP);
                    $voucher_info = Model('chain_voucher')->table('chain_voucher')->where($condition)->master(true)->lock(true)->find();//锁定当前代金券记录
                    if ($voucher_info['voucher_state'] != 1) {
                        throw new Exception('请选择正确的代金券');
                    }
                    $cart_array['chain_buy_amount'] = ncPriceFormat($cart_array['chain_buy_amount']-$voucher_info['voucher_price']);
                }
                $rpt_info = array();
                if ($rpacket_t_id) {
                    $condition = array();
                    $condition['rpacket_t_id'] = $rpacket_t_id;
                    $condition['rpacket_owner_id'] = $member_id;
                    $condition['rpacket_state'] = 1;
                    $condition['rpacket_limit'] = array('lt',$cart_array['chain_buy_amount']);
                    $condition['rpacket_end_date'] = array('gt',TIMESTAMP);
                    $rpt_info = Model('redpacket')->table('redpacket')->where($condition)->master(true)->lock(true)->find();//锁定当前红包记录
                    if ($rpt_info['rpacket_state'] != 1) {
                        throw new Exception('请选择正确的红包');
                    }
                    $cart_array['chain_buy_amount'] = ncPriceFormat($cart_array['chain_buy_amount']-$rpt_info['rpacket_price']);
                }
                $address_info = Model('address')->getAddressInfo(array('address_id'=> $address_id,'member_id'=> $member_id,'dlyp_id'=> 0));
                if (empty($address_info)) {
                    throw new Exception('请选择收货地址');
                }
                $chain_valid = $this->valid_address($address_info);
                if (empty($chain_valid)) {
                    throw new Exception('当前地址不在门店配送范围内');
                }
                $logic_buy_1 = Logic('buy_1');
                $pay_sn = $logic_buy_1->makePaySn($member_id);
                $order_pay = array();
                $order_pay['pay_sn'] = $pay_sn;
                $order_pay['buyer_id'] = $member_id;
                $order_pay_id = $model_order->addOrderPay($order_pay);
                if (!$order_pay_id) {
                    throw new Exception('订单保存失败[未生成支付单]');
                }
                list($reciver_info,$reciver_name,$reciver_phone) = $logic_buy_1->getReciverAddr($address_info);
                $order = array();
                $order_common = array();
                $order_list = array();
                $goods_list = $cart_array['goods_list'];
                $order['order_sn'] = $logic_buy_1->makeOrderSn($order_pay_id);
                $order['pay_sn'] = $pay_sn;
                $order['store_id'] = $goods_list[0]['store_id'];
                $order['store_name'] = $goods_list[0]['store_name'];
                $order['buyer_id'] = $member_id;
                $order['buyer_name'] = $member_info['member_name'];
                $order['buyer_email'] = $member_info['member_email'];
                $order['buyer_phone'] = is_numeric($reciver_phone) ? $reciver_phone : 0;
                $order['add_time'] = TIMESTAMP;
                $order['payment_code'] = 'online';
                $order['order_state'] = ORDER_STATE_NEW;
                $order['order_amount'] = $cart_array['chain_buy_amount'];
                $order['shipping_fee'] = $cart_array['transport_freight'];
                $order['goods_amount'] = $cart_array['buy_goods_amount'];
                $order['order_from'] = 2;
                $order['order_type'] = 5;
                $order['chain_id'] = $chain_id;
                $order['rpt_amount'] = $rpt_info['rpacket_price'];
                $order_id = $model_order->addOrder($order);
                if (!$order_id) {
                    throw new Exception('订单保存失败[未生成订单数据]');
                }
                $order['order_id'] = $order_id;
                $order_list[$order_id] = $order;
                $order_common['order_id'] = $order_id;
                $order_common['store_id'] = $order['store_id'];
                $order_common['order_message'] = $_POST['pay_message'];
                //代金券
                if (!empty($voucher_info)){
                    $order_common['voucher_price'] = $voucher_info['voucher_price'];
                    $order_common['voucher_code'] = $voucher_info['voucher_code'];
                }
                $promotion_total = $voucher_info['voucher_price'];
                if ($rpt_info['rpacket_price'] > $cart_array['transport_freight']) $promotion_total += $rpt_info['rpacket_price']-$cart_array['transport_freight'];
                $order_common['promotion_total'] = ncPriceFormat($promotion_total);
                $order_common['reciver_info']= $reciver_info;
                $order_common['reciver_name'] = $reciver_name;
                $order_common['reciver_city_id'] = intval($address_info['city_id']);
                $order_common['promotion_info'] = array();
                //平台红包值
                if (!empty($rpt_info)) {
                    $order_common['promotion_info'][] = array('平台红包',sprintf('使用%s元红包 编码：%s',$rpt_info['rpacket_price'],$rpt_info['rpacket_code']));
                }
                //代金券
                if (!empty($voucher_info)){
                    $_voucher_type = '门店代金券';
                    $order_common['promotion_info'][] = array($_voucher_type,sprintf('使用%s元代金券 编码：%s',$voucher_info['voucher_price'],$voucher_info['voucher_code']));
                }
                $order_common['promotion_info'] = $order_common['promotion_info'] ? serialize($order_common['promotion_info']) : '';
                $insert = $model_order->addOrderCommon($order_common);
                if (!$insert) {
                    throw new Exception('订单保存失败[未生成订单扩展数据]');
                }
                //添加订单日志
                $log_data = array();
                $log_data['order_id'] = $order_id;
                $log_data['log_role'] = '买家';
                $log_data['log_msg'] = '生成订单';
                $log_data['log_user'] = $member_info['member_name'];
                $log_data['log_orderstate'] = ORDER_STATE_NEW;
                $model_order->addOrderLog($log_data);
                $promotion_rate = abs(number_format($promotion_total/$cart_array['buy_goods_amount'],5));
                if ($promotion_rate <= 1) {
                    $promotion_rate = floatval(substr($promotion_rate,0,5));
                } else {
                    $promotion_rate = 0;
                }
                $promotion_sum = 0;
                $i = 0;
                $order_goods = array();
                $goods_buy_quantity = array();
                foreach ($goods_list as $goods_info) {
                    $goods_commonid = $goods_info['goods_commonid'];
                    $order_goods[$i]['order_id'] = $order_id;
                    $order_goods[$i]['goods_id'] = $goods_info['goods_id'];
                    $order_goods[$i]['store_id'] = $goods_info['store_id'];
                    $order_goods[$i]['goods_name'] = $goods_info['goods_name'];
                    $order_goods[$i]['goods_price'] = $goods_info['goods_price'];
                    $order_goods[$i]['goods_num'] = $goods_info['goods_num'];
                    $order_goods[$i]['goods_image'] = $goods_info['goods_image'];
                    $order_goods[$i]['buyer_id'] = $member_id;
                    $order_goods[$i]['goods_commonid'] = $goods_commonid;
                    $order_goods[$i]['add_time'] = TIMESTAMP;
                    $order_goods[$i]['goods_type'] = 15;
                    $order_goods[$i]['commis_rate'] = 200;
                    //计算商品金额
                    $goods_total = $goods_info['goods_price'] * $goods_info['goods_num'];
                    $promotion_value = floor($goods_total*($promotion_rate)*100)/100;//优惠金额
                    $order_goods[$i]['goods_pay_price'] = $goods_total - $promotion_value < 0 ? 0 : $goods_total - $promotion_value;
                    $promotion_sum += $promotion_value;
                    $i++;
                    $goods_buy_quantity[$goods_info['goods_id']] = $goods_info['goods_num'];
                }
                if ($promotion_total > $promotion_sum) {
                    $i--;
                    for($i;$i>=0;$i--) {
                        if (floatval($order_goods[$i]['goods_pay_price']) > 0) {
                            $order_goods[$i]['goods_pay_price'] -= $promotion_total - $promotion_sum;
                            break;
                        }
                    }
                }
                $insert = $model_order->addOrderGoods($order_goods);
                if (!$insert) {
                    throw new Exception('订单保存失败[未生成商品数据]');
                }

                $result = Logic('queue')->createOrderUpdateChainStorage($goods_buy_quantity,$chain_id);
                if (!$result['state']) {
                    throw new Exception('订单保存失败[变更门店库存销量失败]');
                }
                //更新代金券状态
                if (!empty($voucher_info)) {
                    $result = Logic('realtime_msg')->editVoucherState(array($voucher_info),$chain_id);
                    if (!$result['state']) {
                        throw new Exception('订单保存失败[代金券处理失败]');
                    }
                }
        
                //更新平台红包状态
                if (!empty($rpt_info)) {
                    $result = Logic('realtime_msg')->editRptState($rpt_info,$pay_sn);
                    if (!$result['state']) {
                        throw new Exception('订单保存失败[平台红包处理失败]');
                    }
                }
                QueueClient::push('createSphot', array($order_id));
                $condition = array();
                $condition['member_id'] = $member_id;
                $condition['chain_id'] = $chain_id;
                $condition['goods_id'] = array('in', $cart_array['id_list']);
                $model_chain_cart->delChainCart($condition);
                if ($member_info['member_paypwd'] == md5($_POST['password'])) {
                    //使用充值卡支付
                    if (!empty($_POST['rcb_pay'])) {
                        $order_list = $logic_buy_1->rcbPay($order_list, $_POST, $member_info);
                    }
                    //使用预存款支付
                    if (!empty($_POST['pd_pay'])) {
                        $logic_buy_1->pdPay($order_list, $_POST, $member_info);
                    }
                }
                $model_order->commit();
                output_data(array('pay_sn' => $pay_sn,'payment_code'=> $order['payment_code']));
            }catch (Exception $e){
                $model_order->rollback();
                output_error($e->getMessage());
            }
        }
    }

    /**
     * 购买验证
     */
    private function cart_check(){
        $cart_id = explode(',', $_POST['cart_id']);
        $chain_id = $this->_chain_id;
        $member_id = $this->_token_info['member_id'];
        $model_chain_cart = Model('chain_cart');
        $goods_list = array();
        $buy_list = array();
        $buy_goods_amount = 0;
        $buy_count = 0;
        if (!empty($cart_id) && is_array($cart_id)) {
            foreach ($cart_id as $k => $v) {
                $_a = explode('|', $v);
                $_id = intval($_a[0]);
                $_n = intval($_a[1]);
                if ($_id > 0 && $_n > 0) $goods_list[$_id] = $_n;
            }
            $id_list = array_keys($goods_list);
            if (!empty($id_list) && is_array($id_list)) {
                $condition = array();
                $condition['member_id'] = $member_id;
                $condition['chain_id'] = $chain_id;
                $condition['goods_id'] = array('in', $id_list);
                $cart_list = $model_chain_cart->getChainCartByKey($condition);
                $condition = array();
                $condition['chain_id'] = $chain_id;
                $condition['goods_id'] = array('in', $id_list);
                $stock_list = $model_chain_cart->getStockByKey($condition);
                foreach ($goods_list as $k => $v) {
                    $_info = $cart_list[$k];
                    if (!empty($_info) && ($v <= $stock_list[$k]['stock'])) {
                        $_info['goods_num'] = $v;
                        $_info['goods_price'] = $stock_list[$k]['chain_price'];
                        $_info['goods_commonid'] = $stock_list[$k]['goods_commonid'];
                        $_info['goods_image_url'] = thumb($_info, 60);
                        $_info['image_240_url'] = thumb($_info, 240);
                        $_info['image_360_url'] = thumb($_info, 360);
                        $_info['goods_total'] = ncPriceFormat($_info['goods_num']*$_info['goods_price']);
                        $buy_goods_amount = ncPriceFormat($buy_goods_amount+$_info['goods_total']);
                        $buy_count += $_info['goods_num'];
                        $buy_list[] = $_info;
                    }
                }
            }
        }
        $chain_info = $this->_chain_info;
        $cart_info = array();
        $cart_info['goods_list'] = $buy_list;
        $cart_info['buy_goods_amount'] = $buy_goods_amount;
        $cart_info['buy_count'] = $buy_count;
        $cart_info['selected_num'] = count($goods_list);
        $cart_info['cart_num'] = count($buy_list);
        $cart_info['id_list'] = $id_list;
        $cart_info['chain_name'] = $chain_info['chain_name'];
        $cart_info['start_amount_price'] = $chain_info['start_amount_price'];
        $cart_info['transport_freight'] = $chain_info['transport_freight'];
        $cart_info['chain_buy_amount'] = ncPriceFormat($buy_goods_amount+$chain_info['transport_freight']);
        $cart_info['transport_rule'] = $chain_info['transport_rule'];
        $cart_info['transport_distance'] = $chain_info['transport_distance'];
        $cart_info['transport_areas'] = $chain_info['transport_areas'];
        $cart_info['city_id'] = $chain_info['area_id_2'];
        $cart_info['area_id'] = $chain_info['area_id'];
        $cart_info['area_info'] = $chain_info['area_info'];
        return $cart_info;
    }

    /**
     * 地址验证
     */
    private function valid_address($area) {
        $chain_valid = 0;
        $chain_info = $this->_chain_info;
        if (!empty($chain_info)) {
            if ($chain_info['transport_rule'] == 1) {
                $_distance = $chain_info['transport_distance']*1000;
                $_n = $this->getDistance($chain_info['chain_lat'],$chain_info['chain_lng'],$area['area_lat'],$area['area_lng']);
                if ($_distance >= $_n) $chain_valid = 1;
            } else {
                if (strpos($chain_info['transport_areas'],",".$area['area_id'].",") !== false) $chain_valid = 1;
            }
        }
        return $chain_valid;
    }

    /**
     *  根据两点间的经纬度计算距离
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6367000; //approximate radius of earth in meters
    
        /*
         Convert these degrees to radians
        to work with the formula
        */
    
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
    
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
    
        /*
         Using the
        Haversine formula
    
        http://en.wikipedia.org/wiki/Haversine_formula
    
        calculate the distance
        */
    
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
    
        return round($calculatedDistance);
    }
}
