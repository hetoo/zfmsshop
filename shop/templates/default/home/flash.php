<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/flash.css" rel="stylesheet" type="text/css">

<div id="focus-banner">
		<ul id="fullScreenSlides" class="full-screen-slides">
		    <?php if (is_array($output['pic_list']) && !empty($output['pic_list'])) { ?>
		    <?php foreach($output['pic_list'] as $k => $v) { ?>
			<?php if (!empty($v['pic'])) { ?>
			<li> <a href="<?php echo $v['url'];?>" target="_blank" class="focus-banner-img"> 
			    <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/'.$v['pic'];?>" alt=""> </a> </li><?php } ?>
		    <?php } ?>
		    <?php } ?>
		</ul>
</div>
<div class="bg-wraper">
<div class="sg-wrapper">
  <div class="sg-main">
    <div class="sg-main-left">
        <div class="ml-tit"> <img alt="" src="<?php echo SHOP_TEMPLATES_URL;?>/images/sgtitle.png" > </div>
        <?php if(!empty($output['start_list']) && is_array($output['start_list'])){ ?>
        <?php foreach($output['start_list'] as $v){?>
      <div class="sg-item">
        <div class="mli-img"> 
            <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank"> <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_pic'];?>"> </a>
          <div class="countdown-tag"> <span class="tick-logo"></span>
            <p render_type="active" class="time-remain" count_down="<?php echo $v['end_time']-TIMESTAMP;?>">
                剩<i time_id="d">0</i>天</p>
            <span class="arrow-circle"></span> </div>
          <div class="act-msg">
            <div class="act-mg-bg"></div>
            <p class="txt"><?php echo $v['flash_explain'];?></p>
          </div>
          </div>
        <div class="mli-info">
          <div class="brand-logo"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank"> 
            <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_brand'];?>" width="150" height="35"> </a> </div>
          <div class="mli-short-line"></div>
          <div class="brand-active"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank"> <?php echo $v['flash_name'];?></a> 
            <span> <?php echo $v['flash_title'];?> </span> </div>
          <div class="brand-rebate"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank">立即选购</a> </div>
        </div>
      </div>
        <?php }?>
        <?php }?>
    </div>
    <div class="sg-main-right">
    <div class="sg-con">
    <?php echo loadadv(30,'html');?>
    </div>
      <div class="sidebar">
        <ul class="tab-nav tabbar">
          <li class="fore1 curr"><span>单品TOP10</span><b></b></li>
          <li class="fore2"><span>品牌推荐</span><b></b></li>
        </ul>
        <div class="tab-mc child-tab-show">
          <ul class="tm-list pb3">
              <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
              <?php foreach($output['goods_list'] as $k => $goods){?>
            <li class="fore single "> <a target="_blank" href="<?php echo $goods['goods_url']?>"> <span class="num num<?php echo $k+1;?>"></span> 
                <img class="prd-img" src="<?php echo $goods['image_url']?>">
              <div class="tl-detail">
                <h2><?php echo $goods['flash_name']?></h2>
                <p class="tl-des" title=""><?php echo $goods['goods_name']?></p>
                <p class="tl-yen">¥<?php echo $goods['flash_price']?></p>
                <p class="tl-pnum">已售件数：<?php echo $goods['buy_count']?></p>
              </div>
              </a> </li>
              <?php }?>
              <?php }?>
          </ul>
        </div>
        <div class="tab-mc child-tab-show hide">
          <ul class="tm-list">
        <?php if(!empty($output['start_list_brand']) && is_array($output['start_list_brand'])){ ?>
        <?php foreach($output['start_list_brand'] as $k => $v){?>
            <li class="brand"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank">
              <div class="brand-detail">
                <div class="brand-detail-inner"> <span class="brand-num brand-num<?php echo $k+1;?>"></span> 
                    <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_brand'];?>"> 
                    <span class="brand-name" title=""><?php echo $v['flash_name'];?></span> </div>
              </div>
              <img class="tl-brand-img" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_recommend_pic'];?>"> </a> 
        <?php }?>
        <?php }?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="sg-wrapper">
  <div class="sg-mc">
    <ul class="more-last">
        <?php if(!empty($output['start_list_0']) && is_array($output['start_list_0'])){ ?>
        <?php foreach($output['start_list_0'] as $k => $v){?>
      <li>
        <div class="act-img-wrap"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank"> <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_pic'];?>"> </a>
          <div class="countdown-tag"> <span class="tick-logo"></span>
            <p class="time-remain" count_down="<?php echo $v['end_time']-TIMESTAMP;?>">
                剩<i time_id="d">0</i>天
                <i time_id="h">0</i>时
                <i time_id="m">0</i>分
                </p>
            <span class="arrow-circle"></span> </div>
                </div>
        <div class="more-detail-wrap">
          <div class="inner"> <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_brand'];?>" width="150" height="35">
            <h3> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>" target="_blank"> <?php echo $v['flash_name'];?></a> </h3>
            <span class="discount"> <?php echo $v['flash_title'];?> </span> </div>
        </div>
      </li>
        <?php }?>
        <?php }?>
    </ul>
  </div>
