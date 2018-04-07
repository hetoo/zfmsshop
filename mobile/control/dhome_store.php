<?php
/**
 * 门店信息接口
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class dhome_storeControl extends mobileHomeControl{

    public function indexOp(){
        $this->chain_infoOp();
    }

    /**
     * 获取门店信息
     */
    public function chain_infoOp(){
        $chain_id= intval($_GET['chain_id']);
        if($chain_id <= 0){
            output_error('参数错误');
        }

        $lng = floatval($_GET['curr_lng']);
        $lat = floatval($_GET['curr_lat']);

        $model_chain = Model('chain');
        $field = "*,acos(cos((chain_lng-$lng)*0.01745329252)*cos((chain_lat-$lat)*0.01745329252))*6371004 as gps";
        $chain_info = $model_chain->getChainInfo(array('chain_id'=>$chain_id,'chain_state'=>1),$field);
        if(empty($chain_info)){
            output_error('门店不存在或已关闭');
        }
        unset($chain_info['chain_pwd']);
        $chain_info['chain_img'] = getChainImage($chain_info['chain_img'],$chain_info['store_id']);
        $chain_info['chain_banner'] = getChainImage($chain_info['chain_banner'],$chain_info['store_id']);
        $chain_info['chain_logo'] = getChainImage($chain_info['chain_logo'],$chain_info['store_id']);
        //门店距离处理
        if(ceil($chain_info['gps']) >= 1000){
            $gps = number_format($chain_info['gps']/1000.0,2);
            $chain_info['gps'] = $gps;
            $chain_info['chain_address'] .= "（距您 {$gps} km）";
        }else{
            $gps = ceil($chain_info['gps']);
            $chain_info['gps'] = $gps;
            $chain_info['chain_address'] .= "（距您 {$gps} m）";
        }
        //获取门店商品数量
        $model_chain_goods = Model('chain_stock');
        $goods_count = $model_chain_goods->getChainGoodsCount(array('chain_id'=>$chain_id));
        $chain_info['goods_amount'] = $goods_count;

        //获取门店订单数量
        $order_count = Model('order')->getOrderCount(array('chain_id'=>$chain_id,'order_type'=>5));
        $chain_info['order_amount'] = $order_count;

        //获取门店商品分类
        $model_chain_class = Model('chain_goods_class');
        //$class_list = $model_chain_class->getChainGoodsClassPlainList($chain_id);
        $class_t_list = $model_chain_class->getShowTreeList($chain_id);
        $class_list = array();
        foreach ($class_t_list as $val){
            $class_list[] = $val;
        }

        //获取门店优惠券信息
        $model_chain_voucher = Model('chain_voucher');
        $current_time = TIMESTAMP;
        $condition = array();
        $condition['voucher_t_chain_id'] = $chain_id;
        $condition['voucher_t_state'] = 1;
        $condition['voucher_date'] = array('exp',"voucher_t_start_date<$current_time and voucher_t_end_date>$current_time");
        $voucher_field = "voucher_t_id,voucher_t_title,voucher_t_price,voucher_t_limit,voucher_t_eachlimit,voucher_t_recommend,voucher_t_start_date,voucher_t_end_date";
        $voucher_list = $model_chain_voucher->getVoucherTemplateList($condition,$voucher_field);
        foreach ((array)$voucher_list as $k=>$v){
            $voucher_list[$k]['voucher_t_start_date'] = date('Y-m-d',$v['voucher_t_start_date']);
            $voucher_list[$k]['voucher_t_end_date'] = date('Y-m-d',$v['voucher_t_end_date']);
        }

        output_data(array('chain_info'=>$chain_info,'class_list'=>$class_list,'voucher_list'=>$voucher_list));
    }

    /**
     * 获取门店商品列表
     */
    public function goods_listOp(){
        $chain_id= intval($_GET['chain_id']);
        if($chain_id <= 0){
            output_error('参数错误');
        }
        $condition = array();
        $condition['goods.goods_state'] = 1;
        $condition['goods.goods_verify'] = 1;
        $condition['chain_stock.chain_id'] = $chain_id;
        $condition['chain_stock.stock'] = array('gt', 0);

        //处理商品分类
        $cate_id = intval($_GET['cate_id']);
        if($cate_id > 0){
            $condition['chain_stock.cate_id'] = array('exp',"chain_stock.cate_id=$cate_id or chain_stock.p_cate_id=$cate_id");
        }

        //处理店内关键词搜索
        $keyword = trim($_GET['keyword']);
        if(strlen($keyword) > 0){
            $condition['goods.goods_name|goods.goods_jingle'] = array('like',"%$keyword%");
        }

        $model_chain_goods = Model('chain_stock');
        $field = "chain_stock.chain_id,chain_stock.stock,chain_stock.chain_price,chain_stock.cate_id,chain_stock.goods_salenum,goods.goods_id,goods.goods_commonid,goods.goods_name,goods.goods_jingle,goods.goods_image,goods.store_id";
        $goods_list = $model_chain_goods->getChainGoodsList($condition, $field, $this->page);
        $page_count = $model_chain_goods->gettotalpage();
        foreach((array)$goods_list as $key => $value){
            $goods_list[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }

        $cate_info = array();
        $cate_info['name'] = "全部分类";
        $cate_info['amount'] = 0;
        $goods_count = $model_chain_goods->getChainGoodsCount($condition);
        $cate_info['amount'] = $goods_count;
        if($cate_id > 0){
            $class_info = Model('chain_goods_class')->getChainGoodsClassInfo(array('class_id'=>$cate_id,'chain_id'=>$chain_id),'class_name');
            if(!empty($class_info)){
                $cate_info['name'] = $class_info['class_name'];
            }
        }

        output_data(array('goods_list' => $goods_list,'cate_info'=>$cate_info), mobile_page($page_count));
    }

    /**
     * 门店商品详情
     */
    public function goods_detailOp(){
        $chain_id = intval($_GET['chain_id']);
        $goods_id = intval($_GET['goods_id']);
        if($chain_id <= 0){
            output_error('参数错误');
        }
        if($goods_id <= 0){
            output_error('参数错误');
        }

        //获取商品信息
        $condition = array();
        $condition['goods.goods_id'] = $goods_id;
        $condition['chain_stock.chain_id'] = $chain_id;
        $fiield = "chain_stock.chain_id,chain_stock.stock,chain_stock.chain_price,chain_stock.cate_id,goods.goods_id,goods.goods_name,goods.goods_jingle,goods.store_id,goods.goods_image,goods.color_id,goods.goods_commonid";
        $model_stock = Model('chain_stock');
        $goods_info = $model_stock->getChainGoodsInfo($condition, $fiield);
        $goods_info['goods_image'] = cthumb($goods_info['goods_image'],'360',$goods_info['store_id']);

        //获取商品多图
        $model_goods = Model('goods');
        $condition = array();
        $condition['color_id'] = $goods_info['color_id'];
        $condition['goods_commonid'] = $goods_info['goods_commonid'];
        $goods_images = $model_goods->getGoodsImageList($condition,'goods_image,is_default');
        foreach ((array)$goods_images as $key => $val){
            $tmp = $val;
            $tmp['goods_image'] = cthumb($val['goods_image'],'360',$goods_info['store_id']);
            $goods_info['image_list'][] = $tmp;
        }

        //获取门店信息
        $model_chain = Model('chain');
        $field = "chain_id,chain_name,chain_phone";
        $chain_info = $model_chain->getChainInfo(array('chain_id'=>$chain_id),$field);

        //获取商品评价信息
        $eval_info = $this->_getEvaluateList($chain_id, $goods_id);

        output_data(array('goods_info'=>$goods_info,'chain_info'=>$chain_info,'eval_info'=>$eval_info));
    }

    /**
     * 获取商品评价
     */
    public function goods_evaluateOp(){
        $chain_id = intval($_GET['chain_id']);
        $goods_id = intval($_GET['goods_id']);
        if($chain_id <= 0){
            output_error('参数错误');
        }
        if($goods_id <= 0){
            output_error('参数错误');
        }

        //获取商品评论
        $condition = array();
        $condition['orders.chain_id'] = $chain_id;
        $condition['orders.order_type'] = 5;
        $condition['order_goods.goods_id'] = $goods_id;
        $field = "evaluate_goods.geval_id,evaluate_goods.geval_scores,evaluate_goods.geval_content,evaluate_goods.geval_addtime,evaluate_goods.geval_frommembername,evaluate_goods.geval_frommemberid";
        $model_stock = Model('chain_stock');
        $evaluate_list = $model_stock->getGoodsEvaluateList($condition, $field , 10);
        $page_count = $model_stock->gettotalpage();
        foreach ($evaluate_list as $key=>$val){
            $evaluate_list[$key]['geval_addtime'] = date('Y-m-d',$val['geval_addtime']);
            $evaluate_list[$key]['geval_frommembername'] = substr($val['geval_frommembername'],0,2).'**'.substr($val['geval_frommembername'],-2);
        }

        output_data(array('evaluate_list'=>$evaluate_list),mobile_page($page_count));
    }

    //获取商品评价
    private function _getEvaluateList($chain_id, $goods_id){
        //获取商品评论
        $condition = array();
        $condition['orders.chain_id'] = $chain_id;
        $condition['orders.order_type'] = 5;
        $condition['order_goods.goods_id'] = $goods_id;
        $fiield = "evaluate_goods.geval_scores,count(*) as geval_count";
        $model_stock = Model('chain_stock');
        $count_array = $model_stock->table('orders,order_goods,evaluate_goods')->join('left')->on('orders.order_id=order_goods.order_id,order_goods.order_id=evaluate_goods.geval_orderid')->field($fiield)->where($condition)->group('evaluate_goods.geval_scores')->limit(5)->key('evaluate_goods.geval_scores')->select();
        $star1 = intval($count_array['1']['geval_count']);
        $star2 = intval($count_array['2']['geval_count']);
        $star3 = intval($count_array['3']['geval_count']);
        $star4 = intval($count_array['4']['geval_count']);
        $star5 = intval($count_array['5']['geval_count']);

        $info['good'] = $star4 + $star5;
        $info['all'] = $star1 + $star2 + $star3 + $star4 + $star5;

        $return_data = array();
        $return_data['eval_count'] = $info['all'];
        $return_data['good_percent'] = $info['all'] > 0 ? intval($info['good']/$info['all'] * 100) : 100;
        return $return_data;
    }
}