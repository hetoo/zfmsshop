<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if(!empty($output['spike_list']) && is_array($output['spike_list'])){ ?>

<ul class="dialog-goodslist-s2 goods-list scrollbar-box">
  <?php foreach($output['spike_list'] as $k => $v){ ?>
  <li style="display: inline-block; margin:0 15px 0 0;width:219px; height:200px;">
    <div onclick="select_recommend_spike(<?php echo $v['spike_id'];?>);" class="goods-pic" style=" width:219px; height:150px;"><span class="ac-ico"></span><span class="thumb size-72x72"><i></i>
        <img style=" width:219px; height:150px;" spike_id="<?php echo $v['spike_id'];?>" store_name="<?php echo $v['store_name'];?>" 
        title="<?php echo $v['spike_name'];?>" spike_name="<?php echo $v['spike_name'];?>" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v['spike_common_bg'];?>" onload="javascript:DrawImage(this,72,72);" /></span></div>
    <div class="spike-name" style="height:32px;  height: 32px;
    line-height: 16px;
    overflow: hidden;
    padding-top: 4px;"><a href="<?php echo SHOP_SITE_URL."/index.php?act=spike&op=brand&spike_id=".$v['spike_id'];?>" target="_blank"><?php echo $v['spike_name'];?></a></div>
  </li>
  <?php } ?>
  <div class="clear"></div>
</ul>
<div id="show_recommend_spike" class="pagination"> <?php echo $output['show_page'];?> </div>
<?php }else { ?>
<p class="no-record"><?php echo $lang['nc_no_record'];?></p>
<?php } ?>
<div class="clear"></div>
<script type="text/javascript">
	$('#show_recommend_spike .demo').ajaxContent({
		target:'#show_recommend_spike_list'
	});
</script>