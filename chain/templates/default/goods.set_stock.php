<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="eject_con">
  <?php if ($output['error']) {?>
  <div class="chain-error">参数错误</div>
  <?php } else {?>
  <div class="chain-goods-id">
    <div class="pic-thumb"><img src="<?php echo thumb($output['goodscommon_info'], 60)?>"/></div>
    <dl>
      <dt><?php echo $output['goodscommon_info']['goods_name'];?></dt>
      <dd>SPU：<?php echo $output['goodscommon_info']['goods_commonid']?></dd>
    </dl>
  </div>
  <div id="warning" class="alert alert-error"></div>
  <form method="post" action="<?php echo urlChain('goods', 'set_stock');?>" id="stock_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="goods_commonid" value="<?php echo $_GET['common_id']; ?>" />
    <div class="content">
      <table class="stock-table">
        <thead>
          <tr>
            <?php foreach ($output['spec_name'] as $val) {?>
            <th class="w60"><?php echo $val?></th>
            <?php }?>
            <th>-</th>
            <th class="w100 tl">商家货号</th>
            <th class="w100 tl">价格</th>
            <th class="w70">门店价格</th>
            <th class="w50">门店库存</th>
          </tr>
        </thead>
        <tbody class="stock-sku-list">
          <?php foreach ($output['goods_array'] as $key => $val) {?>
          <tr>
            <?php foreach ($output['spec_name'] as $k => $v) {?>
            <td class="stock"><?php echo $val['goods_spec'][$k];?></td>
            <?php }?>
            <td>-</td>
            <td class="tl"><?php echo $val['goods_serial'];?></td>
            <td class="tl"><?php echo $lang['currency'].ncPriceFormat($val['goods_price']); ?></td>
            <td><input type="text" class="text w60" name="chain_price[<?php echo $key?>]" value="<?php echo ncPriceFormat($output['stock_info'][$key]['chain_price']);?>" /></td>
            <td><input type="text" class="text w40" name="stock[<?php echo $key?>]" value="<?php echo intval($output['stock_info'][$key]['stock']);?>" /></td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
    <div class="ncsc-form-default" style="margin: 0;" id="set_class">
              <dl>
                <dt><?php echo '设置分类'.$lang['nc_colon']; ?></dt>
                <dd>
                  <input type="hidden" name="p_cate_id" value="<?php echo intval($output['p_cate_id'])?>">
                  <input type="hidden" name="cate_id" value="<?php echo intval($output['cate_id'])?>">
                  <select name="class_parent_id">
                    <option value="0">—请选择—</option>
                    <?php foreach((array)$output['class_list'] as $val){?>
                      <option value="<?php echo $val['class_id']?>" <?php echo $val['class_id'] == $output['p_cate_id']?'selected="selected"':'';?> ><?php echo $val['class_name']?></option>
                    <?php }?>
                  </select>
                  <select name="class_id" id="class_id" style="display: none;"></select>
                  <span></span>
                </dd>
              </dl>
            </div>
    <div class="bottom">
      <label class="submit-border">
        <input type="submit" class="submit" value="提交门店库存设置"/>
      </label>
    </div>
  </form>
  <?php }?>
</div>
<script>
$(function(){
  var p_cate_id = $('input[name=p_cate_id]').val();
  var cate_id = $('input[name=cate_id]').val();
  var getNext = function(tid){
    $.ajax({
      url:"index.php?act=goods&op=child_class",
      type: 'get',
      data: {class_id:tid,type:'json'},
      dataType: 'json',
      success:function(results){
        var goods_class = results;
        var count = goods_class.length;
        if(count > 0){
          html = '';
          for (var i = 0; i < count; i++) {
            if(i == 0){
              $('input[name=cate_id]').val(goods_class[i].class_id);
            }
            if(cate_id == goods_class[i].class_id){
              html += '<option value="'+goods_class[i].class_id+'" selected="selected">'+goods_class[i].class_name+'</option>';
            }else{
              html += '<option value="'+goods_class[i].class_id+'">'+goods_class[i].class_name+'</option>';
            }
            
          }
          flag = true;
          $('#class_id').html(html).show();
        }
      }
    });
  };

  if(p_cate_id > 0){
    getNext(p_cate_id);
  }

  var flag = false;
  $('#set_class').on('change','select',function(){
    var s_name = $(this).attr('name');
    if(s_name == 'class_parent_id'){
      var class_id = $(this).val();
      $('input[name=p_cate_id]').val(class_id);
      getNext(class_id);
    }else{
      $('input[name=cate_id]').val($(this).val());
    }
  });
    $('#stock_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
        submitHandler:function(form){
            ajaxpost('stock_form', '', '', 'onerror');
        },
        rules : {
            <?php foreach ($output['goods_array'] as $key => $val) {?>
            "chain_price[<?php echo $key?>]" : {
                required : true,
                number      : true,
                min         : 0.01,
                max         : 9999999
            },
            "stock[<?php echo $key?>]"  : {
                required : true,
                digits      : true
            },
            <?php }?>
            p_cate_id : {
              digits: true,
              min: 0
            },
            cate_id : {
              digits: true,
              min: function(){return flag ? 1 : 0;}
            },
            goods_commonid : {
                digits: true
            }
        },
        messages : {
            <?php foreach ($output['goods_array'] as $key => $val) {?>
            "chain_price[<?php echo $key?>]" : {
                required      : '<i class="icon-exclamation-sign"></i>请填写正确的门店价格',
                number      : '<i class="icon-exclamation-sign"></i>请填写正确的门店价格',
                min         : '<i class="icon-exclamation-sign"></i>请填写0.01~9999999之间的数字',
                max         : '<i class="icon-exclamation-sign"></i>请填写0.01~9999999之间的数字'
            },
            "stock[<?php echo $key?>]" : {
                required      : '<i class="icon-exclamation-sign"></i>门店库存请填写整数',
                digits      : '<i class="icon-exclamation-sign"></i>门店库存请填写整数'
            },
            <?php }?>
            p_cate_id : {
              digits: '',
              min: ''
            },
            cate_id : {
              digits: '',
              min: ''
            },
            goods_commonid : {
                digits: ''
            }
        }
    });
});
</script> 