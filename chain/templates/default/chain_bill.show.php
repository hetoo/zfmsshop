<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.bill-alert-block {
    padding-bottom: 14px;
    padding-top: 14px;
}
.bill_alert {
    background-color: #F9FAFC;
    border: 1px solid #F1F1F1;
    margin-bottom: 20px;
    padding: 8px 35px 8px 14px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
	line-height:30px;
}
</style>
  <div class="bill_alert bill-alert-block mt10">
    <div style="width:800px"><h3 style="float:left"></h3>
    <div style="clear:both"></div>
    </div>
    <ul>
      <li>结算单号：<?php echo $output['bill_info']['ob_id'];?>&emsp;
      <?php echo date('Y-m-d',$output['bill_info']['ob_start_date']);?> &nbsp;至&nbsp; <?php echo date('Y-m-d',$output['bill_info']['ob_end_date']);?></li>
      <li>出账时间：<?php echo date('Y-m-d',$output['bill_info']['ob_create_date']);?></li>
      <li>本期应结：<?php echo ncPriceFormat($output['bill_info']['ob_result_totals']);?> = <?php echo ncPriceFormat($output['bill_info']['ob_order_totals']);?> (订单金额) - <?php echo ncPriceFormat($output['bill_info']['ob_commis_totals']);?> (平台佣金) - <?php echo ncPriceFormat($output['bill_info']['ob_order_return_totals']);?> (退单金额) + <?php echo ncPriceFormat($output['bill_info']['ob_commis_return_totals']);?> (退还佣金)
      <?php if (floatval($output['bill_info']['ob_offline_totals']) > 0) { ?>
      - <?php echo ncPriceFormat($output['bill_info']['ob_offline_totals']);?> (线下支付金额)
      <?php } ?>
      <?php if (floatval($output['bill_info']['ob_rpt_amount']) > 0) { ?>
      + <?php echo ncPriceFormat($output['bill_info']['ob_rpt_amount']);?> (平台红包)
      <?php } ?>
      <?php if (floatval($output['bill_info']['ob_rf_rpt_amount']) > 0) { ?>
      - <?php echo ncPriceFormat($output['bill_info']['ob_rf_rpt_amount']);?> (全部退款时应扣除的平台红包)
      <?php } ?>
      </li>
      </li>
    </ul>
  </div>
  <div class="tabmenu">
  	<?php include template('layout/submenu');?>
  </div>
<?php include template($output['sub_tpl_name']);?>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/themes/ui-lightness/jquery.ui.css";?>"/>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8" ></script> 
<script type="text/javascript">
$(document).ready(function(){
	$('#query_start_date').datepicker();
	$('#query_end_date').datepicker();
});
</script>