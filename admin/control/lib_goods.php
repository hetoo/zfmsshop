<?php
/**
 * 商品库
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class lib_goodsControl{

    /**
     * 验证商品是否重复
     */
    public function check_nameOp() {
        $condition = array();
        $condition['goods_name'] = $_GET['g_name'];
        $condition['goods_id'] = array('neq',intval($_GET['goods_id']));
        $model_goods = Model('lib_goods');
        $goods = $model_goods->getGoodsInfo($condition);
        if (empty($goods)) {
            echo 'true';exit;
        } else {
            echo 'false';exit;
        }
    }

    /**
     * ajax获取商品分类的子级数据
     */
    public function ajax_goods_classOp() {
        $gc_id = intval($_GET['gc_id']);
        $model_goodsclass = Model('goods_class');
        $list = $model_goodsclass->getGoodsClassListByParentId($gc_id);
        if (empty($list)) {
            exit();
        }
        echo json_encode($list);
    }
    /**
     * ajax选择常用商品分类
     */
    public function ajax_show_commOp() {
        $staple_id = intval($_GET['stapleid']);

        /**
         * 查询相应的商品分类id
         */
        $model_staple = Model('goods_class_staple');
        $staple_info = $model_staple->getStapleInfo(array('staple_id' => intval($staple_id)), 'gc_id_1,gc_id_2,gc_id_3');
        if (empty ( $staple_info ) || ! is_array ( $staple_info )) {
            echo json_encode ( array (
                    'done' => false,
                    'msg' => ''
            ) );
            die ();
        }

        $list_array = array ();
        $list_array['gc_id'] = 0;
        $list_array['type_id'] = $staple_info['type_id'];
        $list_array['done'] = true;
        $list_array['one'] = '';
        $list_array['two'] = '';
        $list_array['three'] = '';

        $gc_id_1 = intval ( $staple_info['gc_id_1'] );
        $gc_id_2 = intval ( $staple_info['gc_id_2'] );
        $gc_id_3 = intval ( $staple_info['gc_id_3'] );

        /**
         * 查询同级分类列表
         */
        $model_goods_class = Model ( 'goods_class' );
        // 1级
        if ($gc_id_1 > 0) {
            $list_array['gc_id'] = $gc_id_1;
            $class_list = $model_goods_class->getGoodsClassListByParentId(0);
            if (empty ( $class_list ) || ! is_array ( $class_list )) {
                echo json_encode ( array (
                        'done' => false,
                        'msg' => ''
                ) );
                die ();
            }
            foreach ( $class_list as $val ) {
                if ($val ['gc_id'] == $gc_id_1) {
                    $list_array ['one'] .= '<li class="" onclick="selClass($(this));" data-param="{gcid:' . $val ['gc_id'] . ', deep:1, tid:' . $val ['type_id'] . '}" nctype="selClass"> <a class="classDivClick" href="javascript:void(0)"><span class="has_leaf"><i class="icon-double-angle-right"></i>' . $val ['gc_name'] . '</span></a> </li>';
                } else {
                    $list_array ['one'] .= '<li class="" onclick="selClass($(this));" data-param="{gcid:' . $val ['gc_id'] . ', deep:1, tid:' . $val ['type_id'] . '}" nctype="selClass"> <a class="" href="javascript:void(0)"><span class="has_leaf"><i class="icon-double-angle-right"></i>' . $val ['gc_name'] . '</span></a> </li>';
                }
            }
        }
        // 2级
        if ($gc_id_2 > 0) {
            $list_array['gc_id'] = $gc_id_2;
            $class_list = $model_goods_class->getGoodsClassListByParentId($gc_id_1);
            if (empty ( $class_list ) || ! is_array ( $class_list )) {
                echo json_encode ( array (
                        'done' => false,
                        'msg' => ''
                ) );
                die ();
            }
            foreach ( $class_list as $val ) {
                if ($val ['gc_id'] == $gc_id_2) {
                    $list_array ['two'] .= '<li class="" onclick="selClass($(this));" data-param="{gcid:' . $val ['gc_id'] . ', deep:2, tid:' . $val ['type_id'] . '}" nctype="selClass"> <a class="classDivClick" href="javascript:void(0)"><span class="has_leaf"><i class="icon-double-angle-right"></i>' . $val ['gc_name'] . '</span></a> </li>';
                } else {
                    $list_array ['two'] .= '<li class="" onclick="selClass($(this));" data-param="{gcid:' . $val ['gc_id'] . ', deep:2, tid:' . $val ['type_id'] . '}" nctype="selClass"> <a class="" href="javascript:void(0)"><span class="has_leaf"><i class="icon-double-angle-right"></i>' . $val ['gc_name'] . '</span></a> </li>';
                }
            }
        }
        // 3级
        if ($gc_id_3 > 0) {
            $list_array['gc_id'] = $gc_id_3;
            $class_list = $model_goods_class->getGoodsClassListByParentId($gc_id_2);
            if (empty ( $class_list ) || ! is_array ( $class_list )) {
                echo json_encode ( array (
                        'done' => false,
                        'msg' => ''
                ) );
                die ();
            }
            foreach ( $class_list as $val ) {
                if ($val ['gc_id'] == $gc_id_3) {
                    $list_array ['three'] .= '<li class="" onclick="selClass($(this));" data-param="{gcid:' . $val ['gc_id'] . ', deep:3, tid:' . $val ['type_id'] . '}" nctype="selClass"> <a class="classDivClick" href="javascript:void(0)"><span class="has_leaf"><i class="icon-double-angle-right"></i>' . $val ['gc_name'] . '</span></a> </li>';
                } else {
                    $list_array ['three'] .= '<li class="" onclick="selClass($(this));" data-param="{gcid:' . $val ['gc_id'] . ', deep:3, tid:' . $val ['type_id'] . '}" nctype="selClass"> <a class="" href="javascript:void(0)"><span class="has_leaf"><i class="icon-double-angle-right"></i>' . $val ['gc_name'] . '</span></a> </li>';
                }
            }
        }
        echo json_encode ( $list_array );
        die();
    }

    /**
     * 上传图片
     */
    public function image_uploadOp() {
        $logic_goods = Logic('goods');
        $result =  $logic_goods->uploadGoodsImage($_POST['name'],0,0);
        if(!$result['state']) {
            echo json_encode(array('error' => $result['msg']));die;
        }
        echo json_encode($result['data']);die;
    }

    /**
     * 上传视频
     */
    public function video_uploadOp() {
        $logic_goods = Logic('goods');
        $result =  $logic_goods->uploadGoodsVideo($_POST['name'],0,0,10240);
        if(!$result['state']) {
            echo json_encode(array('error' => $result['msg']));die;
        }
        echo json_encode($result['data']);die;
    }

    /**
     * AJAX查询品牌
     */
    public function ajax_get_brandOp() {
        $type_id = intval($_GET['tid']);
        $initial = trim($_GET['letter']);
        $keyword = trim($_GET['keyword']);
        $type = trim($_GET['type']);
        if (!in_array($type, array('letter', 'keyword')) || ($type == 'letter' && empty($initial)) || ($type == 'keyword' && empty($keyword))) {
            echo json_encode(array());die();
        }

        // 实例化模型
        $model_type = Model('type');
        $where = array();
        $where['type_id'] = $type_id;
        // 验证类型是否关联品牌
        $count = $model_type->getTypeBrandCount($where);
        if ($type == 'letter') {
            switch ($initial) {
                case 'all':
                    break;
                case '0-9':
                    $where['brand_initial'] = array('in', array(0,1,2,3,4,5,6,7,8,9));
                    break;
                default:
                    $where['brand_initial'] = $initial;
                    break;
            }
        } else {
            $where['brand_name|brand_initial'] = array('like', '%' . $keyword . '%');
        }
        if ($count > 0) {
            $brand_array = $model_type->typeRelatedJoinList($where, 'brand', 'brand.brand_id,brand.brand_name,brand.brand_initial');
        } else {
            unset($where['type_id']);
            $brand_array = Model('brand')->getBrandPassedList($where, 'brand_id,brand_name,brand_initial', 0, 'brand_initial asc, brand_sort asc');
        }
        echo json_encode($brand_array);die();
    }
    /**
     * 图片列表
     */
    public function pic_listOp(){

        /**
         * 分页类
         */
        $page   = new Page();
        $page->setEachNum(14);
        $page->setStyle('admin');
        /**
         * 实例化相册类
         */
        $model_album = Model('album');
        /**
         * 图片列表
         */
        $param = array();
        $param['album_pic.store_id']    = '0';
        $pic_list = $model_album->getPicList($param,$page);
        Tpl::output('pic_list',$pic_list);
        Tpl::output('show_page',$page->show());
        switch($_GET['item']) {
        case 'goods':
            Tpl::showpage('goods_add.master_image','null_layout');
            break;
        case 'des':
            Tpl::showpage('goods_add.desc_image','null_layout');
            break;
        case 'mobile':
            Tpl::output('type', $_GET['type']);
            Tpl::showpage('goods_add.mobile_image', 'null_layout');
            break;
        }
    }

    /**
     * 视频列表，外部调用
     */
    public function video_listOp(){

        /**
         * 分页类
         */
        $page   = new Page();
        $page->setEachNum(7);
        $page->setStyle('admin');
        /**
         * 实例化相册类
         */
        $model_album = Model('video_album');
        /**
         * 图片列表
         */
        $param = array();
        $param['video_album.store_id']    = '0';
        $video_list = $model_album->getVideoList($param,$page);
        Tpl::output('video_list',$video_list);
        Tpl::output('show_page',$page->show());
        switch($_GET['item']) {
            case 'goods':
                Tpl::showpage('goods_add.master_video','null_layout');
                break;
        }
    }
}
