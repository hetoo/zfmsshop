<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="alert alert-block mt10">
  <ul class="mt5">
    <li>1、根据线上在售商品列表内容设置门店库存量；门店库存默认值为“0”时，该商品详情页面“门店自提”选项将不会出现您的门店信息，只有当您按所在门店的实际库存情况与线上商品对照设置库存后，才可作为线上销售门店自取点候选。</li>
    <li>2、选择“库存设置”按钮，如该商品具有多项规格值，请根据规格值内容进行逐一“门店库存”设置，并保存提交。</li>
    <li>3、如您的门店某商品线下销售引起库存不足，请及时手动调整该商品的库存量，以免消费者在线上下单后到门店自提时产生交易纠纷。</li>
    <li>4、特殊商品不能设置为门店自提商品（如：虚拟商品、定金预售商品、F码商品等）</li>
  </ul>
</div>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
    <a href="javascript:void(0);" nctype="add_class" class="ncbtn ncbtn-mint">添加分类</a>
</div>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
      <th>&nbsp;</th>
      <th class="w120 tl">分类名称</th>
      <th class="w150">显示状态</th>
      <th class="w150">分类排序</th>
      <th class="tc">操作</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['class_list'])) { ?>
    <?php foreach ($output['class_list'] as $val) { ?>
    <tr>
      <td></td>
      <td class=" tl"><?php echo $val['class_name'];?></td>
      <td style="color: <?php echo $val['class_state']?'#1BBC9D':'#BEC3C7';?>"><?php echo $val['class_state']?'<i class="icon-ok-sign"></i> 显示':'<i class="icon-ban-circle"></i> 不显示';?></td>
      <td><?php echo $val['class_sort'];?></td>
      <td class="nscs-table-handle">        
        <span>
          <a href="javascript:void(0);" class="btn-bluejeans" nctype="show_class" data-classid="<?php echo $val['class_id'];?>">
            <i class="icon-list"></i>
            <p>查看下级</p>
          </a>
        </span>
        <span>
          <a href="javascript:void(0);" class="btn-bluejeans" nctype="edit_class" data-classid="<?php echo $val['class_id'];?>">
            <i class="icon-edit"></i>
            <p>编辑</p>
          </a>
        </span>
        <span>
          <a href="javascript:void(0);" class="btn-bluejeans" nctype="del_class" data-classid="<?php echo $val['class_id'];?>">
            <i class="icon-trash"></i>
            <p>删除</p>
          </a>
        </span>
      </td>
    </tr>
    <tr style="display:none;">
      <td colspan="20" style="border:none;"><div class="ncsc-class-list ps-container"></div></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php  if (!empty($output['class_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script> 
<script type="text/javascript">
$(function(){
    $('.tabmenu').on('click','a[nctype="add_class"]',function(){
      ajax_form('add_class', '添加分类', '<?php echo urlChain('goods', 'add_class');?>' , '600');
    });
    $('.ncsc-default-table').on('click','a[nctype="edit_class"]',function(){
      var class_id = $(this).attr('data-classid');
      ajax_form('edit_class', '编辑分类', '<?php echo urlChain('goods', 'edit_class');?>&class_id='+class_id , '600');
    });
    $('a[nctype="show_class"]').click(function(){
      var class_id = $(this).attr('data-classid');
      var url = "<?php echo urlChain('goods', 'child_class');?>&class_id="+class_id;
      var obj = $(this).parents('tr').next();
      obj.find('.ncsc-class-list').load(url,function(){
        var c_html = obj.find('.ncsc-class-list').html();
        if(c_html.length > 0){
          obj.show();
        }else{
          alert('该分类没有下级分类');
        }
      }); 
      return false;     
    });
    $('.ncsc-default-table').on('click','a[nctype="del_class"]',function(){
      var class_id = $(this).attr('data-classid');
      var url = "<?php echo urlChain('goods', 'del_class');?>&class_id="+class_id;
      ajax_confirm('确认要删除吗？',url);
    });

});
</script>
