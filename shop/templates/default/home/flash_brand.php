<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/flash.css">
<div class="banner-top">
<div class="banner-wrap">
 <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_banner'];?>">
</div>
</div>
<div class="wrap">
<div class="sg-time">
        <div class="brand-logo">
            <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_brand'];?>" height="35" width="150">
        </div>
        <div class="countdown">
            <div class="time">
            <span class="bdb_item_time"> 
              <span class="bdb_item_time_txt time-remain" count_down="<?php echo $output['flash_info']['start_time']>TIMESTAMP ? $output['flash_info']['start_time']-TIMESTAMP : $output['flash_info']['end_time']-TIMESTAMP;?>"> 
                <b><?php echo $output['flash_info']['start_time']>TIMESTAMP ? '距开始' : '剩余';?></b> 
                <i time_id="d">0</i>天
                <i time_id="h">0</i>时
                <i time_id="m">0</i>分
                <i time_id="s">0</i>秒 
              </span> 
            </span> 
            </div>
        </div>
        <div class="activity-name">
            <strong><?php echo $output['flash_info']['flash_name'];?></strong>
            <span class="rebate">
					<?php echo $output['flash_info']['flash_title'];?>
			</span>
    	 </div>
    </div>
</div>
<div class="wrap">  
  <div class="activity-main">
  <div class="activity-list">
    <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
    <ul class="a-list">
      <?php foreach($output['goods_list'] as $goods){?>
      <li> 
        <div class="activity-item">
            <div class="activity-img">
                <a href="<?php echo $goods['goods_url']?>" target="_blank">
                    <img src="<?php echo $goods['image_url']?>" alt="" height="220" width="220">
                </a>
            </div>
            <div class="activity-name">
                <a href="<?php echo $goods['goods_url']?>" target="_blank"><?php echo $goods['goods_name']?></a>
            </div>
            <div class="activity-price">
                <strong><?php echo ncPriceFormatForList($goods['flash_price'])?></strong>
    						<i></i>
    			<del><?php echo ncPriceFormatForList($goods['goods_price'])?></del>
    			<span><?php echo $goods['flash_discount']?></span>
            </div>
        </div>
      </li>
      <?php }?>
    </ul>
    <?php }?>
  </div>
  <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
    <div class="tc mt20 mb20">
      <div class="pagination"><?php echo $output['show_page'];?></div>
    </div>
  <?php }?>
  </div>
</div>
<script type="text/javascript">
  takeCount();
	function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".time-remain").each(function(){
	        var obj = $(this);
	        var tms = obj.attr("count_down");
	        if (tms>0) {
	            tms = parseInt(tms)-1;
                var days = Math.floor(tms / (1 * 60 * 60 * 24));
                var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
                var minutes = Math.floor(tms / (1 * 60)) % 60;
                var seconds = Math.floor(tms / 1) % 60;

                if (days < 0) days = 0;
                if (hours < 0) hours = 0;
                if (minutes < 0) minutes = 0;
                if (seconds < 0) seconds = 0;
                obj.find("[time_id='d']").html(days);
                obj.find("[time_id='h']").html(hours);
                obj.find("[time_id='m']").html(minutes);
                obj.find("[time_id='s']").html(seconds);
                obj.attr("count_down",tms);
	        }
	    });
	}
</script>