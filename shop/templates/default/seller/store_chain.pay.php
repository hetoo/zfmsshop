<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form action="index.php?act=store_chain&op=pay_save" method="POST" id="form_paying_money_certificate">
    <input type="hidden" id="payment_code" name="payment_code" value="">
    <input type="hidden" id="chain_id" name="chain_id" value="<?php echo $output['chain_info']['chain_id']?>">
    <input type="hidden" id="member_id" name="member_id" value="<?php echo $output['store_info']['member_id']?>">
    <input type="hidden" id="member_name" name="member_name" value="<?php echo $output['store_info']['member_name']?>">
    <div class="ncc-receipt-info">
      <div class="ncc-receipt-info-title">
        <h3>请尽快完成门店保证金支付，以免影响您门店功能的正常使用,
        <?php echo $output['earnest_money'] > 0 ? "应付金额：<strong style='color:#f00'>".ncPriceFormat($output['earnest_money'])."</strong>元" : null;?></h3>
      </div>
    </div>
    <?php if (!empty($output['payment_list'])) {?>
    <div class="ncc-receipt-info">
        <div class="ncc-receipt-info-title">
          <h3>选择支付方式:</h3>
        </div>
        <ul class="ncc-payment-list">
          <?php foreach($output['payment_list'] as $val) { ?>
          <li payment_code="<?php echo $val['payment_code']; ?>">
            <label for="pay_<?php echo $val['payment_code']; ?>">
            <i></i>
            <div class="logo" for="pay_<?php echo $val['payment_id']; ?>"> <img src="<?php echo SHOP_TEMPLATES_URL?>/images/payment/<?php echo $val['payment_code']; ?>_logo.gif" /> </div>
            </label>
          </li>
          <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <?php if ($output['pay']['pay_amount_online'] > 0) {?>
    <div class="ncc-bottom"><a href="javascript:void(0);" id="next_button" class="pay-btn"><i class="icon-shield"></i>确认支付</a></div>
    <?php }?>
  </form>
  <div class="ncc-bottom"><a href="javascript:void(0);" id="next_button" class="pay-btn"><i class="icon-shield"></i>确认支付</a></div>
</div>
<script type="text/javascript">
	$(function(){
		$('.ncc-payment-list > li').on('click',function(){
          $('.ncc-payment-list > li').removeClass('using');
          var pay_code = $(this).attr('payment_code');
          if ($('#payment_code').val() != pay_code) {
            $('#payment_code').val(pay_code);
            $(this).addClass('using');
          } else {
            $('#payment_code').val('');
          }
        });
        $('#next_button').on('click',function(){
            if ($('#payment_code').val() == '') {
              showDialog('请选择一种在线支付方式', 'error','','','','','','','',2);
              return;
            }
            $('#form_paying_money_certificate').submit();
        });
	});
</script>