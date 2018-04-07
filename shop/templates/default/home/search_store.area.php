<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.section .cancel-btn{
	color: #fff;
	display: block;
	float: left;
	height: 23px;
	padding: 0 10px;
	background: #f32613;
	line-height: 23px;
	cursor: pointer;
	-webkit-border-radius: 2px;
	-webkit-background-clip: padding-box;
	-moz-border-radius: 2px;
	-moz-background-clip: padding;
	border-radius: 2px;
	background-clip: padding-box;
}
</style>
<?php if($_GET['area_id'] > 0){?>
<div class="section" style="border-top: none;">
	<div style="margin: 8px 17px 0;">
		<a id="area_cancel" class="btn cancel-btn">取消选择</a>
	</div>
</div>
<?php }?>
<?php foreach($output['region_areas'] as $key => $val){?>
<div class="section">
	<div class="region"><?php echo $key;?>:</div>
	<ul>
		<?php foreach($val as $v){?>
		<li><a href="<?php echo replaceParam(array('area_id' => $v));?>"><?php echo $output['name_areas'][$v];?></a></li>
		<?php }?>
	</ul>
</div>
<?php }?>
<?php if($_GET['area_id'] > 0){?>
<script type="text/javascript">
	$('#area_cancel').click(function(){
		window.location.href="<?php echo replaceParam(array('area_id' => 0));?>";
	});
</script>
<?php }?>