<?php
/**
 * 店铺模型管理
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class storeModel extends Model {

    /**
     * 自营店铺的ID
     *
     * array(
     *   '店铺ID(int)' => '是否绑定了全部商品类目(boolean)',
     *   // ..
     * )
     */
    protected $ownShopIds;

    public function __construct() {
        parent::__construct('store');
    }

    /**
     * 删除缓存自营店铺的ID
     */
    public function dropCachedOwnShopIds() {
        $this->ownShopIds = null;
        dkcache('own_shop_ids');
    }

    /**
     * 获取自营店铺的ID
     *
     * @param boolean $bind_all_gc = false 是否只获取绑定全部类目的自营店 默认否（即全部自营店）
     * @return array
     */
    public function getOwnShopIds($bind_all_gc = false) {

        $data = $this->ownShopIds;

        // 属性为空则取缓存
        if (!$data) {
            $data = rkcache('own_shop_ids');

            // 缓存为空则查库
            if (!$data) {
                $data = array();
                $all_own_shops = $this->table('store')->field('store_id,bind_all_gc')->where(array(
                    'is_own_shop' => 1,
                ))->select();
                foreach ((array) $all_own_shops as $v) {
                    $data[$v['store_id']] = (int) (bool) $v['bind_all_gc'];
                }

                // 写入缓存
                wkcache('own_shop_ids', $data);
            }

            // 写入属性
            $this->ownShopIds = $data;
        }

        return array_keys($bind_all_gc ? array_filter($data) : $data);
    }

    /**
     * 查询店铺列表
     *
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 字段
     * @param string $limit 取多少条
     * @return array
     */
    public function getStoreList($condition, $page = null, $order = '', $field = '*', $limit = '') {
        $result = $this->field($field)->where($condition)->order($order)->limit($limit)->page($page)->select();
        return $result;
    }

    /**
     * 查询有效店铺列表
     *
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 字段
     * @return array
     */
    public function getStoreOnlineList($condition, $page = null, $order = '', $field = '*') {
        $condition['store_state'] = 1;
        return $this->getStoreList($condition, $page, $order, $field);
    }

    /**
     * 店铺数量
     * @param array $condition
     * @return int
     */
    public function getStoreCount($condition) {
        return $this->where($condition)->count();
    }

    /**
     * 按店铺编号查询店铺的信息
     *
     * @param array $storeid_array 店铺编号
     * @return array
     */
    public function getStoreMemberIDList($storeid_array, $field = 'store_id,member_id,store_domain') {
        $field .= ',default_im';
        $list = $this->table('store')->where(array('store_id'=> array('in', $storeid_array)))->field($field)->select();
        $store_list = array();
        if (!empty($list) && is_array($list)) {
            foreach ($list as $key => $value) {
                $value['member_id'] = $value['default_im'];
                $store_list[$value['store_id']] = $value;
            }
        }
        return $store_list;
    }

    /**
     * 查询店铺信息
     *
     * @param array $condition 查询条件
     * @return array
     */
    public function getStoreInfo($condition) {
        $store_info = $this->where($condition)->find();
        if(!empty($store_info)) {
            if(!empty($store_info['store_presales'])) $store_info['store_presales'] = unserialize($store_info['store_presales']);
            if(!empty($store_info['store_aftersales'])) $store_info['store_aftersales'] = unserialize($store_info['store_aftersales']);

            //商品数
            $model_goods = Model('goods');
            $store_info['goods_count'] = $model_goods->getGoodsCommonOnlineCount(array('store_id' => $store_info['store_id']));

            //店铺评价
            $model_evaluate_store = Model('evaluate_store');
            $store_evaluate_info = $model_evaluate_store->getEvaluateStoreInfoByStoreID($store_info['store_id'], $store_info['sc_id']);

            $store_info = array_merge($store_info, $store_evaluate_info);
        }
        return $store_info;
    }

    /**
     * 通过店铺编号查询店铺信息
     *
     * @param int $store_id 店铺编号
     * @return array
     */
    public function getStoreInfoByID($store_id) {
            $store_info = $this->getStoreInfo(array('store_id' => $store_id));
        return $store_info;
    }

    public function getStoreOnlineInfoByID($store_id) {
        $store_info = $this->getStoreInfoByID($store_id);
        if(empty($store_info) || $store_info['store_state'] == '0') {
            return array();
        } else {
            return $store_info;
        }
    }

    public function getStoreIDString($condition) {
        $condition['store_state'] = 1;
        $store_list = $this->getStoreList($condition);
        $store_id_string = '';
        foreach ($store_list as $value) {
            $store_id_string .= $value['store_id'].',';
        }
        return $store_id_string;
    }

    /*
     * 添加店铺
     *
     * @param array $param 店铺信息
     * @return bool
     */
    public function addStore($param){
        $param['default_im'] = $param['member_id'];
        return $this->insert($param);
    }

    /*
     * 编辑店铺
     *
     * @param array $update 更新信息
     * @param array $condition 条件
     * @return bool
     */
    public function editStore($update, $condition){
        return $this->where($condition)->update($update);
    }

    /*
     * 删除店铺
     *
     * @param array $condition 条件
     * @return bool
     */
    public function delStore($condition){
        $store_info = $this->getStoreInfo($condition);
        //删除店铺相关图片
        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_label']);
        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_banner']);
        if($store_info['store_slide'] != ''){
            foreach(explode(',', $store_info['store_slide']) as $val){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_SLIDE.DS.$val);
            }
        }

        return $this->where($condition)->delete();
    }

    /**
     * 完全删除店铺 包括店主账号、店铺的管理员账号、店铺相册、店铺扩展
     */
    public function delStoreEntirely($condition)
    {
        $this->delStore($condition);
        Model('seller')->delSeller($condition);
        Model('seller_group')->delSellerGroup($condition);
        Model('album')->delAlbum($condition['store_id']);
        Model('store_extend')->delStoreExtend($condition);
    }

    /**
     * 获取商品销售排行(每天更新一次)
     *
     * @param int $store_id 店铺编号
     * @param int $limit 数量
     * @return array    商品信息
     */
    public function getHotSalesList($store_id, $limit = 5, $isMobile = false) {
        $model_goods = Model('goods');
        $where = array(
            'store_id' => $store_id,
        );
        // 手机端不显示预订商品
        if ($isMobile) {
            $where['is_book'] = 0;
        }
        $fields = "goods_id,goods_commonid,goods_name,store_id,goods_promotion_price,goods_image,goods_salenum";
        if (C('dbdriver') == 'oracle') {
            $oracle_fields = array();
            $fields = explode(',', $fields);
            foreach ($fields as $val) {
                $oracle_fields[] = 'min('.$val.') '.$val;
            }
            $fields = implode(',', $oracle_fields);
        }
        $hot_sales_list = $model_goods->getGoodsOnlineList($where, $fields, 0, 'goods_salenum desc', $limit, 'goods_commonid');
        return $hot_sales_list;
    }

    /**
     * 获取商品收藏排行(每天更新一次)
     *
     * @param int $store_id 店铺编号
     * @param int $limit 数量
     * @return array    商品信息
     */
    public function getHotCollectList($store_id, $limit = 5) {
        $model_goods = Model('goods');
        $fields = "goods_id,goods_commonid,goods_name,store_id,goods_promotion_price,goods_image,goods_collect";
        if (C('dbdriver') == 'oracle') {
            $oracle_fields = array();
            $fields = explode(',', $fields);
            foreach ($fields as $val) {
                $oracle_fields[] = 'min('.$val.') '.$val;
            }
            $fields = implode(',', $oracle_fields);
        }
        $hot_collect_list = $model_goods->getGoodsOnlineList(array('store_id' => $store_id), $fields, 0, 'goods_collect desc', $limit, 'goods_commonid');
        return $hot_collect_list;
    }

    /**
     * 店铺搜索列表
     */
    public function getStoreSearchList($store_list){
        $return_list = array();
        $model_goods = Model('goods');
        foreach($store_list as $value){
            $tmp = $value;
            // 推荐商品列表
            $goods_list = $model_goods->getGoodsCommendList($value['store_id'], 4);
            // 获取店铺商品数
            $goods_count = $model_goods->getGoodsCommonOnlineCount(array('store_id' => $value['store_id']));

            //店铺评价
            $model_evaluate_store = Model('evaluate_store');
            $store_evaluate_info = $model_evaluate_store->getEvaluateStoreInfoByStoreID($value['store_id'], $value['sc_id']);

            $tmp['online_goods_count'] = $goods_count;
            $tmp['commend_goods_list'] = $goods_list;
            $tmp = array_merge($tmp,$store_evaluate_info);
            $return_list[] = $tmp;
        }
        return $return_list;
    }

}
