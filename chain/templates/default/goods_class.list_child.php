<?php defined('InShopNC') or exit('Access Invalid!');?>
<table class="ncsc-default-table">
	<?php if (!empty($output['class_list'])) { ?>
    <?php foreach ($output['class_list'] as $val) { ?>
    <tr>
      <td></td>
      <td class="w130"><?php echo $val['class_name'];?></td>
      <td class="w150" style="color: <?php echo $val['class_state']?'#1BBC9D':'#BEC3C7';?>"><?php echo $val['class_state']?'<i class="icon-ok-sign ncbtn-aqua"></i> 显示':'<i class="icon-ban-circle ncbtn-lightgray"></i> 不显示';?></td>
      <td class="w150"><?php echo $val['class_sort'];?></td>
      <td class="nscs-table-handle">
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
    <?php } ?>    
    <?php } ?>
</table>