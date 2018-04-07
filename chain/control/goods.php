<?php
/**
 * 物流自提服务站首页
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class goodsControl extends BaseChainCenterControl{
    public function __construct(){
        parent::__construct();
    }
    
    public function indexOp() {
        $model_goods = Model('goods');
        $where = array();
        $where['store_id'] = $_SESSION['chain_store_id'];
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case 0:
                    $where['goods_name'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 1:
                    $where['goods_serial'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 2:
                    $where['goods_commonid'] = intval($_GET['keyword']);
                    break;
            }
        }
        
        $goods_list = $model_goods->getGeneralGoodsCommonList($where, '*', 10);
        $stock_list = array();
        if (!empty($goods_list)) {
            $commonid_array = array();
            foreach ($goods_list as $val) {
                $commonid_array[] = $val['goods_commonid'];
            }
            $goodsid_array = $model_goods->getGoodsOnlineList(array('goods_commonid' => array('in', $commonid_array)), 'min(goods_id) goods_id,goods_commonid', 0, 'goods_id desc', 0, 'goods_commonid');
            $goodsid_array = array_under_reset($goodsid_array, 'goods_commonid');
            Tpl::output('goodsid_array', $goodsid_array);
            $stock_array = Model('chain_stock')->getChainStockList(array('chain_id' => $_SESSION['chain_id'], 'goods_commonid' => array('in', $commonid_array)));
            if (!empty($stock_array)) {
                foreach ($stock_array as $val) {
                    if (!isset($stock_list[$val['goods_commonid']])) {
                        $stock_list[$val['goods_commonid']]['stock'] = 0;
                    }
                    $stock_list[$val['goods_commonid']]['stock'] += intval($val['stock']);
                    $stock_list[$val['goods_commonid']]['goods_salenum'] += intval($val['goods_salenum']);
                    if (empty($stock_list[$val['goods_commonid']]['goods_id'])) {
                        $stock_list[$val['goods_commonid']]['goods_id'] = $val['goods_id'];
                        $stock_list[$val['goods_commonid']]['chain_price'] = $val['chain_price'];
                    }
                }
            }
        }
        Tpl::output('stock_list', $stock_list);
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::output('goods_list', $goods_list);
        
        $this->profile_menu('goods_list', 'goods_list');
        Tpl::showpage('goods.list');
    }
    
    /**
     * 设置库存
     */
    public function set_stockOp() {
        $model_chain_stock = Model('chain_stock');
        if (chksubmit()) {
            $goods_commonid   = intval($_POST['goods_commonid']);
            $p_cate_id = intval($_POST['p_cate_id']);
            $cate_id = intval($_POST['cate_id']);
            $stock_info = $model_chain_stock->getChainStockList(array('chain_id' => $_SESSION['chain_id'], 'goods_commonid' => $goods_commonid));
            $stock_info = array_under_reset($stock_info, 'goods_id');
            foreach ($_POST['stock'] as $key => $val) {
                $insert = array();
                $insert['chain_id']         = $_SESSION['chain_id'];
                $insert['goods_id']         = intval($key);
                $insert['goods_commonid']   = $goods_commonid;
                $insert['stock']            = intval($val);
                $insert['chain_price']            = floatval($_POST['chain_price'][$key]);
                $insert['cate_id'] = $cate_id;
                $insert['p_cate_id'] = $p_cate_id;
                if (!empty($stock_info[$insert['goods_id']])) {
                    $model_chain_stock->editChainStock($insert, array('chain_id' => $_SESSION['chain_id'], 'goods_id' => $insert['goods_id']));
                }else{
                    $model_chain_stock->addChainStock($insert);
                }
            }
            showDialog('操作成功', 'reload', 'succ');
        }
        
        $common_id = intval($_GET['common_id']);
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodsCommonInfoByID($common_id);
        if ($goodscommon_info['store_id'] != $_SESSION['chain_store_id']) {
            Tpl::output('error', true);
        }
        Tpl::output('goodscommon_info', $goodscommon_info);
        $spec_name = array_values((array)unserialize($goodscommon_info['spec_name']));
        Tpl::output('spec_name', $spec_name);
        $stock_info = $model_chain_stock->getChainStockList(array('chain_id' => $_SESSION['chain_id'], 'goods_commonid' => $common_id));
        Tpl::output('p_cate_id',$stock_info[0]['p_cate_id']);
        Tpl::output('cate_id',$stock_info[0]['cate_id']);
        $stock_info = array_under_reset($stock_info, 'goods_id');
        $goods_info = $model_goods->getGeneralGoodsOnlineList(array('goods_commonid' => $common_id), 'goods_id,goods_spec,goods_serial,goods_price');
        $goods_array = array();
        if (!empty($goods_info)) {
            foreach ($goods_info as $val) {
                $goods_spec = array_values((array)unserialize($val['goods_spec']));
                $goods_array[$val['goods_id']]['goods_spec'] = $goods_spec;
                $goods_array[$val['goods_id']]['goods_serial'] = $val['goods_serial'];
                $goods_array[$val['goods_id']]['goods_price'] = $val['goods_price'];
                if (empty($stock_info[$val['goods_id']]['chain_price'])) $stock_info[$val['goods_id']]['chain_price'] = $val['goods_price'];
            }
        }

        //获取商家顶级分类列表
        $model_class = Model('chain_goods_class');
        $condition = array();
        $condition['class_parent_id'] = 0;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $class_list = $model_class->getChainGoodsClassList($condition);
        Tpl::output('class_list',$class_list);

        Tpl::output('goods_array', $goods_array);
        Tpl::output('stock_info', $stock_info);
        Tpl::showpage('goods.set_stock', 'null_layout');
    }

    /**
     * 商品分类列表
     */
    public function goods_classOp(){
        $model_class = Model('chain_goods_class');
        $condition = array();
        $condition['class_parent_id'] = 0;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $class_list = $model_class->getChainGoodsClassList($condition);

        Tpl::output('class_list',$class_list);
        $this->profile_menu('goods_list', 'class_list');

        Tpl::showpage('goods_class.list');
    }

    /**
     * 添加分类
     */
    public function add_classOp(){
        $model_class = Model('chain_goods_class');
        $condition = array();
        $condition['class_parent_id'] = 0;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $class_list = $model_class->getChainGoodsClassList($condition);
        Tpl::output('class_list',$class_list);
        Tpl::showpage('goods_class.add','null_layout');
    }

    /**
     * 编辑分类
     */
    public function edit_classOp(){
        $model_class = Model('chain_goods_class');
        $class_id = intval($_REQUEST['class_id']);
        if($class_id <= 0){
            Tpl::output('error', true);
        }
        $condition = array();
        $condition['chain_id'] = $_SESSION['chain_id'];
        $condition['class_id'] = $class_id;
        $class_info = $model_class->getChainGoodsClassInfo($condition);
        Tpl::output('class_info',$class_info);

        $condition = array();
        $condition['class_parent_id'] = 0;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $class_list = $model_class->getChainGoodsClassList($condition);
        Tpl::output('class_list',$class_list);

        Tpl::showpage('goods_class.add','null_layout');
    }

    /**
     * 保存分类信息
     */
    public function save_classOp(){
        if(chksubmit()){
            $class_id = intval($_POST['class_id']);
            $param = array();
            $param['class_name'] = trim($_POST['class_name']);
            $param['class_parent_id'] = intval($_POST['class_parent_id']);
            $param['class_state'] = intval($_POST['class_state']);
            $param['class_sort'] = intval($_POST['class_sort']);
            $model_class = Model('chain_goods_class');
            if($class_id > 0){
                $condition = array();
                $condition['class_id'] = $class_id;
                $result = $model_class->editChainGoodsClass($param, $condition);
            }else{
                $param['chain_id'] = $_SESSION['chain_id'];
                $result = $model_class->addChainGoodsClass($param);
            }
            if($result){
                showDialog('操作成功', 'reload', 'succ');
            }else{
                showDialog('保存失败', 'reload', 'error');
            }
        }else{
            showDialog('保存失败', 'reload', 'error');
        }
    }

    /**
     * 删除分类
     */
    public function del_classOp(){
        $class_id = intval($_REQUEST['class_id']);
        if($class_id <= 0){
            showDialog('参数错误', 'reload', 'error');
        }
        $condition = array();
        $condition['class_id'] = $class_id;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $model_class = Model('chain_goods_class');
        $result = $model_class->delChainGoodsClass($condition);
        if($result){
            showDialog('操作成功', 'reload', 'succ');
        }else{
            showDialog('操作失败', 'reload', 'error');
        }
    }

    /**
     * 获取下级分类
     */
    public function child_classOp(){
        $class_id = intval($_REQUEST['class_id']);
        if($class_id <= 0){
            showDialog('参数错误', 'reload', 'error');
        }
        $type = trim($_REQUEST['type']);
        $condition = array();
        $condition['class_parent_id'] = $class_id;
        $condition['chain_id'] = $_SESSION['chain_id'];
        $model_class = Model('chain_goods_class');
        $class_list = (array)$model_class->getChainGoodsClassList($condition);
        if($type == 'json'){
            echo json_encode($class_list);exit;
        }
        Tpl::output('class_list',$class_list);
        Tpl::showpage('goods_class.list_child','null_layout');
    }




    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key) {
        $menu_array = array();
        switch ($menu_type) {
            case 'goods_list':
                $menu_array = array(
                array('menu_key' => 'goods_list',    'menu_name' => '商品列表', 'menu_url' => urlChain('goods', 'index')),
                array('menu_key' => 'class_list',    'menu_name' => '商品分类列表', 'menu_url' => urlChain('goods', 'goods_class')),
                );
                break;
        }
        Tpl::output ( 'chain_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }
}
