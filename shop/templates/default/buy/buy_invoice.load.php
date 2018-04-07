<?php defined('InShopNC') or exit('Access Invalid!');?>
<ul>
  <?php foreach($output['inv_list'] as $k=>$val){ ?>
  <li class="inv_item <?php echo $k == 0 ? 'ncc-selected-item' : null; ?>">
    <input content="<?php echo $val['content'];?>" id="inv_<?php echo $val['inv_id']; ?>" nc_type="inv" type="radio" name="inv" value="<?php echo $val['inv_id']; ?>" <?php echo $k == 0 ? 'checked' : null; ?>/>
    <label for="inv_<?php echo $val['inv_id']; ?>">&nbsp;&nbsp;<?php echo $val['content']; ?></label>
    &emsp;&emsp;&emsp;<a href="javascript:void(0);" onclick="delInv(<?php echo $val['inv_id']?>);" class="del">[ <?php echo $lang['nc_delete'];?> ]</a> </li>
  <?php } ?>
  <li class="inv_item">
    <?php if (count($output['inv_list']) < 10) {?>
    <input value="0" nc_type="inv" id="add_inv" type="radio" name="inv">
    <label for="add_inv">&nbsp;&nbsp;使用新的发票信息</label>
    <?php } else {?>
    最多允许保存10条发票信息，请先删除部分不常用发票后再添加
    <?php }?>
  </li>
  <div id="add_inv_box" style="display:none">
    <form method="POST" id="inv_form" action="index.php">
      <input type="hidden" value="buy" name="act">
      <input type="hidden" value="add_inv" name="op">
      <input type="hidden" name="form_submit" value="ok"/>
      <div class="ncc-form-default">
        <dl>
          <dt>发票类型<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <label>
              <input type="radio" checked name="invoice_type" value="1">
              普通发票</label>
            &emsp;&emsp;
            <?php if (!$output['vat_deny']) {?>
            <label>
              <input type="radio" name="invoice_type" value="2">
              增值税发票</label>
            <?php }?>
          </dd>
        </dl>
      </div>
      <div id="invoice_panel" class="ncc-form-default">
        <dl>
          <dt>发票抬头<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select name="inv_title_select">
              <option value="person">个人</option>
              <option value="company">单位</option>
            </select>
            <input class="text w200" style="display:none" name="inv_title" id="inv_title" placeholder="单位名称" value="">
            <input class="text w200" style="display:none" name="company_code" id="company_code" placeholder="纳税人识别号" value="">
          </dd>
        </dl>
        <dl>
          <dt>发票内容<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select id="inv_content" name="inv_content">
                <?php foreach($output['inv_content_list'] as $k=>$v){ ?>
                <option value="<?php echo $v;?>"><?php echo $v;?></option>
                <?php }?>
            </select>
          </dd>
        </dl>
      </div>
      <div id="vat_invoice_panel" class="ncc-form-default" style="display:none">
        <dl>
          <dt><i class="required">*</i>单位名称<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_company" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>纳税人识别号<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_code" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>注册地址<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_reg_addr" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>注册电话<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_reg_phone" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>开户银行<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_reg_bname" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>银行账户<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_reg_baccount" value="">
          </dd>
        </dl>
        <dl>
          <dt></dt>
          <dd>如您是首次开具增值税专用发票，请您填写纳税人识别号等开票信息，并上传 加盖公章的营业执照副本、税务登记证副本、一般纳税人资格证书及银行开户 许可证扫描件邮寄给我们，收到您的开票资料后，我们会尽快审核。 </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>发票内容<?php echo $lang['nc_colon'];?></dt>
          <dd>明细</dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>收票人姓名<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_rec_name" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>收票人手机号<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_rec_mobphone" value="">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>收票人省份<?php echo $lang['nc_colon'];?></dt>
          <dd><input type="hidden" id="vregion" name="vregion">
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>送票地址<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" maxlength="50" name="inv_goto_addr" value="">
          </dd>
        </dl>
      </div>
    </form>
  </div>
