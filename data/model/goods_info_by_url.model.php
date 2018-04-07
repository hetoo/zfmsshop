<?php
/**
 * 根据url获取商品信息
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2012 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @url       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class goods_info_by_urlModel extends Model {

    /**
     * 根据链接返回商品信息
     */
    public function get_goods_info_by_url($url) {
        $url_host_name = self::get_url_domain($url);
        $store_host_name = self::get_url_domain(SHOP_SITE_URL);
        switch ($url_host_name) {
        case $store_host_name:
            return self::get_store_goods_info_by_url($url);
        default:
            return FALSE;
            break;
        }
    }

    /**
     * 判断链接合法性
     */
    public function check_personal_buy_link($url) {
        $link_host_name = self::get_url_domain($url);
        $store_host_name = self::get_url_domain(SHOP_SITE_URL);
        switch ($link_host_name) {
            case $store_host_name:
                return TRUE;
            default:
                return FALSE;
                break;
        }
    }

    /**
     * 获取主域名
     */
    private function get_url_domain($url) {
        $url_parse_array = parse_url($url);
        $host = $url_parse_array['host'];
        $host_names = explode(".", $host);
        $bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
        return $bottom_host_name;
    }

    private function get_store_goods_info_by_url($url) {
        $array = parse_url($url);
        $goods_id = 0;
        if(isset($array['query'])){
            // 未开启伪静态
            parse_str($array['query'],$arr);
            $goods_id = $arr['goods_id'];
        }else{
            // 开启伪静态
            $data = explode('/', $array['path']);
            $path = end($data);
            $goods_id = preg_replace('/item-(\d+)\.html/i', '$1', $path);
        }
        if(intval($goods_id) > 0) {
            $model = Model('goods');
            $goods_info = $model->getGoodsInfoByID(intval($goods_id));
            if(!empty($goods_info)) {
                $result = array();
                $result['result'] = 'true';
                $result['id'] = intval($goods_id);
                $result['url'] = $url;
                $result['price'] = $goods_info['goods_price'];
                $result['storeid'] = $goods_info['store_id'];
                $result['title'] = $goods_info['goods_name'];
                $result['img'] = $goods_info['goods_image'];
                $result['image'] = thumb($goods_info, 240);
                $result['type'] = 'store';
                return $result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}
