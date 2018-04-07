<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>门店管理</h3>
        <h5>店铺的审核及经营管理操作</h5>
      </div>
      <?php echo $output['top_link'];?></div>
  </div>
  <form method="post" enctype="multipart/form-data" name="form1" id="chain_form">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">门店</dt>
        <dd class="opt">
          <div class="onoff">
            <label for="chain_allow_1" class="cb-enable <?php if($output['list_setting']['chain_allow'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><?php echo $lang['open'];?></label>
            <label for="chain_allow_0" class="cb-disable <?php if($output['list_setting']['chain_allow'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><?php echo $lang['close'];?></label>
            <input id="chain_allow_1" name="chain_allow" <?php if($output['list_setting']['chain_allow'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="chain_allow_0" name="chain_allow" <?php if($output['list_setting']['chain_allow'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic">门店功能开启后，第三方入驻店铺即可添加门店</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">门店审核</dt>
        <dd class="opt">
          <div class="onoff">
            <label for="chain_check_allow_1" class="cb-enable <?php if($output['list_setting']['chain_check_allow'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><?php echo $lang['open'];?></label>
            <label for="chain_check_allow_0" class="cb-disable <?php if($output['list_setting']['chain_check_allow'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><?php echo $lang['close'];?></label>
            <input id="chain_check_allow_1" name="chain_check_allow" <?php if($output['list_setting']['chain_check_allow'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="chain_check_allow_0" name="chain_check_allow" <?php if($output['list_setting']['chain_check_allow'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic">门店审核功能开启后，第三方入驻店铺添加的门店必须审核通过后才能在前台展示</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="chain_earnest_money">门店保证金</label>
        </dt>
        <dd class="opt">
          <input id="chain_earnest_money" name="chain_earnest_money" value="<?php echo $output['list_setting']['chain_earnest_money'];?>" class="input-txt" type="text" />
          <span class="err"></span>
          <p class="notic">如门店保证金为0则第三方入驻店铺添加门店不需要缴纳保证金。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="chain_hot_keyword">热搜词</label>
        </dt>
        <dd class="opt">
          <input id="chain_hot_keyword" name="chain_hot_keyword" value="<?php echo $output['list_setting']['chain_hot_keyword'];?>" class="input-txt" type="text" />
          <span class="err"></span>
          <p class="notic">门店配送热搜词，为移动端门店配送模块推荐搜索词。多个词使用半角,分割</p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script type="text/javascript">
$(function(){
  $('#submitBtn').click(function(){
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
          chain_earnest_money : {
                required : true,
                digits   : true,
                min      : 0,
                max      : 9999999
          }
        },
        messages:{
          chain_earnest_money : {
                required : '<i class="icon-exclamation-sign"></i>请填写门店保证金',
                digits   : '<i class="icon-exclamation-sign"></i>门店保证金为0~9999999之间的整数',
                min      : '<i class="icon-exclamation-sign"></i>门店保证金为0~9999999之间的整数',
                max      : '<i class="icon-exclamation-sign"></i>门店保证金为0~9999999之间的整数'
          }
        }
  });
});
</script>
