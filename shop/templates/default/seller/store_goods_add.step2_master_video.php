<div class="goods-gallery add-step2"><a class='sample_demo' id="select_submit" href="index.php?act=store_video&op=video_list&item=goods" style="display:none;"><?php echo $lang['nc_submit'];?></a>
  <div class="nav"><span class="l"><?php echo '用户视频';?> >
    <?php if(isset($output['class_name']) && $output['class_name'] != ''){echo $output['class_name'];}else{?>
    <?php echo '全部视频';?>
    <?php }?>
    </span><span class="r">
    <select name="jumpMenu" id="jumpMenu" style="width:100px;">
      <option value="0" style="width:80px;"><?php echo '请选择';?></option>
      <?php foreach($output['class_list'] as $val) { ?>
      <option style="width:80px;" value="<?php echo $val['video_class_id']; ?>" <?php if($val['video_class_id']==$_GET['id']){echo 'selected';}?>><?php echo $val['video_class_name']; ?></option>
      <?php } ?>
    </select>
    </span></div>
  <?php if(!empty($output['video_list'])){?>
  <ul class="list">
    <?php foreach ($output['video_list'] as $v){?>
    <li onclick="insert_video('<?php echo $v['video_cover'];?>','<?php echo goodsVideoPath($v['video_cover'] , $v['store_id']);?>');"><a href="JavaScript:void(0);"><video src="<?php echo goodsVideoPath($v['video_cover'], $v['store_id']);?>" title='<?php echo $v['video_name']?>'></video></a></li>
    <?php }?>
  </ul>
  <?php }else{?>
  <div class="warning-option"><i class="icon-warning-sign"></i><span>空间中暂无视频</span></div>
  <?php }?>
  <div class="pagination"><?php echo $output['show_page']; ?></div>
</div>
<script>
$(document).ready(function(){
	$('#video_demo .demo').ajaxContent({
		event:'click', //mouseover
		loaderType:'img',
		loadingMsg:'<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif',
		target:'#video_demo'
	});
	$('#jumpMenu').change(function(){
		$('#select_submit').attr('href',$('#select_submit').attr('href')+"&id="+$('#jumpMenu').val());
		$('.sample_demo').ajaxContent({
			event:'click', //mouseover
			loaderType:'img',
			loadingMsg:'<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif',
			target:'#video_demo'
		});
		$('#select_submit').click();
	});
});
</script>