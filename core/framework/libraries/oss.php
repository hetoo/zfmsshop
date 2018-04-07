<?php
/**
 * aliyun oss
 *
 * @package    library
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @author     ShopNC Team
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
require_once BASE_DATA_PATH.'/api/OSS2/autoload.php';
require_once(BASE_DATA_PATH.'/api/OSS2/OssClient.php');
use OSS\OssClient;
final class oss {
    private static $endpoint;
    private static $accessKeyId;
    private static $accessKeySecret;
    private static $bucket;
    private static $oss_sdk_service;
    private static function _init() {
        self::$endpoint = C('oss.api_url');
        self::$accessKeyId = C('oss.access_id');
        self::$accessKeySecret = C('oss.access_key');
        self::$bucket = C('oss.bucket');
        self::$oss_sdk_service = new OssClient(self::$accessKeyId, self::$accessKeySecret, self::$endpoint, false);
    }

    /**
     * 
     * @param unknown $src_file
     * @param unknown $new_file
     */
    public static function upload($src_file,$new_file) {
        self::_init();
        try{
            $response = self::$oss_sdk_service->uploadFile(self::$bucket,$new_file,$src_file);
            if (!empty($response['info'])) {
                return true;
            } else {
                return false;
            }
        } catch (OssException $ex){
            return false;
        }
    }

    public static function del($img_list = array()) {
        self::_init();
        try{
            $options = array(
                    'quiet' => false
            );
            self::$oss_sdk_service->deleteObjects(self::$bucket,$img_list,$options);
        } catch (Exception $ex){
            return false;
        }
    }
}
