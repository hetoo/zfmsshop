<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <?php if ($output['isOwnShop']) { ?>
  <a class="ncbtn ncbtn-mint" href="<?php echo urlShop('store_promotion_flash', 'flash_add');?>"><i class="icon-plus-sign"></i><?php echo $lang['flash_add'];?></a>
  <?php } else { ?>
  <?php if(!empty($output['current_flash_quota'])) { ?>
  <a class="ncbtn ncbtn-mint" style="right:100px" href="<?php echo urlShop('store_promotion_flash', 'flash_add');?>"><i class="icon-plus-sign"></i><?php echo $lang['flash_add'];?></a> <a class="ncbtn ncbtn-aqua" href="<?php echo urlShop('store_promotion_flash', 'flash_quota_add');?>" title=""><i class="icon-money"></i>套餐续费</a>
  <?php } else { ?>
  <a class="ncbtn ncbtn-aqua" href="<?php echo urlShop('store_promotion_flash', 'flash_quota_add');?>" title=""><i class="icon-money"></i><?php echo $lang['flash_quota_add'];?></a>
  <?php } ?>
  <?php } ?>
</div>
<?php if ($output['isOwnShop']) { ?>
<div class="alert alert-block mt10">
  <ul>
    <li>1、点击添加活动按钮可以添加闪购活动，点击管理按钮可以对闪购活动内的商品进行管理</li>
    <li>2、点击删除按钮可以删除闪购活动</li>
  </ul>
</div>
<?php } else { ?>
<div class="alert alert-block mt10">
  <?php if(!empty($output['current_flash_quota'])) { ?>
  <strong>套餐过期时间<?php echo $lang['nc_colon'];?></strong><strong style="color:#F00;"><?php echo date('Y-m-d H:i:s', $output['current_flash_quota']['end_time']);?></strong>
  <?php } else { ?>
  <strong>当前没有可用套餐，请先购买套餐</strong>
  <?php } ?>
  <ul>
    <li><?php echo $lang['flash_explain1'];?></li>
    <li><?php echo $lang['flash_explain2'];?></li>
    <li><?php echo $lang['flash_explain3'];?></li>
    <li>4、<strong style="color: red">相关费用会在店铺的账期结算中扣除</strong>。</li>
  </ul>
</div>
<?php } ?>
<form method="get">
  <table class="search-form">
    <input type="hidden" name="act" value="store_promotion_flash" />
    <input type="hidden" name="op" value="flash_list" />
    <tr>
      <td>&nbsp;</td>
      <th>状态</th>
      <td class="w100"><select name="state">
          <?php if(is_array($output['flash_state_array'])) { ?>
          <?php foreach($output['flash_state_array'] as $key=>$val) { ?>
          <option value="<?php echo $key;?>" <?php if(intval($key) === intval($_GET['state'])) echo 'selected';?>><?php echo $val;?></option>
          <?php } ?>
          <?php } ?>
        </select></td>
      <th class="w110"><?php echo $lang['flash_name'];?></th>
      <td class="w160"><input type="text" class="text w150" name="flash_name" value="<?php echo $_GET['flash_name'];?>"/></td>
      <td class="w70 tc"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="tl"><?php echo $lang['flash_name'];?></th>
      <th class="w180"><?php echo $lang['start_time'];?></th>
      <th class="w180"><?php echo $lang['end_time'];?></th>
      <th class="w80">购买上限</th>
      <th class="w80">状态</th>
      <th class="w160"><?php echo $lang['nc_handle'];?></th>
    </tr>
  </thead>
  <?php if(!empty($output['list']) && is_array($output['list'])){?>
  <?php foreach($output['list'] as $key=>$val){?>
  <tbody id="flash_list">
    <tr class="bd-line">
      <td></td>
      <td class="tl"><dl class="goods-name">
          <dt><a href="index.php?act=flash&op=brand&flash_id=<?php echo $val['flash_id'];?>" target="_blank"><?php echo $val['flash_name'];?></a></dt>
        </dl></td>
      <td class="goods-time"><?php echo date("Y-m-d H:i",$val['start_time']);?></td>
      <td class="goods-time"><?php echo date("Y-m-d H:i",$val['end_time']);?></td>
      <td><?php echo $val['upper_limit'];?></td>
      <td><?php echo $val['flash_state_text'];?></td>
      <td class="nscs-table-handle tr"><?php if($val['editable']) { ?>
        <span> <a href="index.php?act=store_promotion_flash&op=flash_edit&flash_id=<?php echo $val['flash_id'];?>" class="btn-bluejeans"> <i class="icon-edit"></i>
        <p><?php echo $lang['nc_edit'];?></p>
        </a> </span>
        <?php } ?>
        <span> <a href="index.php?act=store_promotion_flash&op=flash_manage&flash_id=<?php echo $val['flash_id'];?>" class="btn-mint"> <i class="icon-cog"></i>
        <p><?php echo $lang['nc_manage'];?></p>
        </a> </span> <span> <a href="javascript:;" nctype="btn_del_flash" data-flash-id=<?php echo $val['flash_id'];?> class="btn-grapefruit"> <i class="icon-trash"></i>
        <p><?php echo $lang['nc_delete'];?></p>
        </a> </span></td>
    </tr>
    <?php }?>
    <?php }else{?>
    <tr id="flash_list_norecord">
      <td class="norecord" colspan="20"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php }?>
  </tbody>
  <tfoot>
    <?php if(!empty($output['list']) && is_array($output['list'])){?>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<form id="submit_form" action="" method="post" >
  <input type="hidden" id="flash_id" name="flash_id" value="">
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $('[nctype="btn_del_flash"]').on('click', function() {
            if(confirm('<?php echo $lang['nc_ensure_del'];?>')) {
                var action = "<?php echo urlShop('store_promotion_flash', 'flash_del');?>";
                var flash_id = $(this).attr('data-flash-id');
                $('#submit_form').attr('action', action);
                $('#flash_id').val(flash_id);
                ajaxpost('submit_form', '', '', 'onerror');
            }
        });
    });
</script> 