</div>
<div class="sg-wrapper">
  <div class="mhz-top">
    <div class="mhz-box">
      <ul class="mhz-tab tab">
        <li class="fore1 curr">明天预告<i class="arrow-down"></i></li>
        <li class="fore2">后天预告<i class="arrow-down"></i></li>
        <li class="fore3"><?php echo $output['week_3'];?>预告<i class="arrow-down"></i></li>
      </ul>
      <div class="mhz-mc mhz-tab-show">
        <?php if(!empty($output['start_list_1']) && is_array($output['start_list_1'])){ ?>
        <?php foreach($output['start_list_1'] as $k => $v){?>
        <div class="pop-mod">
          <div class="pm-box">
            <div class="pm-img"> <a target="_blank" href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>">
                <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_pic'];?>"> </a> 
                </div>
            <div class="pm-info">
              <div class="brand-active"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>"> <?php echo $v['flash_name'];?> </a> </div>
              <div class="brand-rebate"> </div>
              <div class="brand-logo"> <a target="_blank"> <img  alt="" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_brand'];?>" width="122" height="28"> </a> </div>
            </div>
          </div>
        </div>
        <?php }?>
        <?php }?>
      </div>
      <div class="mhz-mc mhz-tab-show hide">
        <?php if(!empty($output['start_list_2']) && is_array($output['start_list_2'])){ ?>
        <?php foreach($output['start_list_2'] as $k => $v){?>
        <div class="pop-mod">
          <div class="pm-box">
            <div class="pm-img"> <a target="_blank" href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>">
                <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_pic'];?>"> </a> 
                </div>
            <div class="pm-info">
              <div class="brand-active"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>"> <?php echo $v['flash_name'];?> </a> </div>
              <div class="brand-rebate"> </div>
              <div class="brand-logo"> <a target="_blank"> <img  alt="" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_brand'];?>" width="122" height="28"> </a> </div>
            </div>
          </div>
        </div>
        <?php }?>
        <?php }?>
      </div>
      <div class="mhz-mc mhz-tab-show hide">
        <?php if(!empty($output['start_list_3']) && is_array($output['start_list_3'])){ ?>
        <?php foreach($output['start_list_3'] as $k => $v){?>
        <div class="pop-mod">
          <div class="pm-box">
            <div class="pm-img"> <a target="_blank" href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>">
                <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_pic'];?>"> </a> 
                </div>
            <div class="pm-info">
              <div class="brand-active"> <a href="index.php?act=flash&op=brand&flash_id=<?php echo $v['flash_id'];?>"> <?php echo $v['flash_name'];?> </a> </div>
              <div class="brand-rebate"> </div>
              <div class="brand-logo"> <a target="_blank"> <img  alt="" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['flash_brand'];?>" width="122" height="28"> </a> </div>
            </div>
          </div>
        </div>
        <?php }?>
        <?php }?>
      </div>
    </div>
  </div>
</div>
</div>
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/home_index.js" ></script>

<script type="text/javascript">
$(function (){
  $(".tab-nav li").click(function (){
      var tabLiArr = $(this).parent().find("li");
      var tabShowArr = $(this).parents(".tabbar").nextAll(".child-tab-show");
      var index = $.inArray(this,tabLiArr);
      if($(tabShowArr).eq(index)){
        $(tabLiArr).removeClass("curr").eq(index).addClass("curr");
	    $('.tab-nav').css('border-left-color', '#e1e1e1');
        $('.tab-nav').css('border-right-color', '#e1e1e1');
        $(tabShowArr).addClass("hide").eq(index).removeClass("hide");
      }
  });
  
    $(".mhz-tab li").click(function (){
      var tabLiArr = $(this).parent().find("li");
      var tabShowArr = $(this).parents(".tab").nextAll(".mhz-tab-show");
      var index = $.inArray(this,tabLiArr);
      if($(tabShowArr).eq(index)){
        $(tabLiArr).removeClass("curr").eq(index).addClass("curr");
        $(tabShowArr).addClass("hide").eq(index).removeClass("hide");
      }
  });
})
</script>