<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="alert alert-block mt10">
  <ul>
    <li>1、门店结算的订单包括门店自提、门店配送、门店发货的三种订单</li>
    <li>2、每天处理生成一次账单，不同的门店可设置不同结算周期</li>
 </ul>
</div>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="store_chain_bill" />
    <input type="hidden" name="op" value="index" />
    <tr>
      <td></td>
      <th><select name="search_type">
          <option <?php if ($_GET['search_type'] == 'chain_name') {?>selected<?php } ?> value="chain_name">门店名称</option>
          <option <?php if ($_GET['search_type'] == 'ob_id') {?>selected<?php } ?> value="ob_id">结算单号</option>
        </select></th>
      <td class="w160"><input type="text" class="text w150" name="keyword" value="<?php echo $_GET['keyword']; ?>" /></td>
      <td class="w70 tc"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_common_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w10"></th>
      <th>结算单号</th>
      <th class="w150">门店名称</th>
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
      <td><a href="<?php echo urlShop('show_chain', 'index', array('chain_id' => $bill_info['ob_chain_id']));?>" target="_blank"><?php echo $bill_info['ob_chain_name'];?></a></td>
      <td><?php echo date('Y-m-d',$bill_info['ob_start_date']).' - '.date('Y-m-d',$bill_info['ob_end_date']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_result_totals']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_order_totals']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_commis_totals']);?></td>
      <td><?php echo ncPriceFormat($bill_info['ob_order_return_totals']);?></td>
      <td><?php echo date('Y-m-d',$bill_info['ob_create_date']);?></td>
      <td><a href="index.php?act=store_chain_bill&op=show_bill&ob_id=<?php echo $bill_info['ob_id'];?>"><?php echo $lang['nc_view'];?></a></td>
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