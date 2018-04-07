<?php
/**
 * 微分销
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
class distributeControl extends mobileHomeControl{

    /**
     * 获取分销员信息
     */
    public function get_distri_infoOp(){
        $member_id = intval($_REQUEST['dis_id']);
        if($member_id <= 0){
            output_error('参数错误');
        }
        $member_model = Model('member');
        $field = "member_id,member_name";
        $member_info = $member_model->getMemberInfoByID($member_id, $field);
        if(empty($member_info)){
            output_error('用户不存在');
        }
        $member_info['member_avatar'] = getMemberAvatarForID($member_info['member_id']);
        output_data(array('member_info'=>$member_info));
    }

    /**
     * 分销员商品列表
     */
    public function distri_goods_listOp(){
        $param = $_REQUEST;
        $member_id = intval($param['dis_id']);
        if($member_id <= 0){
            output_error('参数错误');
        }
        $model_dis_goods = Model('dis_goods');
        $field = $this->getGoodsFields();
        $condition = array();
        $condition['dis_goods.member_id'] = $member_id;

        $price_from = preg_match('/^[\d.]{1,20}$/',$param['price_from']) ? $param['price_from'] : null;
        $price_to = preg_match('/^[\d.]{1,20}$/',$param['price_to']) ? $param['price_to'] : null;
        if ($price_from && $price_from) {
            $condition['goods.goods_promotion_price'] = array('between',"{$price_from},{$price_to}");
        } elseif ($price_from) {
            $condition['goods.goods_promotion_price'] = array('egt',$price_from);
        } elseif ($price_to) {
            $condition['goods.goods_promotion_price'] = array('elt',$price_to);
        }

        // 排序
        $order = (int) $param['order'] == 1 ? 'asc' : 'desc';
        switch (trim($param['key'])) {
            case '2':
                $order = 'goods.goods_promotion_price '.$order;
                break;
            case '3':
                $order = 'goods.goods_salenum '.$order;
                break;
            case '5':
                $order = 'goods.goods_click '.$order;
                break;
            default:
                $order = 'goods.goods_id desc';
                break;
        }

        $goods_list = $model_dis_goods->getDistriGoodsInfoList($condition, $field, $this->page, $order);
        $page_count = $model_dis_goods->gettotalpage();

        $goods_list = $this->_goods_list_extend($goods_list);

        output_data(array(
            'goods_list_count' => count($goods_list),
            'goods_list' => $goods_list,
        ), mobile_page($page_count));
    }


    /**
     * 处理商品列表(团购、限时折扣、商品图片)
     */
    private function _goods_list_extend($goods_list) {
        //获取商品列表编号数组
        $goodsid_array = array();
        foreach($goods_list as $key => $value) {
            $goodsid_array[] = $value['goods_id'];
        }

        $sole_array = Model('p_sole')->getSoleGoodsList(array('goods_id' => array('in', $goodsid_array)));
        $sole_array = array_under_reset($sole_array, 'goods_id');

        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['sole_flag']      = false;
            $goods_list[$key]['group_flag']     = false;
            $goods_list[$key]['xianshi_flag']   = false;
            if (!empty($sole_array[$value['goods_id']])) {
                $goods_list[$key]['goods_price'] = $sole_array[$value['goods_id']]['sole_price'];
                $goods_list[$key]['sole_flag'] = true;
            } else {
                $goods_list[$key]['goods_price'] = $value['goods_promotion_price'];
                switch ($value['goods_promotion_type']) {
                    case 1:
                        $goods_list[$key]['group_flag'] = true;
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $goods_list[$key]['xianshi_flag'] = true;
                        break;
                }

            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']);

            unset($goods_list[$key]['goods_promotion_type']);
            unset($goods_list[$key]['goods_promotion_price']);
            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['nc_distinct']);
        }

        return $goods_list;
    }

    private function getGoodsFields()
    {
        return implode(',', array(
            'goods.goods_id',
            'goods.goods_commonid',
            'goods.store_id',
            'goods.store_name',
            'goods.goods_name',
            'goods.goods_price',
            'goods.goods_promotion_price',
            'goods.goods_promotion_type',
            'goods.goods_marketprice',
            'goods.goods_image',
            'goods.goods_salenum',
            'goods.evaluation_good_star',
            'goods.evaluation_count',
            'goods.is_virtual',
            'goods.is_presell',
            'goods.is_fcode',
            'goods.have_gift',
            'goods.goods_addtime',
            'dis_goods.distri_id'
        ));
    }
}