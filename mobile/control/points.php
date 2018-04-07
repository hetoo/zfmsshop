<?php
/**
 * 积分中心
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
class pointsControl extends mobileHomeControl
{
    private $member_info = array();
    function __construct()
    {
        parent::__construct();
        $key = $_POST['key'];
        if(empty($key)) {
            $key = $_GET['key'];
        }
        if($key != ''){
            $this->member_info = $this->getMemberAndGradeInfo();
        }
    }

    /**
     * 积分中心首页
     */
    public function indexOp(){
        $data = array();
        $data['member_info'] = (object)array();
        $data['voucher'] = (object)array();
        $data['pointsprod'] = (object)array();
        $data['redpacket'] = (object)array();

        //开启代金券功能后查询推荐的热门代金券列表
        if (C('voucher_allow') == 1){
            $recommend_voucher = Model('voucher')->getRecommendTemplate(6);
            foreach ($recommend_voucher as $key=>$item) {
                $recommend_voucher[$key]['voucher_t_end_date'] = date('Y-m-d',$item['voucher_t_end_date']);
            }
            $data['voucher'] = $recommend_voucher;
        }

        //开启积分兑换功能后查询推荐的热门兑换商品列表
        if (C('pointprod_isuse') == 1){
            $recommend_pointsprod = Model('pointprod')->getRecommendPointProd(10);
            $data['pointsprod'] = $recommend_pointsprod;
        }

        //开启平台红包功能后查询推荐的红包列表
        if (C('redpacket_allow') == 1){
            $recommend_rpt = Model('redpacket')->getRecommendRpt(9);
            foreach ($recommend_rpt as $key=>$item) {
                $recommend_rpt[$key]['rpacket_t_end_date'] = date('Y-m-d',$item['rpacket_t_end_date']);
            }
            $data['redpacket'] = $recommend_rpt;
        }
        if(!empty($this->member_info)){
            $member_info = $this->member_infoOp(true);
            $data = array_merge($data,$member_info);
        }
        output_data($data);
    }

    //获取用户信息
    public function member_infoOp($is_return = false){
        $member_info = array();
        $member_info['member_id'] = $this->member_info['member_id'];
        $member_info['user_name'] = $this->member_info['member_name'];
        $member_info['avatar'] = getMemberAvatarForID($this->member_info['member_id']);

        $member_gradeinfo = Model('member')->getOneMemberGrade(intval($this->member_info['member_exppoints']));
        $member_info['level'] = $member_gradeinfo['level'];
        $member_info['level_name'] = $member_gradeinfo['level_name'];
        $member_info['member_points'] = $this->member_info['member_points'];
        $member_info['member_exppoints'] = intval($this->member_info['member_exppoints']);

        //查询已兑换并可以使用的代金券数量
        $vouchercount = Model('voucher')->getCurrentAvailableVoucherCount($this->member_info['member_id']);

        //购物车兑换商品数
        $pointcart_count = Model('pointcart')->countPointCart($this->member_info['member_id']);

        //查询已兑换商品数(未取消订单)
        $pointordercount = Model('pointorder')->getMemberPointsOrderGoodsCount($this->member_info['member_id']);

        //查询已兑换并可用的红包数量
        $redpacketcount = Model('redpacket')->getCurrentAvailableRedpacketCount($this->member_info['member_id']);

        $return = array('member_info'=>$member_info,'vouchercount'=>$vouchercount,'pointcart_count'=>$pointcart_count,'pointordercount'=>$pointordercount,'redpacketcount'=>$redpacketcount);
        if($is_return){
            return $return;
        }else{
            output_data($return);
        }
    }

    /**
     * 代金券列表
     */
    public function exchange_voucherOp(){
        if (C('voucher_allow') != 1){
            output_error('系统未开启代金券功能');
        }
        $model_voucher = Model('voucher');

        //代金券模板状态
        $templatestate_arr = $model_voucher->getTemplateState();

        //查询代金券列表
        $where = array();
        $gettype_arr = $model_voucher->getVoucherGettypeArray();
        $where['voucher_t_gettype'] = $gettype_arr['points']['sign'];
        $where['voucher_t_state'] = $templatestate_arr['usable'][0];
        $where['voucher_t_end_date'] = array('gt',time());

        //仅我能兑换的会员级别
        if (intval($_GET['isable']) == 1 && !empty($this->member_info)){
            $model_member = Model('member');
            $member_currgrade = $model_member->getOneMemberGrade(intval($this->member_info['member_exppoints']));
            $where['voucher_t_mgradelimit'] = array('elt',intval($member_currgrade['level']));
        }
        $orderby = 'voucher_t_id desc';
        $voucherlist = $model_voucher->getVoucherTemplateList($where, '*', 0, $this->page, $orderby);
        foreach ($voucherlist as $key => $value) {
            $voucherlist[$key]['end_date'] = date('Y-m-d',$value['voucher_t_end_date']);
        }

        $page_count = $model_voucher->gettotalpage();
        output_data(array('voucherlist' => $voucherlist), mobile_page($page_count));
    }

    /**
     * 红包列表
     */
    public function exchange_redpacketOp(){
        if (C('redpacket_allow') != 1){
            output_error('系统未开启红包功能');
        }

        $model_redpacket = Model('redpacket');
        //模板状态
        $templatestate_arr = $model_redpacket->getTemplateState();
        //领取方式
        $gettype_arr = $model_redpacket->getGettypeArr();

        //查询红包列表
        $where = array();
        $where['rpacket_t_gettype']     = $gettype_arr['points']['sign'];
        $where['rpacket_t_state']       = $templatestate_arr['usable']['sign'];
        $where['rpacket_t_end_date']    = array('egt',time());

        //仅我能兑换的会员级别
        if (intval($_GET['isable']) == 1 && !empty($this->member_info)){
            $model_member = Model('member');
            $member_currgrade = $model_member->getOneMemberGrade(intval($this->member_info['member_exppoints']));
            $where['rpacket_t_mgradelimit'] = array('elt',intval($member_currgrade['level']));
        }

        $orderby = 'rpacket_t_id desc';
        $rptlist = $model_redpacket->getRptTemplateList($where, '*', 0, $this->page, $orderby);

        foreach ($rptlist as $key => $value) {
            $rptlist[$key]['end_date'] = date('Y-m-d',$value['rpacket_t_end_date']);
        }

        $page_count = $model_redpacket->gettotalpage();
        output_data(array('rptlist' => $rptlist), mobile_page($page_count));
    }

    /**
     * 积分商品列表
     */
    public function exchange_pgoodsOp(){
        if (C('pointprod_isuse') != 1){
            output_error('系统未开启积分兑换功能');
        }

        $model_pointprod = Model('pointprod');

        //展示状态
        $pgoodsshowstate_arr = $model_pointprod->getPgoodsShowState();
        //开启状态
        $pgoodsopenstate_arr = $model_pointprod->getPgoodsOpenState();

        //查询兑换商品列表
        $where = array();
        $where['pgoods_show'] = $pgoodsshowstate_arr['show'][0];
        $where['pgoods_state'] = $pgoodsopenstate_arr['open'][0];

        $orderby = 'pgoods_sort asc,pgoods_id desc';

        $pointprod_list = $model_pointprod->getPointProdList($where, '*', $orderby,'',$this->page);

        $page_count = $model_pointprod->gettotalpage();
        output_data(array('pointprod_list' => $pointprod_list), mobile_page($page_count));
    }

    /**
     * 积分礼品详情
     */
    public function pgoods_detialOp(){
        $pgoods_id = intval($_GET['pgoods_id']);
        if($pgoods_id <= 0){
            output_error('参数错误');
        }
        //获取会员信息
        $member_info = $this->member_infoOp(true);

        //获取兑换商品信息
        $model_pgoods = Model('pointprod');
        $pgoods_info = $model_pgoods->getPointProdInfo(array('pgoods_id'=>$pgoods_id));
        $pgoods_info['pgoods_add_time'] = date('Y-m-d',$pgoods_info['pgoods_add_time']);

        $return_data = array();
        $return_data['pgoods_info'] = $pgoods_info;
        $return_data = array_merge($return_data,$member_info);
        output_data($return_data);
    }

    /**
     * 礼品兑换记录列表
     */
    public function pgoods_orderOp(){
        $pgoods_id = intval($_GET['pgoods_id']);
        if($pgoods_id <= 0){
            output_error('参数错误');
        }
        //获取兑换记录
        $model_pointprod = Model();
        $page = $this->page;
        $limit = $_GET['curpage'] * $page.','.$page;
        $field = "points_ordergoods.point_goodsnum,points_order.point_addtime,member.member_id,member.member_name,member.member_avatar";
        $order_list = $model_pointprod->table('points_ordergoods,points_order,member')->join('LEFT')->on('points_ordergoods.point_orderid=points_order.point_orderid,points_order.point_buyerid=member.member_id')->field($field)->where(array('points_ordergoods.point_goodsid'=>$pgoods_id))->page($page)->limit($limit)->select();
        foreach($order_list as $k=>$v){
            $tmp = array();
            $tmp['member_avatar'] = getMemberAvatarForID($v['member_id']);
            $tmp['member_name'] = substr($v['member_name'],0,3).'***'.substr($v['member_name'],-3);
            $tmp['point_addtime'] = date('Y-m-d',$v['point_addtime']);
            $tmp['point_goodsnum'] = $v['point_goodsnum'];
            $order_list[$k] = $tmp;
        }
        $page_count = $model_pointprod->gettotalpage();
        output_data(array('order_list'=>$order_list), mobile_page($page_count));
    }
}