<?php
header("Content-type: text/html; charset=UTF-8");
define('InShopNC',true);

$config_file = '../../../config/config.ini.php';
$config = require($config_file);
$code = $_GET['code'];
$state  = $_GET['state'];
if(!empty($code)) {
    $qq_url = $config['member_site_url'].'/index.php?act=connect_qq&op=index&code=';
    $str = substr($state,0,3);
    if($str == 'api'){
        $state = substr($state,3);
        $qq_url = $config['mobile_site_url'].'/index.php?act=connect&op=get_qq_info&client=wap&code=';
    }
    @header("location: ".$qq_url.$code.'&state='.$state);
} else {
    exit('参数错误');
}