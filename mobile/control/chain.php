<?php
/**
 * 门店接口
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class chainControl extends mobileHomeControl{
    public function __construct() {
        parent::__construct();
    }

    //门店首页
    public function indexOp(){
        $chain_id = $_REQUEST['chain_id'];
        if(intval($chain_id) <= 0){
            output_error('参数错误');
        }
        //处理门店信息
        $model_chain = Model('chain');
        $chain_info = $model_chain->getChainInfo(array('chain_id'=>$chain_id, 'chain_state' => 1, 'is_self_take' => 1));
        $chain_info['chain_img'] = getChainImage($chain_info['chain_img'],$chain_info['store_id']);
        $chain_info['chain_banner'] = getChainImage($chain_info['chain_banner'],$chain_info['store_id']);
        unset($chain_info['chain_pwd']);
        unset($chain_info['area_id_1']);
        unset($chain_info['area_id_2']);
        unset($chain_info['area_id_3']);
        unset($chain_info['area_id_4']);

        //处理门店代金券
        $model_chain_voucher = Model('chain_voucher');
        $condition = array();
        $condition['voucher_t_chain_id'] = $chain_id;
        $condition['voucher_t_state'] = 1;
        $condition['voucher_t_end_date'] = array('gt',TIMESTAMP);
        $field = "voucher_t_id,voucher_t_title,voucher_t_end_date,voucher_t_price,voucher_t_limit,voucher_t_eachlimit";
        $voucher_list = $model_chain_voucher->getVoucherTemplateList($condition, $field, $limit = 3, null, $order = 'voucher_t_recommend desc,voucher_t_id asc');
        foreach($voucher_list as $key => $value){
            $voucher_list[$key]['voucher_t_end_date'] = date('Y-m-d',$value['voucher_t_end_date']);
        }
        output_data(array('chain_info'=>$chain_info,'voucher_list'=>$voucher_list));
    }

    //获取门店商品
    public function goods_listOp(){
        $chain_id = $_REQUEST['chain_id'];
        if(intval($chain_id) <= 0){
            output_error('参数错误');
        }
        //处理门店商品
        $model_chain_stock = Model('chain_stock');
        $condition = array();
        $condition['goods.goods_state'] = 1;
        $condition['goods.goods_verify'] = 1;
        $condition['chain_stock.chain_id'] = $chain_id;
        $field = "chain_stock.chain_id,chain_stock.stock,chain_stock.chain_price,goods.goods_id,goods.goods_commonid,goods.goods_name,goods.goods_jingle,goods.goods_salenum,goods.goods_image,goods.store_id";
        $goods_list = $model_chain_stock->getChainGoodsList($condition, $field, $this->page);
        $page_count = $model_chain_stock->gettotalpage();

        foreach($goods_list as $key => $value){
            $goods_list[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }

        output_data(array('goods_list' => $goods_list), mobile_page($page_count));
    }

    //领取代金券
    public function get_voucherOp(){
        $tid = intval($_POST['tid']);
        if($tid <= 0){
            output_error('参数错误');
        }
        $member_id = $this->getMemberIdIfExists();
        $model_voucher = Model('chain_voucher');
        $res = $model_voucher->receiveVoucher($tid,$member_id);

        if($res['state']){
            output_data('1');
        }else{
            output_error($res['msg']);
        }
    }

    //获取门店地址
    public function chain_addressOp(){
        $chain_id = $_REQUEST['chain_id'];
        if(intval($chain_id) <= 0){
            output_error('参数错误');
        }
        //处理门店信息
        $model_chain = Model('chain');
        $_info = $model_chain->getChainInfo(array('chain_id'=>$chain_id, 'chain_state' => 1, 'is_self_take' => 1),'chain_id,store_id,chain_name,chain_img,area_info,chain_address,chain_phone');
        $data['address'] = $_info['area_info'].$_info['chain_address'];
        $data['chain_name'] = $_info['chain_name'];
        $data['chain_id'] = $_info['chain_id'];
        $data['chain_img'] = getChainImage($_info['chain_img'],$_info['store_id']);
        $data['chain_phone'] = $_info['chain_phone'];
        output_data(array('chain_info'=>$data));
    }
}