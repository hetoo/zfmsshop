<?php
/**
 * 敏感词
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
class sensitive_wordModel extends Model {
    /**
     * 敏感词列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getWordList($condition = array(), $field = '*', $page = null, $order = 'word_id desc', $limit = '') {
        return $this->table('sensitive_word')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 取单个敏感词的内容
     *
     * @param int $word_id 敏感词ID
     * @return array 数组类型的返回结果
     */
    public function getOneWord($word_id){
        if (intval($word_id) > 0){
            $param = array();
            $param['table'] = 'sensitive_word';
            $param['field'] = 'word_id';
            $param['value'] = intval($word_id);
            $result = $this->getRow1($param);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 新增
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addWord($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $result = $this->insert1('sensitive_word',$tmp);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 更新信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function updateWord($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $where = " word_id = '". $param['word_id'] ."'";
            $result = $this->update1('sensitive_word',$tmp,$where);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 删除
     *
     * @param int $id 记录ID
     * @return array $rs_row 返回数组形式的查询结果
     */
    public function delWord($id){
        if (intval($id) > 0){
            $where = " word_id = '". intval($id) ."'";
            $result = $this->delete1('sensitive_word',$where);
            return $result;
        }else {
            return false;
        }
    }
}
