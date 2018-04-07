<?php
/**
 * 门店商品类别模型
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

class chain_goods_classModel extends Model {
    public function __construct(){
        parent::__construct('chain_goods_class');
    }
    /**
     * 单个类别内容提取
     *
     * @param array $param 检索条件
     * @return array 数组结构的返回结果
     */
    public function getChainGoodsClassInfo($param,$field='*') {
        if(empty($param)) {
            return false;
        }
        return $this->where($param)->field($field)->find();
    }
    /**
     * 类别添加
     *
     * @param array $param 分类数组
     * @return array 数组结构的返回结果
     */
    public function addChainGoodsClass($param) {
        if(empty($param)) {
            return false;
        }
        $result = $this->insert($param);
        return $result;
    }
    /**
     * 类别修改
     *
     * @param array $param 分类数组
     * @return array 数组结构的返回结果
     */
    public function editChainGoodsClass($param,$where) {
        if(empty($param)) {
            return false;
        }
        $result = $this->where($where)->update($param);
        return $result;
    }
    /**
     * 店铺商品分类删除
     *
     */
    public function delChainGoodsClass($where) {
        if(empty($where)) {
            return false;
        }
        $result = $this->where($where)->delete();
        return $result;
    }
    /**
     * 类别列表
     *
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function getChainGoodsClassList($condition, $order = 'class_parent_id asc,class_sort asc,class_id asc'){
        $result = $this->where($condition)->order($order)->select();
        return $result;
    }

    /**
     * 取分类列表 用于mobile
     *
     * $param int $storeId 店铺ID
     * @return array 数组类型的返回结果
     */
    public function getChainGoodsClassPlainList($chainId)
    {
        $data = array();
        $goods_class_list = (array) $this->getShowTreeList($chainId);

        foreach ($goods_class_list as $v) {
            $data[] = array(
                'id' => $v['class_id'],
                'name' => $v['class_name'],
                'level' => 1,
                'pid' => 0,
            );

            foreach ((array) $v['children'] as $vv) {
                $data[] = array(
                    'id' => $vv['class_id'],
                    'name' => $vv['class_name'],
                    'level' => 2,
                    'pid' => $v['class_id'],
                );
            }
        }

        return $data;
    }

    /**
     * 取分类列表(前台店铺页左侧用到)
     *
     * $param int $chain_id 店铺ID
     * @return array 数组类型的返回结果
     */
    public function getShowTreeList($chain_id){
        $show_class = array();
        $class_list = $this->getChainGoodsClassList (array('chain_id'=>$chain_id,'class_state'=>'1'));
        if(is_array($class_list) && !empty($class_list)) {
            foreach ($class_list as $val) {
                if($val['class_parent_id'] == 0) {
                    $show_class[$val['class_id']] = $val;
                } else {
                    if(isset($show_class[$val['class_parent_id']])){
                        $show_class[$val['class_parent_id']]['children'][] = $val;
                    }
                }
            }
        }
        return $show_class;
    }
    /**
     * 取分类列表，按照深度归类
     *
     * @param array $condition 检索条件
     * @param int $show_deep 显示深度
     * @return array 数组类型的返回结果
     */
    public function getTreeClassList($condition,$show_deep='2'){
        $class_list = $this->getChainGoodsClassList ($condition);
        $show_deep = intval($show_deep);
        $result = array();
        if(is_array($class_list) && !empty($class_list)) {
            $result = $this->_getTreeClassList($show_deep,$class_list);
        }
        return $result;
    }
    /**
     * 递归 整理分类
     *
     * @param int $show_deep 显示深度
     * @param array $class_list 类别内容集合
     * @param int $deep 深度
     * @param int $parent_id 父类编号
     * @param int $i 上次循环编号
     * @return array $show_class 返回数组形式的查询结果
     */
    private function _getTreeClassList($show_deep,$class_list,$deep=1,$parent_id=0,$i=0){
        static $show_class = array();//树状的平行数组
        if(is_array($class_list) && !empty($class_list)) {
            $size = count($class_list);
            if($i == 0) $show_class = array();//从0开始时清空数组，防止多次调用后出现重复
            for ($i;$i < $size;$i++) {//$i为上次循环到的分类编号，避免重新从第一条开始
                $val = $class_list[$i];
                $stc_id = $val['class_id'];
                $stc_parent_id  = $val['class_parent_id'];
                if($stc_parent_id == $parent_id) {
                    $val['deep'] = $deep;
                    $show_class[] = $val;
                    if($deep < $show_deep && $deep < 2) {//本次深度小于显示深度时执行，避免取出的数据无用
                        $this->_getTreeClassList($show_deep,$class_list,$deep+1,$stc_id,$i+1);
                    }
                }
                if($stc_parent_id > $parent_id) break;//当前分类的父编号大于本次递归的时退出循环
            }
        }
        return $show_class;
    }
    /**
     * 取分类列表
     *
     * @param array $condition 检索条件
     * @return array 数组类型的返回结果
     */
    public function getClassTree($condition){
        $class_list = $this->getChainGoodsClassList ($condition);
        $d = array();
        if (is_array($class_list)){
            foreach($class_list as $v) {
                if($v['class_parent_id'] == 0) {
                    $d[$v['class_id']] = $v;
                }else {
                    if(isset($d[$v['class_parent_id']])) $d[$v['class_parent_id']]['child'][] = $v;//防止出现父类不显示时子类被调出
                }
            }
        }
        return $d;
    }
}
