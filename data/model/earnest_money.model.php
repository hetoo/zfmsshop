<?php
/**
 * 保证金
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class earnest_moneyModel extends Model {
    /**
     * 取得保证金列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $fields
     * @param string $order
     */
    public function getEarnestMoneyList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('earnest_money')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /**
     * 添加保证金记录
     * @param array $data
     */
    public function addEarnestMoney($data) {
        return $this->table('earnest_money')->insert($data);
    }

    /**
     * 编辑保证金记录
     * @param unknown $data
     * @param unknown $condition
     */
    public function editEarnestMoney($data,$condition = array()) {
        return $this->table('earnest_money')->where($condition)->update($data);
    }

    /**
     * 取得单条保证金信息
     * @param unknown $condition
     * @param string $fields
     */
    public function getEarnestMoneyInfo($condition = array(), $fields = '*',$lock = false) {
        return $this->table('earnest_money')->where($condition)->field($fields)->lock($lock)->find();
    }


    /**
     * 取保证金信息总数
     * @param unknown $condition
     */
    public function getEarnestMoneyCount($condition = array()) {
        return $this->table('earnest_money')->where($condition)->count();
    }

    /**
     * 保证金变更
     * @param $change_type
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function changeEarnestMoney($change_type,$data = array()){
        $data_log = array();
        $data_etm = array();
        $pd_log = array();

        $data_log['lg_member_id'] = $data['member_id'];
        $data_log['lg_member_name'] = $data['member_name'];
        $data_log['lg_add_time'] = TIMESTAMP;
        $data_log['lg_type'] = $change_type;

        switch ($change_type){
            case 'store_joinin':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '店铺入驻保证金支付，订单号: '.$data['etm_sn'];

                $data_etm['earnest_money'] = array('exp','earnest_money+'.$data['amount']);
                break;
            case 'close_store':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '关闭店铺退还保证金，订单号: '.$data['etm_sn'];

                $data_etm['earnest_money'] = array('exp','earnest_money-'.$data['amount']);
                break;
            case 'open_chain':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '开设门店保证金支付，订单号: '.$data['etm_sn'];

                $data_etm['earnest_money'] = array('exp','earnest_money+'.$data['amount']);
                break;
            case 'close_chain':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '关闭门店退还保证金，订单号: '.$data['etm_sn'];

                $data_etm['earnest_money'] = array('exp','earnest_money-'.$data['amount']);
                break;
            case 'admin_addition':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '平台管理员因: '.$data['etm_resion'].',增加保证金';

                $data_etm['earnest_money'] = array('exp','earnest_money+'.$data['amount']);
                break;
            case 'admin_reduction':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '平台管理员因: '.$data['etm_resion'].',减少保证金';

                $data_etm['earnest_money'] = array('exp','earnest_money-'.$data['amount']);
                break;
            default:
                throw new Exception('参数错误');
                break;
        }

        $update = Model('member')->editMember(array('member_id'=>$data['member_id']),$data_etm);
        if (!$update) {
            throw new Exception('操作失败');
        }
        $insert = $this->table('earnest_money_log')->insert($data_log);
        if (!$insert) {
            throw new Exception('操作失败');
        }



        return $insert;
    }


    /**
     * 取得保证金日志列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $fields
     * @param string $order
     */
    public function getEarnestMoneyLogList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('earnest_money_log')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /**
     * 添加保证金记录
     * @param array $data
     */
    public function addEarnestMoneyLog($data) {
        return $this->table('earnest_money_log')->insert($data);
    }

}