<?php defined('InShopNC') or exit('Access Invalid!');?>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/themes/ui-lightness/jquery.ui.css";?>"/>
<div class="alert alert-block mt10">
  <ul class="mt5">
    <li>该列表可以查看门店所有代金券及添加门店代金券、编辑门店代金券、删除门店代金券等操作。</li>
  </ul>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="voucher" />
    <input type="hidden" name="op" value="index" />
    <tr>
      <td>
        <a href="index.php?act=voucher&op=templateadd" class="ncbtn ncbtn-mint">创建代金券</a>
      </td>
      <th>有效期</th>
      <td class="w240" style="line-height: 25px;">
        <input class="text w70" readonly="readonly" value="<?php echo $_GET['start_time']?>" id="start_time" name="start_time" type="text">
        <label class="add-on" style="margin-left: -4px;"> <i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;
        <input class="text w70" readonly="readonly" value="<?php echo $_GET['end_time']?>" id="end_time" name="end_time" type="text">
        <label class="add-on" style="margin-left: -4px;"> <i class="icon-calendar"></i></label>
      </td>
      <th>状态</th>
      <td class="w90"><select name="search_state">
          <option value="0" <?php if ($_GET['search_state'] == '0') {?>selected<?php }?>>请选择</option>
          <option value="1" <?php if ($_GET['search_state'] == '1') {?>selected<?php }?>>有效</option>
          <option value="2" <?php if ($_GET['search_state'] == '2') {?>selected<?php }?>>失效</option>
        </select></td>
      <td class="w65">代金券名称</td>
      <td class="w120"><input type="text" class="text w150" name="keyword" value="<?php echo $_GET['keyword']; ?>"/></td>
      <td class="tc w80">
        <label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label>
      </td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
    <th class="w20"></th>
      <th>代金券名称</th>
      <th class="w70">面额（元）</th>
      <th class="w90">消费金额（元）</th>
      <th class="w60">每人限领</th>
      <th class="w90">开始时间</th>
      <th class="w90">结束时间</th>
      <th class="w80">已领</th>
      <th class="w80">已用</th>
      <th class="w40">状态</th>
      <th class="w40">推荐</th>
      <th class="w100">操作</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['voucher_list']) && is_array($output['voucher_list'])) { ?>
    <?php foreach ($output['voucher_list'] as $voucher_info) { ?>
    <tr>
    <td class="bdl"></td>
      <td><?php echo $voucher_info['voucher_t_title'];?></td>
      <td><em class="goods-price"><?php echo ncPriceFormat($voucher_info['voucher_t_price']);?></em></td>
      <td><em class="goods-price"><?php echo ncPriceFormat($voucher_info['voucher_t_limit']);?></em></td>
      <td><?php echo $voucher_info['voucher_t_eachlimit']>0?$voucher_info['voucher_t_eachlimit'].'&nbsp;张':'不限';?></td>
      <td><?php echo date('Y-m-d',$voucher_info['voucher_t_start_date']);?></td>
      <td><?php echo date('Y-m-d',$voucher_info['voucher_t_end_date']);?></td>
      <td><?php echo $voucher_info['voucher_t_giveout'];?></td>
      <td><?php echo $voucher_info['voucher_t_used'];?></td>
      <td><?php echo $voucher_info['voucher_t_state_text'];?></td>
      <td><?php echo $voucher_info['voucher_t_recommend']?'是':'否';?></td>
      <td>
        <?php if($voucher_info['voucher_t_giveout'] <= 0){?>
        <a href="index.php?act=voucher&op=templateedit&tid=<?php echo $voucher_info['voucher_t_id'];?>" class="btn icon-edit-sign mr10 no_unl"><i>&nbsp;</i>编辑</a>&nbsp;
        <?php }?>
        <a href="javascript:void(0);" data-id="<?php echo $voucher_info['voucher_t_id'];?>" class="voucher_del_btn btn icon-remove no_unl"><i>&nbsp;</i>删除</a>
      </td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php  if (!empty($output['voucher_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script type="text/javascript">
  $('#start_time').datepicker({dateFormat: 'yy-mm-dd'});
  $('#end_time').datepicker({dateFormat: 'yy-mm-dd'});
  $('.voucher_del_btn').click(function(){
    var tid = $(this).attr('data-id');
    var uri = "<?php echo urlChain('voucher','templatedel');?>";
    ajax_get_confirm('您确定要删除吗?删除后已领取的代金券可以继续使用',uri+'&tid='+tid);
  });
</script>
