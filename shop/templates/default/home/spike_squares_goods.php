<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
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
<?php }?>