<?php
/**
 * cms文章心情
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
class attitudeControl extends CMSHomeControl{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 文章心情
     */
    public function article_attitudeOp() {
        $article_id = intval($_GET['article_id']);
        $article_attitude = intval($_GET['article_attitude']);
        if(empty($article_id) || empty($article_attitude)) {
            $data['result'] = 'false';
            $data['message'] = Language::get('wrong_argument');
            self::echo_json($data);
        }

        if(!empty($_SESSION['member_id'])) {
            $model_attitude = Model('cms_article_attitude');
            $param = array();
            $param['attitude_article_id'] = $article_id;
            $param['attitude_member_id'] = $_SESSION['member_id'];
            $exist = $model_attitude->isExist($param);
            if(!$exist) {
                $param['attitude_time'] = time();
                $result = $model_attitude->save($param);
                if($result) {

                    //评论计数加1
                    $model_article = Model('cms_article');
                    $update = array();
                    $update['article_attitude_'.$article_attitude] = array('exp','article_attitude_'.$article_attitude.'+1');
                    $condition = array();
                    $condition['article_id'] = $article_id;
                    $model_article->modify($update, $condition);

                    //返回信息
                    $data['result'] = 'true';

                } else {
                    $data['result'] = 'false';
                    $data['message'] = Language::get('nc_common_save_fail');
                }
            } else {
                $data['result'] = 'false';
                $data['message'] = Language::get('attitude_published');
            }
        } else {
            $data['result'] = 'false';
            $data['message'] = Language::get('no_login');
        }
        self::echo_json($data);

    }
}
