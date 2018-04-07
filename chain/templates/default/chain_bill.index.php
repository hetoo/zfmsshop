<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="alert alert-block mt10">
  <ul>
    <li>1、门店结算的订单包括门店自提、门店配送、门店发货的三种订单</li>
    <li>2、每天处理生成一次账单，当前结算周期是：<?php echo $output['chain_info']['chain_cycle'];?>天</li>
 </ul>
</div>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="chain_bill" />
    <input type="hidden" name="op" value="index" />
    <tr>
      <td></td>
      <th>结算单号</th>
      <td class="w160"><input type="text" class="text w150" name="ob_id" value="<?php echo $_GET['ob_id']; ?>" /></td>
      <td class="w70 tc"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w10"></th>
      <th>结算单号</th>
      <th>起止时间</th>
      <th class="w90">应结金额</th>
      <th>订单金额</th>
      <th>平台佣金</th>
      <th>退单金额</th>
      <th>账单生成日期</th>
      <th class="w120"><?php echo $lang['nc_handle'];?></th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['bill_list']) && is_array($output['bill_list'])) { ?>
    <?php foreach($output['bill_list'] as $bill_info) { ?>
    <tr class="bd-line">
      <td></td>
      <td><?php echo $bill_info['ob_id'];?></td>
      <td><?php echo date('Y-m-d',$bill_info['ob_start_date']).' - '.date('Y-m-d',$bill_info['ob_end_date']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_result_totals']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_order_totals']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_commis_totals']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_order_return_totals']);?></td>
      <td><?php echo date('Y-m-d',$bill_info['ob_create_date']);?></td>
      <td><a href="index.php?act=chain_bill&op=show_bill&ob_id=<?php echo $bill_info['ob_id'];?>">查看</a></td>
    </tr>
    <?php }?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (!empty($output['bill_list']) && is_array($output['bill_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>