</ul>
<div class="hr16"> <a id="hide_invoice_list" class="ncbtn ncbtn-grapefruit" href="javascript:void(0);"><?php echo $lang['cart_step1_invoice_submit'];?></a> <a id="cancel_invoice" class="ncbtn ml10" href="javascript:void(0);">不需要发票</a></div>
<script>
var postResult = false;
function delInv(id){
    $('#invoice_list').load(SITEURL+'/index.php?act=buy&op=load_inv&vat_hash<?php echo $_GET['vat_hash'];?>&del_id='+id);
}
$(function(){
	$('#vregion').nc_region();
    $.ajaxSetup({async : false});
    //不需要发票
    $('#cancel_invoice').on('click',function(){
        $('#invoice_id').val('');
        hideInvList('不需要发票');
    });
    //使用新的发票信息
    $('input[nc_type="inv"]').on('click',function(){
        if ($(this).val() == '0') {
            $('.inv_item').removeClass('ncc-selected-item');
            $('#add_inv_box').show();
        } else {
            $('.inv_item').removeClass('ncc-selected-item');
            $(this).parent().addClass('ncc-selected-item');
            $('#add_inv_box').hide();
        }
    });

    //保存发票信息
    $('#hide_invoice_list').on('click',function(){
        var content = '';
        if ($('input[name="inv"]:checked').size() == 0){
        	$('#cancel_invoice').click();
        	return false;
        }
        if ($('input[name="inv"]:checked').val() != '0'){
            //如果选择已保存过的发票信息
            content = $('input[name="inv"]:checked').attr('content');
            $('#invoice_id').val($('input[name="inv"]:checked').val());
            hideInvList(content);
            return false;
        }
        //如果是新增发票信息
        if ($('input[name="invoice_type"]:checked').val() == 1){
            //如果选择普通发票
            if ($('select[name="inv_title_select"]').val() == 'person'){
                content = '普通发票 个人 ' + $('select[name="inv_content"]').val();
            }
            if($('select[name="inv_title_select"]').val() == 'company'){
                if($.trim($('#inv_title').val()) == '' || $.trim($('#inv_title').val()) == '单位名称'){
                    showDialog('请填写单位名称', 'error','','','','','','','','',2);
                    return false;
                }
                if($.trim($('#company_code').val()) == '' || $.trim($('#company_code').val()) == '纳税人识别号'){
                    showDialog('请填写纳税人识别号', 'error','','','','','','','','',2);
                    return false;
                }
                content = '普通发票 ' + $.trim($('#inv_title').val())+ ' ' + $.trim($('#company_code').val())+ ' ' + $('#inv_content').val();
            }
        }else{
            content = '增值税发票 ' + $.trim($('input[name="inv_company"]').val()) + ' ' + $.trim($('input[name="inv_code"]').val()) + ' ' + $.trim($('input[name="inv_reg_addr"]').val());
            //验证增值税发票表单
            if (!$('#inv_form').valid()){
                return false;
            }
        }
        var datas=$('#inv_form').serialize();
        
        $.post('index.php',datas,function(data){
            if (data.state=='success'){
                $('#invoice_id').val(data.id);
                postResult = true;
            }else{
                showDialog(data.msg, 'error','','','','','','','','',2);
                postResult = false;
            }
        },'json');
        if (postResult){
            hideInvList(content);
        }
    });
	$('input[name="invoice_type"]').on('click',function(){
		if ($(this).val() == 1) {
			$('#invoice_panel').show();
			$('#vat_invoice_panel').hide();
		} else {
			$('#invoice_panel').hide();
			$('#vat_invoice_panel').show();
		}
	});
	$('select[name="inv_title_select"]').on('change',function(){
	    if ($(this).val()=='company') {
	        $('#inv_title').show();
	        $('#company_code').show();
	    } else {
	        $('#inv_title').hide();
	        $('#company_code').hide();
	    }
	});

    $('#inv_form').validate({
        rules : {
            inv_company : {
                required : true
            },
            inv_code : {
                required : true
            },
            inv_reg_addr : {
                required : true
            },
			inv_reg_phone : {
				required : true
			},
            inv_reg_bname : {
                required : true
            },
            inv_reg_baccount : {
                required : true
            },
            inv_rec_name : {
                required : true
            },
            inv_rec_mobphone : {
                required : true
            },            
            vregion : {
            	checklast: true
            },
            inv_goto_addr : {
                required : true
            }
        },
        messages : {
            inv_company : {
                required : '<i class="icon-exclamation-sign"></i>单位名称不能为空'
            },
            inv_code : {
                required : '<i class="icon-exclamation-sign"></i>纳税人识别号不能为空'
            },
            inv_reg_addr : {
                required : '<i class="icon-exclamation-sign"></i>注册地址不能为空'
            },
			inv_reg_phone : {
				required : '<i class="icon-exclamation-sign"></i>注册电话不能为空'
			},
            inv_reg_bname : {
                required : '<i class="icon-exclamation-sign"></i>开户银行不能为空'
            },
            inv_reg_baccount : {
                required : '<i class="icon-exclamation-sign"></i>银行账户不能为空'
            },
            inv_rec_name : {
                required : '<i class="icon-exclamation-sign"></i>收票人姓名不能为空'
            },
            inv_rec_mobphone : {
                required : '<i class="icon-exclamation-sign"></i>收票人手机号不能为空'
            },
            vregion : {
            	checklast: '<i class="icon-exclamation-sign"></i>请将地区选择完整'
            },
            inv_goto_addr : {
                required : '<i class="icon-exclamation-sign"></i>送票地址不能为空'
            }
        }
    });
});
</script>