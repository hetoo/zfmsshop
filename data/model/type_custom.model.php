<?php
/**
 * 自定义属性模型
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

class type_customModel extends Model {
    const STATE1 = 1;       // 开启
    const STATE0 = 0;       // 关闭

    public function __construct() {
        parent::__construct('type_custom');
    }

    /**
     * 自定义属性列表
     *
     * @param array $condition
     * @param string $field
     * @param int $page
     * @param string $order
     * @return array
     */
    public function getTypeCustomList($condition, $field = '*', $order = 'custom_id asc') {
        return $this->field($field)->where($condition)->order($order)->select();
    }

    /**
     * 保存自定义属性
     *
     * @param array $insert
     * @return boolean
     */
    public function addTypeCustomAll($insert) {
        return $this->insertAll($insert);
    }

    /**
     * 编辑自定义属性
     * @param array $update
     * @param array $condition
     * @return array
     */
    public function editTypeCustom($update, $condition) {
        return $this->where($condition)->update($update);
    }

    /**
     * 删除自定义属性
     * @param array $condition
     * @return array
     */
    public function delTypeCustom($condition) {
        return $this->where($condition)->delete();
    }

}
