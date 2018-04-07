<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/spike.css">
<div class="seckill-container wrapper">
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
	<div class="ncp-category">
		<ul>
			<li class="item_on"><a href="javascript:void(0);">秒杀分类</a></li>
	        <?php if (!empty($output['show_goods_class']) && is_array($output['show_goods_class'])) { $i = 0; ?>
	        <?php foreach ($output['show_goods_class'] as $key => $val) { $i++; ?>
	        <li cat_id="<?php echo $val['gc_id'];?>">
	            <a href="<?php echo urlShop('spike','spike_list',array('cate_id'=> $val['gc_id']));?>" <?php echo $val['gc_id'] == $_GET['cate_id']?'class="selected"':''?>><?php echo $val['gc_name'];?></a>
	        </li>
	        <?php } ?>
	        <?php } ?>
		</ul>
	</div>	
	<div class="seckill_list">
		<ul class="s_goodslist">
        <?php if(!empty($output['list']) && is_array($output['list'])) { ?>
        <?php foreach($output['list'] as $k => $v) { ?>
            <?php if(!empty($v['goods_list']) && is_array($v['goods_list'])) { ?>
            <?php foreach($v['goods_list'] as $k2 => $v2) { ?>
			<li class="seckill_goods"> 
				<a href="<?php echo urlShop('goods','index',array('goods_id'=>$v2['goods_id']));?>" target="_blank" class="seckill_link"> 
					<img class="s_link_img" src="<?php echo thumb($v2, 240);?>">
					<h4 class="seckill_title"><?php echo $v2['goods_name'];?></h4>
					<span class="s_goods_info"> 
						<span class="s_goods_info_txt"> 
							<span class="s_goods_price"> 
								<i class="s_p_now"><?php echo ncPriceFormatForList($v2['spike_price']);?><i class="s_p_small"></i></i> 
								<span class="s_goods_pre"><del><?php echo ncPriceFormatForList($v2['goods_price']);?></del></span> 
							</span> 
							<span class="s_goods_progress"> 
							    <?php $percent = intval($v2['had_spiked_count']*100/$v2['spike_amount']);?>
								<i class="s_goods_txt">已售<?php echo $percent>100 ? 100 : $percent;?>%</i> 
								<i class="s_inner"><b class="s_completed" style="width:<?php echo $percent>100 ? 100 : $percent;?>%"></b></i> 
							</span> 
						</span> 
						<span class="s_info_btn"> <i></i> </span> 
						<i class="s_info_i">立即抢购</i> 
					</span> 
				</a> 
			</li>
            <?php } ?>
            <?php } ?>
            <?php if(!empty($v['spike_list']) && is_array($v['spike_list'])) { ?>
            <?php foreach($v['spike_list'] as $k2 => $v2) { ?>
	<li class="ad_item">
      <a target="_blank" href="index.php?act=spike&op=brand&spike_id=<?php echo $v2['spike_id'];?>" class="ad_item_lk"></a>
      <img data-webp="no" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v2['spike_common_bg'];?>" class="ad_item_img">
      
      <div class="ad_item_container">
      <ul class="ad_item_list">
        
            <?php if(!empty($v2['goods_list']) && is_array($v2['goods_list'])) { ?>
            <?php foreach($v2['goods_list'] as $k3 => $v3) { ?>
      <li class="ad_item_list_item">
        <img data-webp="no" src="<?php echo thumb($v3, 240);?>" class="ad_item_list_item_img">
        <div class="ad_item_list_item_goods_info">
            <span class="ad_item_list_item_price">
                <i class="ad_item_list_item_price_now"><?php echo ncPriceFormatForList($v3['spike_price']);?></i>
                <del class="ad_item_list_item_price_pre"><?php echo ncPriceFormatForList($v3['goods_price']);?></del>
            </span>
            <span class="ad_item_list_item_progress">
                <?php $percent = intval($v3['had_spiked_count']*100/$v3['spike_amount']);?>
                    <i class="ad_item_list_item_progress_txt">已售<?php echo $percent>100 ? 100 : $percent;?>%</i>
                    <i class="ad_item_list_item_progress_inner"><b class="ad_item_list_item_progress_completed" style="width:<?php echo $percent>100 ? 100 : $percent;?>%"></b></i>
            </span>
         </div>
      </li>
            <?php } ?>
            <?php } ?>
      
      </ul>
      </div>
    </li>
            <?php } ?>
            <?php } ?>
        <?php } ?>
        <?php } ?>		
		</ul>
	</div>
   <div class="seckill_more"> 
   <h3 class="more_goods_tit">更多好货</h3>
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
                <i class="s_p_now"><?php echo ncPriceFormatForList($goods['spike_price']);?><i class="s_p_small"></i></i> 
                <span class="s_goods_pre"><del><?php echo ncPriceFormatForList($goods['goods_price']);?></del></span> 
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
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/home_index.js" ></script>
	
<script type="text/javascript">
  var cur_page = <?php echo $output['cur_page']?>;
  var page_count = <?php echo $output['page_count']?>;
  $('a.show_more').click(function(){
    if(page_count > cur_page){
      var show_page = cur_page + 1;
      $.get('index.php?act=spike&op=list_ajax&curpage='+show_page,function(datas){
        cur_page++;
        $('.seckill_more .s_goodslist').append(datas);
        if(cur_page == page_count){
          $('div.m_goods').html('<a href="javascript:void(0)" style="cursor:default;" class="show_btn" ><span class="show_btn_t">没有更多商品了...</span></a>');
        }
      });
    }else{
      $('div.m_goods').html('<a href="javascript:void(0)" style="cursor:default;" class="show_btn" ><span class="show_btn_t">没有更多商品了...</span></a>');
    }
  });
</script>