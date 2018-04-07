<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if($_REQUEST['op'] == 'cms_special_detail' ){?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo $output['special_detail']['special_title']; ?></title>
<meta name="keywords" content="<?php echo $output['special_detail']['special_title']; ?>" />
<meta name="description" content="<?php echo $output['special_detail']['special_title']; ?>" />
<meta name="author" content="ShopNC">
<meta name="copyright" content="ShopNC Inc. All Rights Reserved">
<?php }?>
<style type="text/css">
#body { color: #333333; background-color: <?php echo $output['special_detail']['special_background_color'];?>; background-image: url(<?php echo getCMSSpecialImageUrl($output['special_detail']['special_background']);?>); background-repeat: <?php echo $output['special_detail']['special_repeat'];?>; background-position: top center; width: 100%; padding: 0; margin: 0; overflow: hidden;}
img { border: 0; vertical-align: top; }
.cms-special-detail-content { width: 1200px; margin-top: <?php echo $output['special_detail']['special_margin_top']?>px; margin-right: auto; margin-bottom: 0; margin-left: auto; overflow: hidden;}
.special-content-link, .special-hot-point { text-align: 0; display: block; width: 100%; float: left; clear: both; padding: 0; margin: 0; border: 0; overflow: hidden;}
.special-content-goods-list,#special_content_lottery_view { width: 1200px; margin: 0 auto; overflow: hidden;}

.special-goods-list { background: #FFFFFF; width: 988px; padding: 0 2px 0 0; overflow: hidden;}
.special-goods-list li { float: left; width: 160px; padding: 15px 30px; margin: 15px 13px 15px 12px; border: solid 1px #D8D8D8;}
.special-goods-list dl { border: none; width: 160px; height: 60px; padding: 160px 0 0 0; position: relative; z-index: 1;}
.special-goods-list dt.name { font-size: 12px; line-height: 18px; height: 36px; margin: 5px; overflow: hidden;}
.special-goods-list dd.image { width: 160px; height: 160px; position: absolute; z-index: 1; top: 0; left: 0;}
.special-goods-list dd.image a { text-align: center; vertical-align: middle; display: table-cell; width:160px; height: 160px; overflow: hidden;}
.special-goods-list dd.image img { max-width: 160px; max-height: 160px; margin-top:expression(100-this.height/2);}
.special-goods-list dd.price { color: #999;}
.special-goods-list dd.price em { font-weight: 600; color: #F30;}
#special_content_lottery_view { padding-top: 10px; height: 550px;}

#special_content_lottery_view .lottery_info{ width: 400px; height: 500px; display: inline-block; float: left; }
.lottery_info .winner-name{/*position: absolute;*/ top: 52px; left: 60px; width: 282px; height: 456px; padding: 25px 15px 15px; background-color: #fff; font-size: 12px; -webkit-border-radius: 6px; border-radius: 6px; box-shadow: 0 8px 0 0px #f9f9f9;
   border: 1px solid #f0f0f0;
}
.lottery_info .winner-list{position: relative; height: 360px; margin: 0 5px; overflow: hidden;}
.lottery_info .winner-list::before{content: ""; top: 0; width: 100%; height: 40px; position: absolute; z-index: 1;
    background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%);
    background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(255,255,255,0) 100%);
    background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(255,255,255,0) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=0 );
}
.lottery_info .list-wrap{display: none; position: absolute; top: 0px; width: 100%;}
.lottery_info .winner-title{position: relative; height: 40px; line-height: 40px; text-align: center;}
.lottery_info .winner-title-bg{position: absolute; top: 19px; left: 0; width: 100%; border-top: 2px solid #f34c49;}
.lottery_info .winner-title-con{position: relative; display: inline-block; *display: inline; *zoom: 1; padding: 0 10px; font-size: 24px; background-color: #fff; color: #f34c49;}
.lottery_info .winner-inner-tit{overflow: hidden; line-height: 36px; margin: 10px 0;}
.lottery_info .winner-name span{float: left; width: 39%; padding: 0 5%; white-space: nowrap; overflow: hidden;text-align:center;text-overflow: ellipsis;}
.lottery_info .winner-info{line-height: 38px; border-top: 1px dashed #d9d8d8; overflow: hidden;}
.lottery_info .border-none{border: 0;}
.lottery_info .w-zm{font-size: 16px; color: #9d4e21;}
.lottery_info .w-name{color: #928977;}
.lottery_info .w-prize{color: #f34c49;}
.lottery_info .winner-none{text-align: center; line-height: 300px; color: #666; font-size: 14px;}

#special_content_lottery_view .lottery_view{ width: 788px; padding: 20px 2px 0 0; overflow: hidden;}
.m-ui-dial { position: relative; margin: 0 auto; width: 499px; height: 499px;}
.m-ui-dial .pointer {position: absolute; top: 50%; left: 50%; display: block; margin-top: -139px !important; width: 150px; height: 238px; margin: -119px 0 0 -75px; -webkit-transform-origin: 75px 139px; transform-origin: 75px 139px;}
.m-ui-dial .btn { position: absolute; left: 0; display: block; top: 64px; width: 150px; height: 150px; border-radius: 75px;}
</style>
<?php if($_REQUEST['op'] == 'cms_special_detail' ){?>
</head>
<body>
<?php }?>

<div class="cms-special-detail-content">
<?php echo html_entity_decode($output['special_detail']['special_content']);?>
    
</div>

<?php if($_REQUEST['op'] == 'cms_special_detail' ){?>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL?>/js/jquery.js"></script>
<script type="text/javascript">
    var obj = $("div#special_content_lottery_view");
    var lot_id = obj.find("input[nctype='lot_id']").val();
    var obj_info = obj.find(".lottery_info");
    var obj_view = obj.find(".lottery_view");
    $.ajax({
        type:'post',
        url:"<?php echo urlShop('lottery_dial','dial_info');?>",
        data:{lot_id:lot_id},
        dataType:'json',
        async:false,
        success:function(result){
            if(result.code == 200){
                lot_info = result.datas.dial_info;
                qua_amount = parseInt(result.datas.dail_qua);
                if(lot_info.lot_bg != ''){
                    $('#special_content_lottery_view').css({'background':'url("'+lot_info.lot_bg+'") no-repeat center','background-size':'100%'});
                }else{
                    $('#special_content_lottery_view').css({'background':'#faebc0'});
                }
                var html = '<div class="m-ui-dial" style="background:url(\''+lot_info.lot_dial_bg+'\') no-repeat center;background-size:100%">';
                html += '<div id="js_pointer" class="pointer" style="background:url(\''+lot_info.lot_dial_pointer+'\') no-repeat center;background-size:100%">';
                html += '<a class="btn" href="javascript:;"></a></div></div>';
                obj_view.html(html);
                var p_html = "<div class='winner-none'>还没出现中奖者，期待您赢取大奖！</div>";
                obj_info.find('.winner-name').append(p_html);
            }
        }
    });
</script>
</body>
</html>
<?php }?>