<?php
/**
 * 微信公众平台分享接口
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit('Access Invalid!');
class wx_shareControl {
    public function indexOp(){
        $str = $_GET["str"];
        if (!empty($str)) {
            $share = explode('@@@', $str);//'分享链接@@@分享标题@@@分享图标@@@分享描述'
            $link = str_replace('&amp;', '&', $share[0]);
            $url = $link;
            Tpl::output('link',$url);
            Tpl::output('title',$share[1]);
            Tpl::output('imgUrl',$share[2]);
            if (empty($share[3])) $share[3] = C('site_name');
            Tpl::output('desc',$share[3]);
            $weixin_appid = C('wap_weixin_appid');
            if (!empty($weixin_appid)) {
                Tpl::output('appid',$weixin_appid);
                $jsapiTicket = $this->getJsApiTicket();
                $nonceStr = $this->createNonceStr();
                $timestamp = TIMESTAMP;
                $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
                $signature = sha1($string);
                Tpl::output('nonceStr',$nonceStr);
                Tpl::output('signature',$signature);
                Tpl::showpage('wx_share');
            }
        }
    }

    /**
     * 获取jsapi_ticket
     */
    private function getJsApiTicket(){
        $_token_info = C('weixin_mp_jsapi_array');
        if (!empty($_token_info)) {
            $_info = unserialize($_token_info);
            if ($_info['end_time'] > TIMESTAMP) {
                return $_info['jsapi_ticket'];
            }
        }
        
        $weixin_appid = C('wap_weixin_appid');
        $weixin_appsecret = C('wap_weixin_secret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$weixin_appid.'&secret='.$weixin_appsecret;
        $_token_info = $this->httpGet($url);
        if (!empty($_token_info)) {
            $_info = json_decode($_token_info, true);
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$_info['access_token'];
            $res = json_decode($this->httpGet($url), true);
            $_info['jsapi_ticket'] = $res['ticket'];
            $model_setting = Model('setting');
            $_info['end_time'] = TIMESTAMP+$_info['expires_in'];
            $update_array = array();
            $update_array['weixin_mp_jsapi_array'] = serialize($_info);
            if (C('weixin_mp_appid') == $weixin_appid) {//同步access_token全局缓存
                $update_array['weixin_mp_token_array'] = $update_array['weixin_mp_jsapi_array'];
            }
            $model_setting->updateSetting($update_array);
            return $_info['jsapi_ticket'];
        }
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
