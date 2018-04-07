<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="author" content="ShopNC">
<meta name="copyright" content="ShopNC Inc. All Rights Reserved">
<title><?php echo $output['html_title'];?></title>
<link href="<?php echo CHAIN_TEMPLATES_URL?>/css/chain.css" rel="stylesheet" type="text/css">
<link href="<?php echo CHAIN_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="<?php echo CHAIN_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var SITEURL = '<?php echo SHOP_SITE_URL;?>';var MEMBER_SITE_URL = '<?php echo MEMBER_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var CHAIN_RESOURCE_SITE_URL = '<?php echo CHAIN_RESOURCE_SITE_URL;?>';var CHAIN_TEMPLATES_URL = '<?php echo CHAIN_TEMPLATES_URL;?>';</script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/member.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="<?php echo RESOURCE_SITE_URL;?>/js/html5shiv.js"></script>
      <script src="<?php echo RESOURCE_SITE_URL;?>/js/respond.min.js"></script>
<![endif]-->
</head>

<body>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/ToolTip.js"></script>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<header class="ncsc-head-layout w">
  <div class="wrapper">
    <div class="ncsc-admin">
      <a href="<?php echo urlShop('show_chain','index',array('chain_id'=>$_SESSION['chain_id']))?>" target="_blank">
      <div class="pic" title="<?php echo $_SESSION['chain_name'];?>"><img src="<?php echo $_SESSION['chain_img'];?>"></div></a>
    </div>
    <div class="center-logo"> <a href="<?php echo SHOP_SITE_URL;?>" target="_blank"><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('seller_center_logo');?>" class="pngFix" alt=""/></a>
      <h1>门店管理</h1>
    </div>
    <ul class="ncsc-nav">
      <li> <a href="index.php?act=goods" class="<?php echo $_GET['act'] == 'goods'?'current':'';?>">商品库存</a> </li>
      <li> <a href="index.php?act=order" class="<?php echo $_GET['act'] == 'order'?'current':'';?>">自提订单</a> </li>
      <li> <a href="index.php?act=chain_order" class="<?php echo $_GET['act'] == 'chain_order'?'current':'';?>">门店配送</a> </li>
      <li> <a href="index.php?act=chain_sender" class="<?php echo $_GET['act'] == 'chain_sender'?'current':'';?>">门店发货</a> </li>
      <li> <a href="index.php?act=chain_reciver" class="<?php echo $_GET['act'] == 'chain_reciver'?'current':'';?>">代收订单</a> </li>
      <li> <a href="index.php?act=chain_bill" class="<?php echo $_GET['act'] == 'chain_bill'?'current':'';?>">门店结算</a> </li>
      <li> <a href="index.php?act=voucher" class="<?php echo $_GET['act'] == 'voucher'?'current':'';?>">优惠券</a> </li>
      <li> <a href="index.php?act=setting" class="<?php echo $_GET['act'] == 'setting'?'current':'';?>">门店设置</a> </li>
      <li><a href="javascript:;" onclick="ajaxget('<?php echo urlChain('login', 'logout');?>')" title="安全退出">退出</a></li>
    </ul>
  </div>
</header>
<div class="ncsc-layout wrapper">
  <div class="main-content" id="mainContent">
    <?php require_once($tpl_file); ?>
  </div>
  <div id="layoutRight" class="ncsc-layout-right"> </div>
</div>
<?php require_once template('layout/footer');?>
</body>
</html>
