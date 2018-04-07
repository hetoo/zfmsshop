<?php
/**
 * 分销商品
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class distri_goodsControl extends MemberDistributeControl{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 分销商品列表页
     */
    public function indexOp(){
        $this->goods_listOp();
    }

    public function goods_listOp(){
        $model_goods = Model('dis_goods');
        $condition = array('member_id'=>$_SESSION['member_id']);
        $condition['dis_goods.distri_goods_state'] = 1;
        if(trim($_GET['goods_name']) != ''){
            $condition['goods_common.goods_name|goods_common.goods_jingle'] = array('like', '%' . $_GET['goods_name'] . '%');
        }
        $goods_list = $model_goods->getDistriGoodsCommonList($condition,'*',8);
        self::profile_menu('log','goods_list');
        Tpl::output('goods_list',$goods_list);
        Tpl::output('show_page',$model_goods->showpage(2));
        Tpl::showpage('distri_goods.list');
    }

    /**
     * 删除分销商品
     */
    public function drop_goodsOp(){
        $distri_id = intval($_GET['distri_id']);
        if($distri_id <= 0){
            showMessage('参数错误');
        }
        $model_goods = Model('dis_goods');
        $condition = array('distri_id' => $distri_id);
        $condition['member_id'] = $_SESSION['member_id'];
        $stat = $model_goods->delDistriGoods($condition);
        if($stat){
            showDialog('删除成功','index.php?act=distri_goods','succ');
        }else{
            showDialog('删除失败','index.php?act=distri_goods','error');
        }
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key=''){
        $menu_array = array(
            array('menu_key'=>'goods_list',        'menu_name'=>'分销商品管理',    'menu_url'=>'index.php?act=distri_goods&op=goods_list')
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}