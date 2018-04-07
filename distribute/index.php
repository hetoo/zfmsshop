<?php
/**
 * 分销板块初始化文件
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
define('APP_ID','distribute');
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));

require __DIR__ . '/../shopnc.php';

define('APP_SITE_URL', DISTRIBUTE_SITE_URL);
define('TPL_NAME',TPL_DISTRIBUTE_NAME);
define('DISTRIBUTE_RESOURCE_SITE_URL',DISTRIBUTE_SITE_URL.DS.'resource');
define('DISTRIBUTE_TEMPLATES_URL',DISTRIBUTE_SITE_URL.'/templates/'.TPL_NAME);
define('BASE_TPL_PATH',BASE_PATH.'/templates/'.TPL_NAME);

Shopnc\Core::runApplication();