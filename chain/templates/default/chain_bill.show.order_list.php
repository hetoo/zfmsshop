<?php defined('InShopNC') or exit('Access Invalid!');?>
  <form method="get" id="formSearch">
    <table class="search-form">
      <input type="hidden" id='act' name='act' value='chain_bill' />
      <input type="hidden" id='op' name='op' value='show_bill' />
      <input type="hidden" name='ob_id' value='<?php echo $_GET['ob_id'];?>' />
      <input type="hidden" name='type' value='<?php echo $_GET['type'];?>' />
      <tr>
        <td>&nbsp;</td>
        <th>订单编号</th>
        <td class="w180"><input type="text" class="text"  value="<?php echo $_GET['query_order_no'];?>" name="query_order_no" /></td>
        <th>成交时间</th>
        <td class="w180">
        	<input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $_GET['query_start_date']; ?>"/>
          &#8211;
          <input type="text" class="text w70" name="query_end_date" id="query_end_date" value="<?php echo $_GET['query_end_date']; ?>"/>
        </td>
        <td class="tc w200">
        <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label>
        </td>
    </table>
  </form>
<table class="ncsc-default-table">
    <thead>
      <tr>
        <th class="w10"></th>
        <th>订单编号</th>
        <th>下单时间</th>
        <th>成交时间</th>
        <th>订单金额</th>
        <th>运费</th>
        <th>平台佣金</th>
        <th>平台红包</th>
        <th>支付方式</th>
        <th>订单类型</th>
      </tr>
    </thead>
    <tbody>
      <?php if (is_array($output['order_list']) && !empty($output['order_list'])) { ?>
      <?php foreach($output['order_list'] as $order_info) { ?>
      <tr class="bd-line">
        <td></td>
        <td class="w90"><?php echo $order_info['order_sn'];?></td>
        <td><?php echo date("Y-m-d",$order_info['add_time']);?></td>
        <td><?php echo date("Y-m-d",$order_info['finnshed_time']);?></td>
        <td><?php echo ncPriceFormat($order_info['order_amount']);?></td>
        <td><?php echo ncPriceFormat($order_info['shipping_fee']);?></td>
        <td><?php echo ncPriceFormat($output['commis_list'][$order_info['order_id']]['commis_amount']);?></td>
        <td><?php echo ncPriceFormat($order_info['rpt_amount']);?></td>
        <td><?php echo orderPaymentName($order_info['payment_code']);?></td>
        <td>
       	<?php echo $output['chain_order_type'][$order_info['order_type']];?>
        </td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if (is_array($output['order_list']) && !empty($output['order_list'])) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>