<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="eject_con">
  <?php if ($output['error']) {?>
  <div class="chain-error">参数错误</div>
  <?php } else {?>
  <form method="post" id="class_form" action="<?php echo urlChain('goods', 'save_class');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>" />
    <div class="ncsc-form-default">
      <dl>
        <dt><i class="required">*</i><?php echo '分类名称'.$lang['nc_colon']; ?></dt>
        <dd>
          <input type="text" class="w200 text" name="class_name" value="<?php echo $output['class_info']['class_name'];?>" maxlength=20 />
          <span></span>
        </dd>
      </dl>
      <dl>
        <dt><i class="required">*</i><?php echo '上级分类'.$lang['nc_colon']; ?></dt>
        <dd>
          <select name="class_parent_id">
            <option value="0">—请选择—</option>
            <?php foreach((array)$output['class_list'] as $val){?>
              <option value="<?php echo $val['class_id']?>" <?php echo $val['class_id'] == $output['class_info']['class_parent_id'] ? 'selected="selected"' : ''; ?> ><?php echo $val['class_name']?></option>
            <?php }?>
          </select>
          <span></span>
        </dd>
      </dl>
      <dl>
        <dt><i class="required">*</i><?php echo '排序'.$lang['nc_colon']; ?></dt>
        <dd>
          <input type="text" class="w80 text" name="class_sort" value="<?php echo intval($output['class_info']['class_sort'])>0?intval($output['class_info']['class_sort']):0;?>" />
          <span></span>
        </dd>
      </dl>
      <dl>
        <dt><?php echo '是否显示'.$lang['nc_colon']; ?></dt>
        <dd>
          <input type="radio" name="class_state" value="1" <?php echo intval($output['class_info']['class_state'])>0 ? 'checked="checked"' :"";?>> 是
          &nbsp;&nbsp;&nbsp;&nbsp;
          <input type="radio" name="class_state" value="0" <?php echo intval($output['class_info']['class_state'])<=0 ? 'checked="checked"' :"";?>> 否
          <span></span>
        </dd>
      </dl>      
    </div>
    <div class="bottom">
      <label class="submit-border">
        <input type="submit" class="submit" value="提交"/>
      </label>
    </div>
  </form>
  <?php }?>
</div>
<script>
$(function(){
    $('#class_form').validate({
        errorPlacement: function(error, element){
          var error_td = element.parent('dd').children('span');
          error_td.append(error);
        },
        submitHandler:function(form){
            ajaxpost('class_form', '', '', 'onerror');
        },
        rules : {
            class_name : {
                required : true,
            },
            class_sort  : {
                required : true,
                min : 0,
                max : 255,
                digits  : true
            }
        },
        messages : {
            class_name : {
                required : '<i class="icon-exclamation-sign"></i>请填写分类名称'
            },
            class_sort  : {
                required : '<i class="icon-exclamation-sign"></i>排序为0~255的整数',
                min : '<i class="icon-exclamation-sign"></i>排序为0~255的整数',
                max : '<i class="icon-exclamation-sign"></i>排序为0~255的整数',
                digits  : '<i class="icon-exclamation-sign"></i>排序为0~255的整数'
            }
        }
    });
});
</script> 