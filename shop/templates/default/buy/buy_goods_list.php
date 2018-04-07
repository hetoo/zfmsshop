<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="ncc-receipt-info">
  <div class="ncc-receipt-info-title">
    <h3>商品清单</h3>
  </div>
  <table class="ncc-table-style">
    <thead>
      <tr>
        <th class="w50"></th>
        <th></th>
        <th><?php echo $lang['cart_index_store_goods'];?></th>
        <th class="w150"><?php echo $lang['cart_index_price'].'('.$lang['currency_zh'].')';?></th>
        <th class="w100"><?php echo $lang['cart_index_amount'];?></th>
        <th class="w150"><?php echo $lang['cart_index_sum'].'('.$lang['currency_zh'].')';?></th>
      </tr>
    </thead>
    <tbody id="jjg-valid-skus-tpl" style="display:none;">
      <tr class="bundling-list">
        <td class="tree td-border-left"><input name="jjg[]" type="hidden" value="%jjgId%|%jjgLevel%|%id%" /></td>
        <td><a class="ncc-goods-thumb" href="%url%" target="_blank"> <img alt="%name%" data-src="%imgUrl%" /> </a></td>
        <td class="tl"><dl class="ncc-goods-info">
            <dt> <a href="%url%" target="_blank">%name%</a> </dt>
            <dd class="ncc-goods-gift"><span>已选换购</span></dd>
          </dl></td>
        <td><em class="goods-price">%jjgPrice%</em></td>
        <td>1</td>
        <td class="td-border-right"><em nc_type="eachGoodsTotal" class="goods-subtotal"> %jjgPrice% </em></td>
      </tr>
    </tbody>
    <?php include template('buy/buy_store_cart_list');?><tbody id="buy_store_cart_list"></tbody>
      <?php if (!empty($output['order_date_list']) && is_array($output['order_date_list'])) { ?>
    <tbody>
      <tr>
        <td colspan="20" style="border-top-width: 0px;">
            <div class="ncc-store-account">
            <dl>
                <dt class="w100">希望配送日期：</dt>
                <dd style="float: right">
                <select name="reciver_date_msg" class="select">
                    <?php foreach($output['order_date_list'] as $k=>$v){ ?>
                    <?php if(!empty($v)){?><option value="<?php echo $v;?>"><?php echo $v;?></option><?php }?>
                    <?php }?>
                </select></dd>
            </dl>
            <dl class="clear">
                <dd style="float: right">
                <?php echo $output['order_date_msg'];?></dd>
            </dl>
            </div>
      </td>
      </tr>
    </tbody>
      <?php }?>
    <tfoot id="cart_tfoot">
      <!-- S rpt list -->
      <tr id="rpt_panel" style="display: none">
        <td class="pd-account" colspan="20"><div class="ncc-store-account"><dl><dt>平台红包：</dt><dd class="rule">
            <select nctype="rpt" id="rpt" name="rpt" class="select">
            </select>
            <dd class="sum"><em id="orderRpt" class="subtract">-0.00</em></dd></dl></div></td>
      </tr>
      <!-- E rpt list -->
      <tr>
        <td colspan="20"><?php if(!empty($output['ifcart'])){?>
          <a href="index.php?act=cart" class="ncc-prev-btn"><i class="icon-angle-left"></i><?php echo $lang['cart_step1_back_to_cart'];?></a>
          <?php }?>
          <div class="ncc-all-account">订单总金额：<em id="orderTotal">....</font></em><?php echo $lang['currency_zh'];?></div>
          <a href="javascript:void(0)" id='submitOrder' class="ncc-next-submit"><?php echo $lang['cart_index_submit_order'];?></a></td>
      </tr>
    </tfoot>
  </table>
