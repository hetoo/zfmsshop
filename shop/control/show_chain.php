<?php
/**
 * 会员店铺
 *
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

class show_chainControl extends BaseChainControl {
    public function __construct(){
        parent::__construct();
    }
    /**
     * 展示门店
     */
    public function indexOp() {
        $chain_id = intval($_GET['chain_id']);

        //处理门店信息
        $chain_info = Model('chain')->getChainInfo(array('chain_id' => $chain_id, 'chain_state' => 1, 'is_self_take' => 1));
        Tpl::output('chain_info', $chain_info);
        unset($chain_info['chain_pwd']);

        //处理门店代金券
        $model_chain_voucher = Model('chain_voucher');
        $condition = array();
        $condition['voucher_t_chain_id'] = $chain_id;
        $condition['voucher_t_state'] = 1;
        $condition['voucher_t_end_date'] = array('gt',TIMESTAMP);
        $field = "voucher_t_id,voucher_t_title,voucher_t_end_date,voucher_t_price,voucher_t_limit,voucher_t_eachlimit";
        $voucher_list = $model_chain_voucher->getVoucherTemplateList($condition, $field, $limit = 3, null, $order = 'voucher_t_recommend desc,voucher_t_id asc');
        Tpl::output('voucher_list',$voucher_list);

        //处理门店商品
        $model_chain_stock = Model('chain_stock');
        $condition = array();
        $condition['goods.goods_state'] = 1;
        $condition['goods.goods_verify'] = 1;
        $condition['chain_stock.chain_id'] = $chain_id;
        $field = "chain_stock.chain_id,chain_stock.stock,chain_stock.chain_price,goods.goods_id,goods.goods_commonid,goods.goods_name,goods.goods_jingle,goods.goods_salenum,goods.goods_image,goods.store_id";
        $goods_list = $model_chain_stock->getChainGoodsList($condition, $field, 20);

        Tpl::output('goods_list',$goods_list);
        Tpl::output('show_page',$model_chain_stock->showpage());

        Tpl::showpage('show_chain');
    }

    //领取代金券
    public function get_voucherOp(){
        $this->checkLogin();
        $tid = intval($_POST['tid']);
        if($tid <= 0){
            output_error('参数错误');
        }
        $member_info = $this->getMemberAndGradeInfo(true);
        $model_voucher = Model('chain_voucher');
        $res = $model_voucher->receiveVoucher($tid,$member_info['member_id']);

        echo json_encode($res);exit;
    }
}
