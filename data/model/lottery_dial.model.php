<?php
/**
 * 抽奖大转盘活动管理
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

class lottery_dialModel extends Model{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加大转盘活动
     * @param array $data
     * @return boolean
     */
    public function addDialActivity($data){
        return $this->table('lottery_dial_activity')->insert($data);
    }

    /**
     * 编辑大转盘活动
     * @param array $data
     * @param array $condition
     * @return mixed
     */
    public function editDialActivity($data, $condition = array()){
        return $this->table('lottery_dial_activity')->where($condition)->update($data);
    }

    /**
     * 删除活动
     * @param array $condition
     * @return mixed
     */
    public function delDialActivity($condition = array()){
        return $this->table('lottery_dial_activity')->where($condition)->delete();
    }

    /**
     * 根据活动ID获取活动详情
     * @param $id
     * @param string $field
     * @param bool $master
     * @return mixed
     */
    public function getOneDialActivityById($id, $field = '*', $master = false){
        return $this->getOneDialActivity(array('lot_id'=>intval($id)),$field,$master);
    }

    /**
     * 获取单条活动信息
     * @param array $condition
     * @param string $field
     * @param bool $master
     * @return mixed
     */
    public function getOneDialActivity($condition =array(), $field = '*', $master = false){
        return $this->table('lottery_dial_activity')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 获取多条活动信息
     * @param array $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param bool $master
     * @return mixed
     */
    public function getListDialActivity($condition = array(), $pagesize = '', $field = '*', $order = 'lot_id desc', $limit = '', $master = false){
        return $this->table('lottery_dial_activity')->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
    }

    /**
     * 添加抽奖记录
     * @param int $member_id
     * @param $data
     * @return bool
     */
    public function addDialPrize($member_id = 1,$data){
        $table = $this->_getPrizeTable($member_id);
        return $this->table($table)->insert($data);
    }

    /**
     * 修改抽奖结果记录
     * @param int $member_id
     * @param array $condition
     * @param $data
     * @return bool
     */
    public function editDialprize($member_id = 1, $condition = array(), $data){
        $table = $this->_getPrizeTable($member_id);
        return $this->table($table)->where($condition)->update($data);
    }

    /**
     * 删除抽奖结果记录
     * @param int $member_id
     * @param array $condition
     * @return bool
     */
    public function delDialPrize($member_id = 1, $condition = array()){
        $table = $this->_getPrizeTable($member_id);
        return $this->table($table)->where($condition)->delete();
    }

    /**
     * 根据ID获取抽奖结果记录
     * @param int $member_id
     * @param $id
     * @param string $field
     * @param bool $master
     * @return bool
     */
    public function getOneDialPrizeById($member_id = 1, $id, $field = '*', $master = false){
        return $this->getOneDialPrize($member_id,array('act_id'=>intval($id)),$field,$master);
    }

    /**
     * 获取单条抽奖结果记录
     * @param int $member_id
     * @param array $condition
     * @param string $field
     * @param bool $master
     * @return bool
     */
    public function getOneDialPrize($member_id = 1, $condition =array(), $field = '*', $master = false){
        $table = $this->_getPrizeTable($member_id);
        return $this->table($table)->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 获取单个会员抽奖结果记录列表
     * @param int $member_id
     * @param array $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param bool $master
     * @return bool
     */
    public function getListDialPrizeById($member_id = 1, $condition = array(), $pagesize = '', $field = '*', $order = 'lot_id desc', $limit = '', $master = false){
        $table = $this->_getPrizeTable($member_id);
        return $this->table($table)->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
    }

    /**
     * 获取单个会员抽奖次数
     * @param int $member_id
     * @param array $condition
     * @param bool $master
     * @return mixed
     */
    public function getCountDialPrize($member_id = 1, $condition = array(), $master = false){
        $table = $this->_getPrizeTable($member_id);
        return $this->table($table)->where($condition)->master($master)->count();
    }

    /**
     * 获取所有符合条件会员抽奖结果记录列表
     * @param int $member_id
     * @param array $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param bool $master
     * @return bool
     */
    public function getUnionListDialPrize($condition = array(), $pagesize = '', $field = '*', $order = 'lot_id desc', $limit = '', $master = false){
        return $this->table('lottery_dial_detail')->union('index',4)->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
    }

    /**
     * 获取抽奖结果记录数量
     * @param int $member_id
     * @param array $condition
     * @param bool $master
     * @return mixed
     */
    public function getUnionCountDialPrize($condition = array(), $master = false){
        return $this->table('lottery_dial_detail')->union('index',4)->where($condition)->master($master)->count();
    }

    /**
     * 添加抽奖资格记录
     * @param int $member_id
     * @param $data
     * @return bool
     */
    public function addDialQualifications($member_id = 1,$data){
        $table = $this->_getQualificationsTabel($member_id);
        return $this->table($table)->insert($data);
    }

    /**
     * 修改抽奖资格记录
     * @param int $member_id
     * @param array $condition
     * @param $data
     * @return bool
     */
    public function editDialQualifications($member_id = 1, $condition = array(), $data){
        $table = $this->_getQualificationsTabel($member_id);
        return $this->table($table)->where($condition)->update($data);
    }

    /**
     * 获取抽奖资格信息
     * @param int $member_id
     * @param array $condition
     * @param string $field
     * @param bool $master
     * @return bool
     */
    public function getOneDialQualifications($member_id = 1, $condition =array(), $field = '*', $master = false){
        $table = $this->_getQualificationsTabel($member_id);
        return $this->table($table)->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 获取抽奖资格表名
     * @param $member_id
     * @return bool|string
     */
    private function _getQualificationsTabel($member_id){
        if(!$member_id) return false;
        $table = getSplitTableName('lottery_dial_qualifications',$member_id);
        return $table;
    }

    /**
     * 获取抽奖记录表名
     * @param $member_id
     * @return bool|string
     */
    private function _getPrizeTable($member_id){
        if(!$member_id) return false;
        $table = getSplitTableName('lottery_dial_detail',$member_id);
        return $table;
    }
}