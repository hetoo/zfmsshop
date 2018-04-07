<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/spike.css">
<div class="seckill-container wrapper">  
  <div class="grid_c1">
    <ul class="bdb_list">
      <li class="bdb_item"> 
        <span class="bdb_item_time"> 
          <span class="bdb_item_time_txt time-remain" count_down="<?php echo $output['spike_info']['start_time']>TIMESTAMP ? $output['spike_info']['start_time']-TIMESTAMP : $output['spike_info']['end_time']-TIMESTAMP;?>">
            <b class="bdb_item_time_txt_word"><?php echo $output['spike_info']['start_time']>TIMESTAMP ? '距开始' : '距结束';?></b> 
            <i class="bdb_item_time_txt_time" time_id="d">0</i>天
            <i class="bdb_item_time_txt_time" time_id="h">0</i>时
            <i class="bdb_item_time_txt_time" time_id="m">0</i>分
            <i class="bdb_item_time_txt_time" time_id="s">0</i>秒 
          </span> 
        </span> 
        <img class="bdb_item_banner" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['spike_info']['spike_banner'];?>" data-webp="no">
      </li>
    </ul>
  </div>
  <div class="seckill_list">
    <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
    <ul class="s_goodslist">
      <?php foreach($output['goods_list'] as $goods){?>
      <li class="seckill_goods"> 
        <a href="<?php echo $goods['goods_url']?>" target="_blank" class="seckill_link">
          <img class="s_link_img" src="<?php echo $goods['image_url']?>">
          <h4 class="seckill_title"><?php echo $goods['goods_name']?></h4>
          <span class="s_goods_info"> 
            <span class="s_goods_info_txt"> 
              <span class="s_goods_price"> 
                <i class="s_p_now"><?php echo ncPriceFormatForList($goods['spike_price'])?><i class="s_p_small"></i></i> 
                <span class="s_goods_pre"><del><?php echo ncPriceFormatForList($goods['goods_price'])?></del></span> 
              </span> 
              <span class="s_goods_progress"> 
              <?php $percent = intval($goods['had_spiked_count']*100/$goods['spike_amount']);?>
                <i class="s_goods_txt">已售<?php echo $percent>100 ? 100 : $percent;?>%</i>
                <i class="s_inner"><b class="s_completed" style="width:<?php echo $percent>100 ? 100 : $percent;?>%"></b></i> 
              </span>
            </span> 
            <span class="s_info_btn"> <i></i> </span> 
            <i class="s_info_i">立即抢购</i> 
          </span> 
        </a> 
      </li>
      <?php }?>
    </ul>
    <?php }?>
  </div>
  <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
  <?php if($output['page_count'] > $output['cur_page']){?>
  <div class="m_goods"> 
    <a href="javascript:void(0)" class="show_btn show_more" ><span class="show_btn_t">查看更多</span><i class="show_icon"></i></a> 
  </div>
  <?php }else{?>
  <div class="m_goods"> 
    <a href="javascript:void(0)" style="cursor:default;" class="show_btn" ><span class="show_btn_t">没有更多商品了...</span></a> 
  </div>
  <?php }?>
  <?php }?>
</div>
<script type="text/javascript">
  var cur_page = <?php echo $output['cur_page']?>;
  var page_count = <?php echo $output['page_count']?>;
  $('a.show_more').click(function(){
    if(page_count > cur_page){
      var show_page = cur_page + 1;
      $.get('index.php?act=spike&op=brand_ajax&spike_id=<?php echo $output['spike_info']['spike_id']?>&curpage='+show_page,function(datas){
        cur_page++;
        $('ul.s_goodslist').append(datas);
        if(cur_page == page_count){
          $('div.m_goods').html('<a href="javascript:void(0)" style="cursor:default;" class="show_btn" ><span class="show_btn_t">没有更多商品了...</span></a>');
        }
      });
    }else{
      $('div.m_goods').html('<a href="javascript:void(0)" style="cursor:default;" class="show_btn" ><span class="show_btn_t">没有更多商品了...</span></a>');
    }
  });
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