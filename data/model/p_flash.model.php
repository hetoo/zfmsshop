<?php
/**
 * 闪购活动模型
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
class p_flashModel extends Model{

    const FLASH_STATE_NORMAL = 1;
    const FLASH_STATE_CLOSE = 2;
    const FLASH_STATE_CANCEL = 3;

    private $flash_state_array = array(
        0 => '全部',
        self::FLASH_STATE_NORMAL => '正常',
        self::FLASH_STATE_CLOSE => '已结束',
        self::FLASH_STATE_CANCEL => '管理员关闭'
    );

    public function __construct(){
        parent::__construct('p_flash');
    }

    /**
     * 闪购状态数组
     *
     */
    public function getFlashStateArray() {
        return $this->flash_state_array;
    }

    /**
     * 读取闪购列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 闪购列表
     *
     */
    public function getFlashList($condition, $page=null, $order='flash_id desc', $field='*', $limit=0) {
        $flash_list = $this->table('p_flash')->field($field)->where($condition)->limit($limit)->page($page)->order($order)->select();
        if(!empty($flash_list)) {
            for($i =0, $j = count($flash_list); $i < $j; $i++) {
                $flash_list[$i] = $this->getFlashExtendInfo($flash_list[$i]);
            }
        }
        return $flash_list;
    }

    /**
     * 获取闪购展信息，包括状态文字和是否可编辑状态
     * @param array $flash_info
     * @return string
     *
     */
    public function getFlashExtendInfo($flash_info) {
        if($flash_info['end_time'] > TIMESTAMP) {
            $flash_info['flash_state_text'] = $this->flash_state_array[$flash_info['flash_state']];
        } else {
            $flash_info['flash_state_text'] = '已结束';
        }

        if($flash_info['flash_state'] == self::FLASH_STATE_NORMAL && $flash_info['end_time'] > TIMESTAMP) {
            $flash_info['editable'] = true;
        } else {
            $flash_info['editable'] = false;
        }

        if(!empty($flash_info['flash_brand'])){
            $flash_info['flash_brand_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$flash_info['flash_brand'];
        }
        if(!empty($flash_info['flash_pic'])){
            $flash_info['flash_pic_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$flash_info['flash_pic'];
        }
        if(!empty($flash_info['flash_banner'])){
            $flash_info['flash_banner_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$flash_info['flash_banner'];
        }
        if(!empty($flash_info['flash_recommend_pic'])){
            $flash_info['recommend_pic_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$flash_info['flash_recommend_pic'];
        }

        return $flash_info;
    }

    /**
     * 根据条件读取限制闪购信息
     * @param array $condition 查询条件
     * @return array 闪购信息
     *
     */
    public function getFlashInfo($condition) {
        $flash_info = $this->where($condition)->find();
        $flash_info = $this->getFlashExtendInfo($flash_info);
        return $flash_info;
    }

    /**
     * 根据闪购编号读取限制闪购信息
     * @param array $flash_id 限制闪购活动编号
     * @param int $store_id 如果提供店铺编号，判断是否为该店铺活动，如果不是返回null
     * @return array 闪购信息
     *
     */
    public function getFlashInfoByID($flash_id, $store_id = 0) {
        if(intval($flash_id) <= 0) {
            return null;
        }
        $condition = array();
        $condition['flash_id'] = $flash_id;
        $flash_info = $this->getFlashInfo($condition);
        if($store_id > 0 && $flash_info['store_id'] != $store_id) {
            return null;
        } else {
            return $flash_info;
        }
    }


    /**
     * 增加
     * @param array $param
     * @return bool
     *
     */
    public function addFlash($param){
        $param['flash_state'] = self::FLASH_STATE_NORMAL;
        return $this->insert($param);
    }

    /**
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     *
     */
    public function editFlash($update, $condition){
        return $this->where($condition)->update($update);
    }

    /**
     * 删除闪购活动，同时删除闪购商品
     * @param array $condition
     * @return bool
     *
     */
    public function delFlash($condition){
        $flash_list = $this->getFlashList($condition);
        $flash_id_string = '';
        if(!empty($flash_list)) {
            foreach ($flash_list as $value) {
                $flash_id_string .= $value['flash_id'] . ',';
            }
        }

        //删除闪购商品
        if($flash_id_string !== '') {
            $model_flash_goods = Model('p_flash_goods');
            $model_flash_goods->delFlashGoods(array('flash_id'=>array('in', $flash_id_string)));
        }

        return $this->where($condition)->delete();
    }

    /**
     * 取消闪购活动，同时取消闪购商品
     * @param array $condition
     * @return bool
     *
     */
    public function cancelFlash($condition){
        $flash_list = $this->getFlashList($condition);
        $flash_id_string = '';
        if(!empty($flash_list)) {
            foreach ($flash_list as $value) {
                $flash_id_string .= $value['flash_id'] . ',';
            }
        }

        $update = array();
        $update['flash_state'] = self::FLASH_STATE_CANCEL;

        //删除闪购商品
        if($flash_id_string !== '') {
            $model_flash_goods = Model('p_flash_goods');
            $model_flash_goods->editFlashGoods($update, array('flash_id'=>array('in', $flash_id_string)));
        }

        return $this->editFlash($update, $condition);
    }


    /**
     * 过期修改状态
     */
    public function editExpireFlash($condition = array()) {
        $condition['end_time'] = array('lt', TIMESTAMP);
        $condition['flash_state'] = self::FLASH_STATE_NORMAL;
        
        $list = $this->table('p_flash')->where($condition)->select();
        if(!empty($list) && is_array($list)) {
            foreach($list as $k => $v) {
                $flash_id = $v['flash_id'];
                // 更新商品促销价格
                $flashgoods_list = Model('p_flash_goods')->getFlashGoodsList(array('flash_id' => $flash_id));
                if (!empty($flashgoods_list)) {
                    $goodsid_array = array();
                    foreach ($flashgoods_list as $val) {
                        $goodsid_array[] = $val['goods_id'];
                    }
                    // 更新商品促销价格，需要考虑团购是否在进行中
                    QueueClient::push('updateGoodsPromotionPriceByGoodsId', $goodsid_array);
                }
                $updata = array();
                $update['flash_state'] = self::FLASH_STATE_CLOSE;
                $this->editFlash($update, array('flash_id' => $flash_id));
            }
        }
        return true;
    }

    /**
     * 更新头部图片信息
     */
    public function updateRecommendPic($data){
        if (is_array($data)){
            $str = serialize($data);
            $result = $this->table('web_code')->where(array('var_name' => 'flash_pic'))->update(array('code_info' => $str));
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 头部图片
     */
    public function getRecommendPic() {
        $result = $this->table('web_code')->where(array('var_name' => 'flash_pic'))->find();
        return $result;
    }
}