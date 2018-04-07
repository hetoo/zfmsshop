<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
<?php if ($output['order_info']) {?>

  <form id="changeform" method="post" action="index.php?act=store_order&op=change_state&state_type=modify_price&order_id=<?php echo $output['order_info']['order_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt style="width: 49%"><?php echo $lang['store_order_buyer_with'].$lang['nc_colon'];?></dt>
      <dd style="width: 49%"><?php echo $output['order_info']['buyer_name']; ?></dd>
    </dl>
    <dl>
      <dt style="width: 49%"><?php echo $lang['store_order_sn'].$lang['nc_colon'];?></dt>
      <dd style="width: 49%"><span class="num"><?php echo $output['order_info']['order_sn']; ?></span></dd>
    </dl>
    <dl>
      <dt style="width: 49%">运费金额<?php echo $lang['nc_colon'];?></dt>
      <dd style="width: 49%">
        <input type="text" class="text w50" id="shipping_fee" name="shipping_fee" value="<?php echo $output['order_info']['shipping_fee']; ?>"/>
        <p class="hint">运费可以提高也可减少，金额最小为0</p>
      </dd>
    </dl>
        <?php if (is_array($output['order_info']['extend_order_goods']) && !empty($output['order_info']['extend_order_goods'])) { ?>
        <?php foreach ($output['order_info']['extend_order_goods'] as $key => $val) { ?>
        <?php if ($val['goods_pay_price']>0.01) {?>
    <dl>
      <dt style="width: 49%">[<?php echo str_cut($val['goods_name'],25); ?>]优惠金额<?php echo $lang['nc_colon'];?></dt>
      <dd style="width: 49%">
        <input type="text" class="text w50" id="goods_<?php echo $val['rec_id']; ?>" name="goods_<?php echo $val['rec_id']; ?>" value="0"/>
        <p class="hint">最大优惠金额为<?php echo ncPriceFormat($val['goods_pay_price']-0.01); ?></p>
      </dd>
    </dl>
        <?php } ?>
        <?php } ?>
        <?php } ?>
    <dl class="bottom">
      <dt style="width: 39%">&nbsp;</dt>
      <dd style="width: 59%">
        <input type="submit" class="submit" id="confirm_button" value="<?php echo $lang['nc_ok'];?>" />
      </dd>
    </dl>
  </form>
<?php } else { ?>
<p style="line-height:80px;text-align:center">该订单并不存在，请检查参数是否正确!</p>
<?php } ?>
</div>
<script type="text/javascript">
$(function(){
    $('#changeform').validate({
    	errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
           var errors = validator.numberOfInvalids();
           if(errors){ $('#warning').show();}else{ $('#warning').hide(); }
        },
     	submitHandler:function(form){
    		ajaxpost('changeform', '', '', 'onerror'); 
    	},    
	    rules : {
        	shipping_fee : {
	            number : true,
                min:0,
	        },
        <?php if (is_array($output['order_info']['extend_order_goods']) && !empty($output['order_info']['extend_order_goods'])) { ?>
        <?php foreach ($output['order_info']['extend_order_goods'] as $key => $val) { ?>
        <?php if ($val['goods_pay_price']>0.01) {?>
        	goods_<?php echo $val['rec_id']; ?> : {
	            number : true,
            	min : 0,
                max: <?php echo ncPriceFormat($val['goods_pay_price']-0.01); ?>
	        },
        <?php } ?>
        <?php } ?>
        <?php } ?>
        	goods_amount : {
	            number : true
	        }
	    },
	    messages : {
	    	shipping_fee : {
            	number : '运费金额不能为空且必须为数字',
            	min : '运费金额最小为0'
	        },
        <?php if (is_array($output['order_info']['extend_order_goods']) && !empty($output['order_info']['extend_order_goods'])) { ?>
        <?php foreach ($output['order_info']['extend_order_goods'] as $key => $val) { ?>
        <?php if ($val['goods_pay_price']>0.01) {?>
	    	goods_<?php echo $val['rec_id']; ?> : {
            	number : '商品优惠金额不能为空且必须为数字',
            	min : '商品优惠金额金额最小为0',
            	max : '商品优惠金额最大为<?php echo ncPriceFormat($val['goods_pay_price']-0.01); ?>'
	        },
        <?php } ?>
        <?php } ?>
        <?php } ?>
        	goods_amount : {
	            number : true
	        }
	    }
	});
});
</script>