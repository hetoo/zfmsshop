<?php
/**
 * 代金券模型
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class chain_voucherModel extends Model {
    const VOUCHER_STATE_UNUSED = 1;
    const VOUCHER_STATE_USED = 2;
    const VOUCHER_STATE_EXPIRE = 3;

    private $voucher_state_array = array(
        self::VOUCHER_STATE_UNUSED => '未使用',
        self::VOUCHER_STATE_USED => '已使用',
        self::VOUCHER_STATE_EXPIRE => '已过期',
    );

    private $templatestate_arr;

    public function __construct(){
        parent::__construct();
        //代金券模板状态
        $this->templatestate_arr = array('usable'=>array(1,'有效'),'disabled'=>array(2,'失效'));
    }

    /**
     * 获取代金券模板状态
     */
    public function getTemplateState(){
        return $this->templatestate_arr;
    }

    /**
     * 领取的代金券状态
     */
    public function getVoucherState(){
        return array('unused'=>array(1,Language::get('voucher_voucher_state_unused')),'used'=>array(2,Language::get('voucher_voucher_state_used')),'expire'=>array(3,Language::get('voucher_voucher_state_expire')));
    }

    /**
     * 返回当前可用的代金券列表,每种类型(模板)的代金券里取出一个代金券码(同一个模板所有码面额和到期时间都一样)
     * @param array $condition 条件
     * @param array $goods_total 商品总金额
     * @return string
     */
    public function getCurrentAvailableVoucher($condition = array(), $goods_total = 0,$order = '') {
        $condition['voucher_end_date'] = array('gt',TIMESTAMP);
        $condition['voucher_state'] = 1;
        $voucher_list = $this->table('chain_voucher')->where($condition)->order($order)->key('voucher_t_id')->select();
        foreach ($voucher_list as $key => $voucher) {
            if ($goods_total < $voucher['voucher_limit']) {
                unset($voucher_list[$key]);
            } else {
                $voucher_list[$key]['desc'] = sprintf('面额%s元 有效期至 %s ',$voucher['voucher_price'],date('Y-m-d',$voucher['voucher_end_date']));
                if ($voucher['voucher_limit'] > 0) {
                    $voucher_list[$key]['desc'] .= sprintf(' 消费满%s可用',$voucher['voucher_limit']);
                }
            }
        }
        return $voucher_list;
    }

    /**
     * 取得会员当前有效代金券数量
     * @param int $member_id
     */
    public function getCurrentAvailableVoucherCount($member_id) {
        $condition['voucher_owner_id'] = $member_id;
        $condition['voucher_end_date'] = array('gt',TIMESTAMP);
        $condition['voucher_state'] = 1;
        $voucher_count = $this->table('chain_voucher')->where($condition)->count();
        $voucher_count = intval($voucher_count);
        return $voucher_count;
    }

    /**
     * 获得代金券列表
     */
    public function getVoucherList($where, $field = '*', $limit = 0, $page = 0, $order = '', $group = ''){
        $voucher_list = array();
        if (is_array($page)){
            if ($page[1] > 0){
                $voucher_list = $this->table('chain_voucher')->field($field)->where($where)->limit($limit)->page($page[0],$page[1])->order($order)->group($group)->select();
            } else {
                $voucher_list = $this->table('chain_voucher')->field($field)->where($where)->limit($limit)->page($page[0])->order($order)->group($group)->select();
            }
        } else {
            $voucher_list = $this->table('chain_voucher')->field($field)->where($where)->limit($limit)->page($page)->order($order)->group($group)->select();
        }
        return $voucher_list;
    }

    /**
     * 获取未使用代金券列表
     * @param $where
     * @param string $field
     * @return array
     */
    public function getVoucherUnusedList($where, $field = '*') {
        $where['voucher_state'] = 1;
        return $this->getVoucherList($where, $field);
    }
    /**
     * 领取代金券
     */
    public function receiveVoucher($vid,$member_id){
        if ($vid <= 0 || $member_id <= 0 ){
            return array('state'=>false,'msg'=>'参数错误');
        }
        //查询可用代金券模板
        $where = array();
        $where['voucher_t_id'] = $vid;
        $where['voucher_t_state'] = $this->templatestate_arr['usable'][0];
        $where['voucher_t_end_date'] = array('gt',time());
        $template_info = $this->getVoucherTemplateInfo($where);
        if (empty($template_info)){//代金券不存在或者已兑换完
            return array('state'=>false,'msg'=>'代金券已兑换完');
        }

        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($member_id);
        if (empty($member_info)){
            return array('state'=>false,'msg'=>'参数错误');
        }

        //查询代金券对应的店铺信息
        $chain_info = Model('chain')->getChainInfo(array('chain_id'=>$template_info['voucher_t_chain_id']));
        if (empty($chain_info)){
            return array('state'=>false,'msg'=>'代金券已兑换完');
        }
        $store_info = Model('store')->getStoreInfoByID($chain_info['store_id']);
        if (empty($store_info)){
            return array('state'=>false,'msg'=>'代金券已兑换完');
        }

        //查询会员店铺信息
        $seller_info = Model('seller')->getSellerInfo(array('member_id'=>$member_id));

        //验证是否为店铺自己
        if (!empty($seller_info) && $seller_info['store_id'] == $chain_info['store_id']){
           return array('state'=>false,'msg'=>'不可以兑换自己店铺的代金券');
        }

        //整理代金券信息
        $template_info = array_merge($template_info,$store_info);
        //查询代金券列表
        $where = array();
        $where['voucher_owner_id'] = $member_id;
        $where['voucher_t_id'] = $template_info['voucher_t_id'];
        $voucher_count = $this->getVoucherCount($where);
        if (intval($template_info['voucher_t_eachlimit']) >0 && $voucher_count >= $template_info['voucher_t_eachlimit']){
            $message = sprintf('该代金券您已兑换%s次，不可再兑换了',$template_info['voucher_t_eachlimit']);
            return array('state'=>false,'msg'=>$message);
        }

        $insert_arr = array();
        $insert_arr['voucher_code'] = $this->get_voucher_code($member_info['member_id']);
        $insert_arr['voucher_t_id'] = $template_info['voucher_t_id'];
        $insert_arr['voucher_title'] = $template_info['voucher_t_title'];
        $insert_arr['voucher_desc'] = $template_info['voucher_t_desc'];
        $insert_arr['voucher_start_date'] = $template_info['voucher_t_start_date'];
        $insert_arr['voucher_end_date'] = $template_info['voucher_t_end_date'];
        $insert_arr['voucher_price'] = $template_info['voucher_t_price'];
        $insert_arr['voucher_limit'] = $template_info['voucher_t_limit'];
        $insert_arr['voucher_chain_id'] = $template_info['voucher_t_chain_id'];
        $insert_arr['voucher_state'] = 1;
        $insert_arr['voucher_active_date'] = TIMESTAMP;
        $insert_arr['voucher_owner_id'] = $member_info['member_id'];
        $insert_arr['voucher_owner_name'] = $member_info['member_name'];

        $result = $this->table('chain_voucher')->insert($insert_arr);

        if ($result){
            //代金券模板的兑换数增加
            $result = $this->editVoucherTemplate(array('voucher_t_id'=>$template_info['voucher_t_id']), array('voucher_t_giveout'=>array('exp','voucher_t_giveout+1')));
            if (!$result){
                return array('state'=>false,'msg'=>'领取失败');
            }
            return array('state'=>true,'msg'=>'领取成功');
        } else {
            return array('state'=>false,'msg'=>'领取失败');
        }
    }

    /**
     * 获取代金券编码
     */
    public function get_voucher_code($member_id = 0){
        static $num = 1;
        $sign_arr = array();
        $sign_arr[] = sprintf('%02d',mt_rand(10,99));
        $sign_arr[] = sprintf('%03d', (float) microtime() * 1000);
        $sign_arr[] = sprintf('%010d',time() - 946656000);
        if($member_id){
            $sign_arr[] = sprintf('%03d', (int) $member_id % 1000);
        } else {
            //自增变量
            $tmpnum = 0;
            if ($num > 99){
                $tmpnum = substr($num, -1, 2);
            } else {
                $tmpnum = $num;
            }
            $sign_arr[] = sprintf('%02d',$tmpnum);
            $sign_arr[] = mt_rand(1,9);
        }
        $code = implode('',$sign_arr);
        $num += 1;
        return $code;
    }

    /**
     * 更新代金券信息
     * @param array $data
     * @param array $condition
     */
    public function editVoucher($data,$condition) {
        $result = $this->table('chain_voucher')->where($condition)->update($data);
        return $result;
    }

    /**
     * 返回代金券状态数组
     * @return array
     */
    public function getVoucherStateArray() {
        return $this->voucher_state_array;
    }

    /**
     * 获取买家代金券列表
     *
     * @param int $member_id 用户编号
     * @param int $voucher_state 代金券状态
     * @param int $page 分页数
     */
    public function getMemberVoucherList($member_id, $voucher_state, $page = null, $order = 'voucher_id desc') {
        if(empty($member_id)) {
            return false;
        }

        //更新过期代金券状态
        $this->_checkVoucherExpire($member_id);

        $field = 'voucher_id,voucher_code,voucher_title,voucher_desc,voucher_start_date,voucher_end_date,voucher_price,voucher_limit,voucher_state,voucher_order_id,voucher_chain_id,chain_name,chain_id,chain_img,store_id';

        $on = 'chain_voucher.voucher_chain_id=chain.chain_id';

        $where = array('voucher_owner_id'=>$member_id);
        $voucher_state  = intval($voucher_state);
        if (intval($voucher_state) > 0 && array_key_exists($voucher_state, $this->voucher_state_array)){
            $where['voucher_state'] = $voucher_state;
        }

        $list = $this->table('chain_voucher,chain')->field($field)->join('inner')->on($on)->where($where)->order($order)->page($page)->select();

        if(!empty($list) && is_array($list)){
            foreach ($list as $key=>$val){
                $list[$key]['chain_logo_url'] = getChainImage($val['chain_img'], $val['store_id']);
                //代金券状态文字
                $list[$key]['voucher_state_text'] = $this->voucher_state_array[$val['voucher_state']];
                //代金券有效期
                $list[$key]['voucher_start_date_text'] = @date('Y-m-d',$val['voucher_start_date']);
                $list[$key]['voucher_end_date_text'] = @date('Y-m-d',$val['voucher_end_date']);
            }
        }
        return $list;
    }

    /**
     * 更新过期代金券状态
     */
    private function _checkVoucherExpire($member_id) {
        $condition = array();
        $condition['voucher_owner_id'] = $member_id;
        $condition['voucher_state'] = self::VOUCHER_STATE_UNUSED;
        $condition['voucher_end_date'] = array('lt', TIMESTAMP);

        $this->table('chain_voucher')->where($condition)->update(array('voucher_state' => self::VOUCHER_STATE_EXPIRE));
    }

    /**
     * 查询代金券模板列表
     */
    public function getVoucherTemplateList($where, $field = '*', $limit = 0, $page = 0, $order = '', $group = '') {
        $voucher_list = array();
        if (is_array($page)){
            if ($page[1] > 0){
                $voucher_list = $this->table('chain_voucher_template')->field($field)->where($where)->limit($limit)->page($page[0],$page[1])->order($order)->group($group)->select();
            } else {
                $voucher_list = $this->table('chain_voucher_template')->field($field)->where($where)->limit($limit)->page($page[0])->order($order)->group($group)->select();
            }
        } else {
            $voucher_list = $this->table('chain_voucher_template')->field($field)->where($where)->limit($limit)->page($page)->order($order)->group($group)->select();
        }

        if (!empty($voucher_list) && is_array($voucher_list)){
            foreach ($voucher_list as $k=>$v){
                //状态
                if($v['voucher_t_state']){
                    foreach($this->templatestate_arr as $tstate_k=>$tstate_v){
                        if($v['voucher_t_state'] == $tstate_v[0]){
                            $v['voucher_t_state_text'] = $tstate_v[1];
                        }
                    }
                }
                $voucher_list[$k] = $v;
            }
        }
        return $voucher_list;
    }

    /**
     * 更新代金券模板信息
     * @param array $data
     * @param array $condition
     */
    public function editVoucherTemplate($where,$data) {
        return $this->table('chain_voucher_template')->where($where)->update($data);
    }
    /**
     * 批量增加代金券
     */
    public function addVoucherBatch($insert_arr){
        return $this->table('chain_voucher')->insertAll($insert_arr);
    }
    /**
     * 获得代金券模板总数量
     */
    public function getVoucherTemplateCount($where){
        return $this->table('chain_voucher_template')->where($where)->count();
    }

    /**
     * 获得代金券总数量
     */
    public function getVoucherCount($where){
        return $this->table('chain_voucher')->where($where)->count();
    }

    /**
     * 获得代金券模板详情
     */
    public function getVoucherTemplateInfo($where = array(), $field = '*', $order = '',$group = '') {
        $info = $this->table('chain_voucher_template')->where($where)->field($field)->order($order)->group($group)->find();
        if(!$info){
            return array();
        }
        if($info['voucher_t_state']){
            foreach($this->templatestate_arr as $k=>$v){
                if($info['voucher_t_state'] == $v[0]){
                    $info['voucher_t_state_text'] = $v[1];
                }
            }
        }
        return $info;
    }

    /**
     * 获得代金券详情
     */
    public function getVoucherInfo($where = array(), $field = '*', $order = '',$group = '') {
        $info = $this->table('chain_voucher')->where($where)->field($field)->order($order)->group($group)->find();
        if($info['voucher_state']){
            $info['voucher_state_text'] = $this->voucher_state_array[$info['voucher_state']];
        }
        return $info;
    }

    /**
     * 退还已经使用的代金券
     * @param $order_id
     * @return boolean true/false
     */
    public function returnVoucher($order_id) {
        if (!preg_match('/^\d+$/',$order_id)) return true;
        $order_info = Model('order')->getOrderCommonInfo(array('order_id'=>$order_id),'voucher_code');
        if (!$order_info || $order_info['voucher_code'] == '') return true;
        $voucher_info = $this->getVoucherInfo(array('voucher_code'=>$order_info['voucher_code'],'voucher_state'=>2));
        if (!$voucher_info) return true;
        $update = $this->editVoucher(array('voucher_state'=>1,'voucher_order_id'=>0),array('voucher_id'=>$voucher_info['voucher_id']));
        if ($update) {
            $update = $this->editVoucherTemplate(array('voucher_t_id'=>$voucher_info['voucher_t_id']), array('voucher_t_used'=>array('exp','voucher_t_used-1')));
            if (!$update) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
}
