<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php defined('InShopNC') or exit('Access Invalid!');?>
    <link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        body { background: #FFF none; color: black;  }
        /* 链接 */
        a { color: #333; text-decoration: none; outline: medium none; -webkit-transition-property:color; -webkit-transition-duration: 0.3s; -webkit-transition-timing-function: ease; }
        a:link, a:visited, a:active { text-decoration: none;}
        a:hover { color: #C81623; text-decoration: none;}
        /* tip提示 */
        .tip-yellowsimple { color:#000; background-color:#fff9c9; text-align:left; min-width:50px; max-width:300px; border:1px solid #c7bf93; border-radius:4px; -moz-border-radius:4px; -webkit-border-radius:4px; z-index:1000; padding:6px 8px;}
        .tip-yellowsimple .tip-inner { font:12px/16px arial,helvetica,sans-serif;}
        .tip-yellowsimple .tip-arrow-top { background:url(../images/tip-yellowsimple_arrows.gif) no-repeat; width:9px; height:6px; margin-top:-6px; margin-left:-5px; top:0; left:50%;}
        .tip-yellowsimple .tip-arrow-right { background:url(../images/tip-yellowsimple_arrows.gif) no-repeat -9px 0; width:6px; height:9px; margin-top:-4px; margin-left:0; top:50%; left:100%;}
        .tip-yellowsimple .tip-arrow-bottom { background:url(../images/tip-yellowsimple_arrows.gif) no-repeat -18px 0; width:9px; height:6px; margin-top:0; margin-left:-5px; top:100%; left:50%;}
        .tip-yellowsimple .tip-arrow-left { background:url(../images/tip-yellowsimple_arrows.gif) no-repeat -27px 0; width:6px; height:9px; margin-top:-4px; margin-left:-6px; top:50%; left:0;}
    </style>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/jquery.printarea.js" charset="utf-8"></script>
    <title><?php echo $lang['member_printorder_print'];?>电子面单</title>
</head>
<body>
<div class="print-layout">
    <div class="print-btn" id="printbtn" title="<?php echo $lang['member_printorder_print_tip'];?>"><i></i><a href="javascript:void(0);"><?php echo $lang['member_printorder_print'];?></a></div>
    <div class="a4-size"></div>
    <div class="print-page">
        <div id="printarea">
            <style type="text/css">
                body {color: black;}
            </style>
            <?php echo $output['print_template'];?>
        </div>
    </div>
</div>
</body>
<script>
    $(function(){
        $("#printbtn").click(function(){
            $("#printarea").printArea();
        });
    });

    //打印提示
    $('#printbtn').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'bottom',
        offsetY: 5,
        allowTipHover: false
    });
</script>
</html>