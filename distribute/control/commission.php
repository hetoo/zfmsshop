<?php
/**
 * 分销余额管理
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

class commissionControl extends MemberDistributeControl{

    public function indexOp(){
        $this->commission_infoOp();
    }

    /**
     * 账户余额
     */
    public function commission_infoOp(){
        $model_tard = Model('dis_trad');
        $condition = array();
        $condition['lg_member_id'] = $_SESSION['member_id'];
        $list = $model_tard->getDistriTradList($condition, '*' , 20);

        //信息输出
        self::profile_menu('log','commission_info');
        Tpl::output('show_page',$model_tard->showpage(2));
        Tpl::output('list',$list);
        Tpl::showpage('commission_info');
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
            array('menu_key'=>'commission_info',        'menu_name'=>'账户余额',    'menu_url'=>'index.php?act=commission&op=commission_info')
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}