</div>
<script>
function submitNext(){
	if (!SUBMIT_FORM) return;

	if ($('input[name="cart_id[]"]').size() == 0) {
		showDialog('所购商品无效', 'error','','','','','','','','',2);
		return;
	}
    if ($('#address_id').val() == ''){
		showDialog('<?php echo $lang['cart_step1_please_set_address'];?>', 'error','','','','','','','','',2);
		return;
	}
	if ($('#buy_city_id').val() == '') {
		showDialog('正在计算运费,请稍后！', 'error','','','','','','','','',2);
		return;
	}
	if ($('input[name="fcode"]').size() == 1 && $('#fcode_callback').val() != '1') {
		showDialog('请输入并使用F码！', 'error','','','','','','','','',2);
		return;
	}
	if (no_send_tpl_ids.length > 0 || no_chain_goods_ids.length > 0) {
		showDialog('有部分商品配送范围无法覆盖您选择的地址，请更换其它商品！', 'error','','','','','','','','',4);
		return;
	}
	SUBMIT_FORM = false;
 	$('#order_form').submit();
}

//计算总运费和每个店铺小计
function calcOrder() {
    allTotal = 0;
    $('em[nc_type="eachStoreTotal"]').each(function(){
        store_id = $(this).attr('store_id');
        var eachTotal = 0;
        $('em[nc_type="eachGoodsTotal'+store_id+'"]').each(function(){
        	if (no_send_tpl_ids[$(this).attr('tpl_id')]) {
     		    $(this).next().show();
     		    $('#cart_item_'+$(this).attr('cart_id')).addClass('item_disabled');
     		} else {
         		if (no_chain_goods_ids[$(this).attr('goods_id')]){
         		    $(this).next().show();
         		    $('#cart_item_'+$(this).attr('cart_id')).addClass('item_disabled');
             	} else {
         		    $(this).next().hide();
           		    $('#cart_item_'+$(this).attr('cart_id')).removeClass('item_disabled');
                }
     		}
        });
        if ($('#eachStoreGoodsTotal_'+store_id).length > 0) {
        	eachTotal += parseFloat($('#eachStoreGoodsTotal_'+store_id).html());
	    }
        if ($('#eachStoreManSong_'+store_id).length > 0) {
        	eachTotal += parseFloat($('#eachStoreManSong_'+store_id).html());
	    }
        if ($('#eachStoreVoucher_'+store_id).length > 0) {
        	eachTotal += parseFloat($('#eachStoreVoucher_'+store_id).html());
        }
        if ($('#eachStoreFreight_'+store_id).length > 0) {
        	eachTotal += parseFloat($('#eachStoreFreight_'+store_id).html());
	    }
        allTotal += eachTotal;
        $(this).html(eachTotal.toFixed(2));
    });
    
    if ($('#orderRpt').length > 0) {
    	iniRpt(allTotal.toFixed(2));
    	$('#orderRpt').html('-0.00');
    }
    $('#orderTotal').html(allTotal.toFixed(2));
    if ($('#payment_list').css('display') == 'none') $('#submitOrder').on('click',function(){submitNext()}).addClass('ok');
}
$(function() {
    var tpl = $('#jjg-valid-skus-tpl').html();
    var jjgValidSkus = <?php echo json_encode($output['jjgValidSkus']); ?>;

    $footers = {};
    $('[data-jjg]').each(function() {
        var id = $(this).attr('data-jjg');
        if (!$footers[id]) {
            var $footer = $('<tr><td colspan="20"></td></tr>');
            $footers[id] = $footer;
            $("tr[data-jjg='"+id+"']:last").after($footer);
        }
    });

    $.each(jjgValidSkus || {}, function(k, v) {
        $.each(v || {}, function(kk, vv) {
            var s = tpl.replace(/%(\w+)%/g, function($m, $1) {
                return vv[$1];
            });
            $footers[k].before(s);
            var $s = $footers[k].prev();
            $s.find('img[data-src]').each(function() {
                this.src = $(this).attr('data-src');
            });
        });
    });
});

</script> 
