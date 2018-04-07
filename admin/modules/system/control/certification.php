<?php
/**
 * 实名认证管理
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class certificationControl extends SystemControl
{
    const EXPORT_SIZE = 2000;

    function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->memberOp();
    }

    public function memberOp()
    {
        Tpl::showpage('certification.index');
    }
    /**
     * 输出XML数据
     */
    public function get_xmlOp()
    {
        $model_member = Model('member');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('member_id', 'member_name', 'member_email', 'member_mobile','id_card_name','id_card_code');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $condition['id_card_state'] = array('gt', 0);

        $member_list_tmp = $model_member->getMemberList($condition, '*', $page, $order);

        $member_list = array();
        foreach ($member_list_tmp as $value) {
            $member_list[$value['member_id']] = $value;
        }

        $data = array();
        $data['now_page'] = $model_member->shownowpage();
        $data['total_num'] = $model_member->gettotalnum();

        foreach ($member_list as $value) {
            $param = array();
            $operation = '';
            if (in_array($value['id_card_state'], array('1'))) {
                $operation .= "<a class='btn orange' href=\"index.php?act=certification&op=member_info&member_id=" . $value['member_id'] . "\"><i class=\"fa fa-check-circle-o\"></i>审核</a>";
            }
            $operation .= "<a class='btn green' href='index.php?act=certification&op=member_info&member_id=" . $value['member_id'] . "'><i class='fa fa-list-alt'></i>查看</a>";

            $param['operation'] = $operation;
            $param['member_id'] = $value['member_id'];
            $param['member_name'] = "<img src=" . getMemberAvatarForID($value['member_id']) . " class='user-avatar' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=" . getMemberAvatarForID($value['member_id']) . ">\")'>" . $value['member_name'];
            $param['id_card_state'] = str_replace(array('1', '2', '3'), array('待审核', '已通过', '未通过'), $value['id_card_state']);
            $param['member_mobile'] = $value['member_mobile'];
            $param['member_email'] = $value['member_email'];
            $param['id_card_name'] = $value['id_card_name'];
            $param['id_card_code'] = $value['id_card_code'];

            $data['list'][$value['member_id']] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    /**
     * 认证详情
     */
    public function member_infoOp()
    {
        $member_id = intval($_REQUEST['member_id']);
        if ($member_id <= 0) {
            showMessage('会员不存在', 'index.php?act=distri_member');
        }
        $member_model = Model('member');
        $member_info = $member_model->getMemberInfoByID($member_id);

        if(!empty($member_info['id_card_img'])){
            $data = explode(',', $member_info['id_card_img']);
            Tpl::output('id_card_img1', $data[0]);
            Tpl::output('id_card_img2', $data[1]);
        }

        Tpl::output('member_info', $member_info);
        Tpl::showpage('certification.info');
    }

    /**
     * 会员认证
     */
    public function authOp()
    {
        if (!empty($_POST)) {
            $param = array();
            $member_model = Model('member');
            $param['id_card_explain'] = trim($_POST['message']);
            if($_POST['verify_type'] == 'pass'){
                $id_card_state = 2;
                $id_card_bind = 1;
                $member_info = $member_model->getMemberInfoByID(intval($_POST['member_id']));
                $param['member_truename'] = $member_info['id_card_name'];
            }else{
                $id_card_state = 3;
                $id_card_bind = 0;
            }
            $param['id_card_state'] = $id_card_state;
            $param['id_card_bind'] = $id_card_bind;

            $member_id = intval($_POST['member_id']);

            $stat = $member_model->editMember(array('member_id' => $member_id), $param);
            if ($stat) {
                showMessage('认证处理成功', 'index.php?act=certification');
            } else {
                showMessage('认证处理失败', 'index.php?act=certification&op=member_info&member_id=' . $member_id);
            }
        } else {
            showMessage('非法请求', 'index.php?act=certification');
        }
    }


    /**
     * csv导出
     */
    public function export_csvOp()
    {
        $model_member = Model('member');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['member_id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        $order = '';
        $param = array('member_id', 'member_name', 'member_email', 'member_mobile','id_card_name','id_card_code');
        if (in_array($_GET['sortname'], $param) && in_array($_GET['sortorder'], array('asc', 'desc'))) {
            $order = $_GET['sortname'] . ' ' . $_GET['sortorder'];
        }
        $condition['id_card_state'] = array('gt', 0);

        if (!is_numeric($_GET['curpage'])) {
            $count = $model_member->getMemberCount($condition);
            if ($count > self::EXPORT_SIZE) {   //显示下载链接
                $array = array();
                $page = ceil($count / self::EXPORT_SIZE);
                for ($i = 1; $i <= $page; $i++) {
                    $limit1 = ($i - 1) * self::EXPORT_SIZE + 1;
                    $limit2 = $i * self::EXPORT_SIZE > $count ? $count : $i * self::EXPORT_SIZE;
                    $array[$i] = $limit1 . ' ~ ' . $limit2;
                }
                Tpl::output('list', $array);
                Tpl::output('murl', 'index.php?act=member&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage'] - 1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 . ',' . $limit2;
        }

        $member_list_tmp = $model_member->getMemberList($condition, '*', null, $order, $limit);
        $member_list = array();
        foreach ($member_list_tmp as $value) {
            $member_list[$value['member_id']] = $value;
        }

        $this->createCsv($member_list);
    }

    /**
     * 生成csv文件
     */
    private function createCsv($member_list)
    {
        $data = array();
        foreach ($member_list as $value) {
            $param = array();
            $param['member_id'] = $value['member_id'];
            $param['member_name'] = $value['member_name'];
            $param['id_card_state'] = str_replace(array('1', '2', '3'), array('待审核', '已通过', '未通过'), $value['id_card_state']);
            $param['member_mobile'] = $value['member_mobile'];
            $param['member_email'] = $value['member_email'];
            $param['id_card_name'] = $value['id_card_name'];
            $param['id_card_code'] = $value['id_card_code'];
            $data[$value['member_id']] = $param;
        }
        $header = array('member_id' => '会员ID', 'member_name' => '会员名称', 'id_card_state' => '申请状态', 'member_email' => '会员邮箱', 'member_mobile' => '会员手机', 'id_card_name' => '真实姓名', 'id_card_code' => '身份证号');

        \Shopnc\Lib::exporter()->output('member_certification_list' . $_GET['curpage'] . '-' . date('Y-m-d'), $data, $header);
    }
}