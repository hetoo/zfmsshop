<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if (!empty($output['list'])) { ?>

<ul>
  <?php foreach($output['list'] as $k => $v) { ?>
  <li>
    <label>
      <input type="radio" <?php echo $_GET['chain_id'] == $v['chain_id'] ? 'checked' : null; ?> nctype="dlyp_radio" data_area="<?php echo str_replace(' ', '', $v['area_info']).$v['chain_address'];?>" value="<?php echo $v['chain_id'];?>" name="chain_id">
      <?php echo $v['chain_name'] ? $v['chain_name'].'，' : '';?>
      <?php echo $v['chain_address'];?>，
      电话：<?php echo $v['chain_phone'];?>，
      代收费用：<?php echo ncPriceFormat($v['collection_price']);?></label>
    <div class="delivery-map"></div>
  </li>
  <?php } ?>
</ul>
<div class="pagination"> <?php echo $output['show_page'];?> </div>
<?php } else { ?>
<div class="no-delivery">该地区下还没有门店！</div>
<?php } ?>
<script type="text/javascript">
$(document).ready(function(){
	$('input[nctype="dlyp_radio"]').on('click',function(){
		$('#zt_address > ul').children().removeClass('select');
		$(this).parent().parent().addClass('select');
		$('.delivery-map').html('<img height="250" width="250" src="http://api.map.baidu.com/staticimage?center=&width=250&height=250&zoom=15&markers='+$(this).attr('data_area')+'">');
	});
	if ($('input[type="radio"]:checked').val()) {
		$('input[type="radio"]:checked').parent().parent().addClass('select');
		$('.delivery-map').html('<img height="250" width="250" src="http://api.map.baidu.com/staticimage?center=&width=250&height=250&zoom=15&markers='+$('input[type="radio"]:checked').attr('data_area')+'">');
	}
	$('#zt_address').find('.demo').ajaxContent({
		event:'click', //mouseover
		loaderType:"img",
		loadingMsg:"<?php echo MEMBER_TEMPLATES_URL;?>/images/transparent.gif",
		target:'#zt_address'
	});
});
</script>