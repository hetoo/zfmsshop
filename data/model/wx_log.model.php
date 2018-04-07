<?php
/**
 * 微信消息记录
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

class wx_logModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加记录
     *
     * @param
     * @return int
     */
    public function addWx($log_array) {
        $log_array['msg_type'] = 3;//类型:1邮件,2短信,3微信
        $log_array['log_type'] = 2;//来源:1商城发出,2微信推送
        $log_array['add_time'] = TIMESTAMP;
        $log_id = $this->table('realtime_log')->insert($log_array);
        return $log_id;
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getWxInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('realtime_log')->where($condition)->order('log_id desc')->find();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getWxList($condition = array(), $page = '', $limit = '', $order = 'log_id desc') {
        $result = $this->table('realtime_log')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }
    
    /**
     * 取得记录数量
     *
     * @param
     * @return int
     */
    public function getLogCount($condition = array()) {
        return $this->table('realtime_log')->where($condition)->count();
    }

    /**
     * 查询模板记录
     *
     * @param
     * @return array
     */
    public function getWxTpl($condition = array()) {
        $result = $this->table('wx_msg_tpl')->where($condition)->select();
        return $result;
    }

    /**
     * 修改模板记录
     *
     * @param
     * @return bool
     */
    public function editWxTpl($condition, $data) {
        if (empty($condition)) {
            return false;
        }
        if (is_array($data)) {
            $result = $this->table('wx_msg_tpl')->where($condition)->update($data);
            return $result;
        } else {
            return false;
        }
    }
}
