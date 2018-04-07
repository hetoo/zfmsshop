<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <a class="back" href="index.php?act=store&op=store" title="返回<?php echo $lang['manage'];?>列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['nc_store_manage'];?> - 设置店铺“<?php echo $output['store_info']['store_name'];?>”的门店相关信息</h3>
        <h5><?php echo $lang['nc_store_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <form method="post" enctype="multipart/form-data" name="form1" id="chain_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="store_id" value="<?php echo $output['store_info']['store_id'];?>">
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">门店</dt>
        <dd class="opt">
          <div class="onoff">
            <label for="is_chain_allow_1" class="cb-enable <?php if($output['store_info']['is_chain_allow'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><?php echo $lang['open'];?></label>
            <label for="is_chain_allow_0" class="cb-disable <?php if($output['store_info']['is_chain_allow'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><?php echo $lang['close'];?></label>
            <input id="is_chain_allow_1" name="is_chain_allow" <?php if($output['store_info']['is_chain_allow'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="is_chain_allow_0" name="is_chain_allow" <?php if($output['store_info']['is_chain_allow'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic">设置当前是否可以创建门店</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="allow_chain_count">可开门店数</label>
        </dt>
        <dd class="opt">
          <input id="allow_chain_count" name="allow_chain_count" value="<?php echo $output['store_info']['allow_chain_count'];?>" class="w70" type="text" />&nbsp;个
          <span class="err"></span>
          <p class="notic">设置当前店铺可创建门店数量。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="earnest_money">门店保证金</label>
        </dt>
        <dd class="opt">
          <input id="earnest_money" name="earnest_money" value="<?php echo $output['store_info']['earnest_money'];?>" class="w70" type="text" />&nbsp;元
          <span class="err"></span>
          <p class="notic">设置当前店铺创建门店需要缴纳的保证金金额，如果设置为0则根据平台统一设置标准收取。</p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" id="submitBtn" class="ncap-btn-big ncap-btn-green"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
$(function(){
  $("#submitBtn").click(function(){
      if($("#chain_form").valid()){
          $("#chain_form").submit();
      }
    });
  $('#chain_form').validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
          earnest_money : {
                required : true,
                digits   : true,
                min      : 0,
                max      : 9999999
          },
          allow_chain_count : {
                required : true,
                digits   : true,
                min      : 0,
                max      : 100
          }
        },
        messages:{
          earnest_money : {
                required : '<i class="icon-exclamation-sign"></i>请填写门店保证金',
                digits   : '<i class="icon-exclamation-sign"></i>门店保证金为0~9999999之间的整数',
                min      : '<i class="icon-exclamation-sign"></i>门店保证金为0~9999999之间的整数',
                max      : '<i class="icon-exclamation-sign"></i>门店保证金为0~9999999之间的整数'
          },
          allow_chain_count : {
                required : '<i class="icon-exclamation-sign"></i>请填写门店保证金',
                digits   : '<i class="icon-exclamation-sign"></i>门店保证金为0~100之间的整数',
                min      : '<i class="icon-exclamation-sign"></i>门店保证金为0~100之间的整数',
                max      : '<i class="icon-exclamation-sign"></i>门店保证金为0~100之间的整数'
          }
        }
  });
});
</script>
