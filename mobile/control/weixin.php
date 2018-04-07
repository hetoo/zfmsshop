<?php
/**
 * 微信公众平台推送处理
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit('Access Invalid!');
class weixinControl {
    /**
     * 推送处理
     */
    public function indexOp(){
        if (!empty($_GET["echostr"]) && !empty($_GET["timestamp"])) {
            if($this->checkSignature()){
            	echo $_GET["echostr"];
            	exit;
            }
        }else{
        	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        	if (!empty($postStr)){
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $msgType = strtolower($postObj->MsgType);//消息类型
                $msgEvent = '';
                if($msgType == 'event'){//事件推送
                    $msgEvent = strtolower($postObj->Event);
                    $fromUsername = (string) $postObj->FromUserName;
                    Model('wx_log')->addWx(array('log_msg'=> $postStr,'to_id'=> $fromUsername));
                    switch ($msgEvent) {
                        case "subscribe"://关注事件
                            $k = (string) $postObj->EventKey;
                            if (!empty($k)){
                                $str = str_replace('qrscene_', '', $k);
                                $id = substr($str,2);
                                Model('member')->editMember(array('member_id'=> $id),array('weixin_mp_openid'=> $fromUsername));
                            }
                            break;
                        case "unsubscribe"://取消关注事件
                            Model('member')->editMember(array('weixin_mp_openid'=> $fromUsername),array('weixin_mp_openid'=> ''));
                            break;
                        case "scan"://扫描带参数二维码事件
                            $k = (string) $postObj->EventKey;
                            $id = substr($k,2);
                            if (!empty($id)) Model('member')->editMember(array('member_id'=> $id),array('weixin_mp_openid'=> $fromUsername));
                            break;
                        default:
                            break;
                    }
                }
            }
        	echo "success";
        	exit;
        }
    }

    /**
     * 验证消息
     */
    public function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
    	$token = C('weixin_mp_token');//$token = 'weixin';
    	$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
    	sort($tmpArr, SORT_STRING);
    	$tmpStr = implode( $tmpArr );
    	$tmpStr = sha1( $tmpStr );
    	
    	if( $tmpStr == $signature ){
    		return true;
    	}else{
    		return false;
    	}
    }
}
