<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_chain', 'chain_add');?>" class="ncbtn ncbtn-mint" title="添加门店"><i class="icon-plus-sign"></i>添加门店</a>
  <a href="<?php echo CHAIN_SITE_URL;?>" class="ncbtn ncbtn-aqua" style="right:90px" target="_blank" title="进入门店系统"><i class="icon-building"></i>进入门店系统</a>
</div>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w200 tl">门店名称</th>
      <th class="w200">所在地区</th>
      <th class="tc ">门店地址</th>
      <th class="w200"><?php echo $lang['nc_handle'];?></th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['chain_list'])) { ?>
    <?php foreach($output['chain_list'] as $val) { ?>
    <tr class="bd-line">
      <td class="tl"><a href="<?php echo urlShop('show_chain', 'index', array('chain_id' => $val['chain_id']));?>" target="_blank"><?php echo $val['chain_name'];?></a></td>
      <td><?php echo $val['area_info'];?></td>
      <td><?php echo $val['chain_address'];?></td>
      <td class="nscs-table-handle">
        <?php if(in_array($val['chain_state'], array(4,6))){?>
          <span><a href="<?php echo urlShop('store_chain', 'chain_pay', array('chain_id' => $val['chain_id']));?>" class="btn-grapefruit"><i class="icon-shopping-cart"></i><p>去付款</p></a></span>
        <?php }?>
        <?php if(in_array($val['chain_state'], array(0,1,2,3))){?>
          <span><a href="<?php echo urlShop('store_chain', 'chain_edit', array('chain_id' => $val['chain_id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <?php }?>
        <?php if(!in_array($val['chain_state'], array(0,5))){?>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('确认要关闭门店吗？', '<?php echo urlShop('store_chain', 'chain_close', array('chain_id' => $val['chain_id']));?>');" class="btn-grapefruit"><i class="icon-ban-circle"></i><p>关闭</p></a></span>
        <?php }?>
        <?php if(in_array($val['chain_state'], array(0,3))){?>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('store_chain', 'chain_del', array('chain_id' => $val['chain_id']));?>');" class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a></span>
        <?php }?>
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
    <?php if (!empty($output['chain_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
