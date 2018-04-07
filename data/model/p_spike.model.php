<?php
/**
 * 秒杀活动模型
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
class p_spikeModel extends Model{

    const SPIKE_STATE_NORMAL = 1;
    const SPIKE_STATE_CLOSE = 2;
    const SPIKE_STATE_CANCEL = 3;

    private $spike_state_array = array(
        0 => '全部',
        self::SPIKE_STATE_NORMAL => '正常',
        self::SPIKE_STATE_CLOSE => '已结束',
        self::SPIKE_STATE_CANCEL => '管理员关闭'
    );

    public function __construct(){
        parent::__construct('p_spike');
    }

    /**
     * 秒杀状态数组
     *
     */
    public function getSpikeStateArray() {
        return $this->spike_state_array;
    }

    /**
     * 读取秒杀列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 秒杀列表
     *
     */
    public function getSpikeList($condition, $page=null, $order='', $field='*', $limit=0) {
        $spike_list = $this->field($field)->where($condition)->limit($limit)->page($page)->order($order)->select();
        if(!empty($spike_list)) {
            for($i =0, $j = count($spike_list); $i < $j; $i++) {
                $spike_list[$i] = $this->getSpikeExtendInfo($spike_list[$i]);
            }
        }
        return $spike_list;
    }

    /**
     * 获取秒杀展信息，包括状态文字和是否可编辑状态
     * @param array $spike_info
     * @return string
     *
     */
    public function getSpikeExtendInfo($spike_info) {
        if($spike_info['end_time'] > TIMESTAMP) {
            $spike_info['spike_state_text'] = $this->spike_state_array[$spike_info['spike_state']];
        } else {
            $spike_info['spike_state_text'] = '已结束';
        }

        if($spike_info['spike_state'] == self::SPIKE_STATE_NORMAL && $spike_info['end_time'] > TIMESTAMP) {
            $spike_info['editable'] = true;
        } else {
            $spike_info['editable'] = false;
        }

        return $spike_info;
    }

    /**
     * 根据条件读取限制秒杀信息
     * @param array $condition 查询条件
     * @return array 秒杀信息
     *
     */
    public function getSpikeInfo($condition) {
        $spike_info = $this->where($condition)->find();
        $spike_info = $this->getSpikeExtendInfo($spike_info);
        return $spike_info;
    }

    /**
     * 根据秒杀编号读取限制秒杀信息
     * @param array $spike_id 限制秒杀活动编号
     * @param int $store_id 如果提供店铺编号，判断是否为该店铺活动，如果不是返回null
     * @return array 秒杀信息
     *
     */
    public function getSpikeInfoByID($spike_id, $store_id = 0) {
        if(intval($spike_id) <= 0) {
            return null;
        }
        $condition = array();
        $condition['spike_id'] = $spike_id;
        $spike_info = $this->getSpikeInfo($condition);
        if($store_id > 0 && $spike_info['store_id'] != $store_id) {
            return null;
        } else {
            return $spike_info;
        }
    }


    /**
     * 增加
     * @param array $param
     * @return bool
     *
     */
    public function addSpike($param){
        $param['spike_state'] = self::SPIKE_STATE_NORMAL;
        return $this->insert($param);
    }

    /**
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     *
     */
    public function editSpike($update, $condition){
        return $this->where($condition)->update($update);
    }

    /**
     * 删除秒杀活动，同时删除秒杀商品
     * @param array $condition
     * @return bool
     *
     */
    public function delSpike($condition){
        $spike_list = $this->getSpikeList($condition);
        $spike_id_string = '';
        if(!empty($spike_list)) {
            foreach ($spike_list as $value) {
                $spike_id_string .= $value['spike_id'] . ',';
            }
        }

        //删除秒杀商品
        if($spike_id_string !== '') {
            $model_spike_goods = Model('p_spike_goods');
            $model_spike_goods->delSpikeGoods(array('spike_id'=>array('in', $spike_id_string)));
        }

        return $this->where($condition)->delete();
    }

    /**
     * 取消秒杀活动，同时取消秒杀商品
     * @param array $condition
     * @return bool
     *
     */
    public function cancelSpike($condition){
        $spike_list = $this->getSpikeList($condition);
        $spike_id_string = '';
        if(!empty($spike_list)) {
            foreach ($spike_list as $value) {
                $spike_id_string .= $value['spike_id'] . ',';
            }
        }

        $update = array();
        $update['spike_state'] = self::SPIKE_STATE_CANCEL;

        //删除秒杀商品
        if($spike_id_string !== '') {
            $model_spike_goods = Model('p_spike_goods');
            $model_spike_goods->editSpikeGoods($update, array('spike_id'=>array('in', $spike_id_string)));
        }

        return $this->editSpike($update, $condition);
    }

    /**
     * 过期修改状态
     */
    public function editExpireSpike($condition = array()) {
        $condition['end_time'] = array('lt', TIMESTAMP);
        $condition['spike_state'] = self::SPIKE_STATE_NORMAL;
        
        $list = $this->table('p_spike')->where($condition)->select();
        if(!empty($list) && is_array($list)) {
            foreach($list as $k => $v) {
                $spike_id = $v['spike_id'];
                // 更新商品促销价格
                $spikegoods_list = Model('p_spike_goods')->getSpikeGoodsList(array('spike_id' => $spike_id));
                if (!empty($spikegoods_list)) {
                    $goodsid_array = array();
                    foreach ($spikegoods_list as $val) {
                        $goodsid_array[] = $val['goods_id'];
                    }
                    // 更新商品促销价格，需要考虑团购是否在进行中
                    QueueClient::push('updateGoodsPromotionPriceByGoodsId', $goodsid_array);
                }
                $updata = array();
                $update['spike_state'] = self::SPIKE_STATE_CLOSE;
                $this->editSpike($update, array('spike_id' => $spike_id));
            }
        }
        return true;
    }

    /**
     * 更新商品推荐信息
     */
    public function updateRecommend($data){
        if (is_array($data)){
            $str = serialize($data);
            $result = $this->table('web_code')->where(array('var_name' => 'spike_list'))->update(array('code_info' => $str));
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 商品推荐
     */
    public function getRecommendList() {
        $info = array();
        $result = $this->table('web_code')->where(array('var_name' => 'spike_list'))->find();
        if (!empty($result)) {
            $code_info = unserialize($result['code_info']);
            if(!is_array($code_info)) $code_info = array();
            if(!empty($code_info) && is_array($code_info)) {
                $goods_ids = array();
                $spike_ids = array();
                foreach($code_info as $k => $v) {
                    $t = $v[0];
                    unset($v[0]);
                    foreach($v as $k2 => $v2) {
                        if ($t == 'goods') $goods_ids[] = $v2;
                        if ($t == 'spike') $spike_ids[] = $v2;
                    }
                }
                $goods_list = $this->table('p_spike_goods')->where(array('spike_goods_id'=> array('in', $goods_ids)))->key('spike_goods_id')->select();
                $spike_list = $this->table('p_spike')->where(array('spike_id'=> array('in', $spike_ids)))->key('spike_id')->select();
                foreach($spike_list as $k => $v) {
                    $_list = $this->table('p_spike_goods')->where(array('spike_id'=> $k))->order('spike_recommend desc,spike_goods_id desc')->limit(3)->select();
                    $spike_list[$k]['goods_list'] = $_list;
                }
                foreach($code_info as $k => $v) {
                    $t = $v[0];
                    unset($v[0]);
                    foreach($v as $k2 => $v2) {
                        if ($t == 'goods') $info[$k]['goods_list'][] = $goods_list[$v2];
                        if ($t == 'spike') $info[$k]['spike_list'][] = $spike_list[$v2];
                    }
                }
            }
        }
        return $info;
    }

    /**
     * 更新头部图片信息
     */
    public function updateRecommendPic($data){
        if (is_array($data)){
            $str = serialize($data);
            $result = $this->table('web_code')->where(array('var_name' => 'spike_pic'))->update(array('code_info' => $str));
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 头部图片
     */
    public function getRecommendPic() {
        $result = $this->table('web_code')->where(array('var_name' => 'spike_pic'))->find();
        return $result;
    }
}