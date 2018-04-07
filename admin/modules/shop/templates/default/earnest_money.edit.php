<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?act=member&op=member" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>保证金管理</h3>
        <h5>商城商家入驻及门店开设保证金支付管理</h5>
      </div>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>对未付款记录支付状态进行编辑操作。</li>
    </ul>
  </div>
  <form id="user_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="etm_id" value="<?php echo $output['earnest_info']['etm_id']?>" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="member_name">用户名</label>
        </dt>
        <dd class="opt">
          <?php echo $output['earnest_info']['etm_member_name']?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="etm_sn">支付单号</label>
        </dt>
        <dd class="opt">
          <?php echo $output['earnest_info']['etm_sn']?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="etm_amount">缴费金额</label>
        </dt>
        <dd class="opt">
          ￥<?php echo $output['earnest_info']['etm_amount']?>
        </dd>
      </dl>
      <?php if($output['earnest_info']['etm_chain_id'] > 0){?>
      <dl class="row">
        <dt class="tit">
          <label for="etm_chain_id">门店编号</label>
        </dt>
        <dd class="opt">
          <?php echo $output['earnest_info']['etm_chain_id']?>
        </dd>
      </dl>
      <?php }?>
      <?php if($output['state_type'] == 'receive_pay'){?>
      <dl class="row">
        <dt class="tit">
          <label> 支付状态</label>
        </dt>
        <dd class="opt">
          <label>
            <input type="radio" checked="checked" value="0" name="etm_payment_state">
            未支付</label>
          <label>
            <input type="radio" value="1" name="etm_payment_state">
            已支付</label>
          <span class="err"></span></dd>
      </dl>
      <dl class="row pay_info" style="display: none;">
        <dt class="tit">
          <label for="payment_time"><em>*</em>付款时间</label>
        </dt>
        <dd class="opt">
          <input readonly id="payment_time" class="" name="payment_time" value="" type="text" />
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row pay_info" style="display: none;">
        <dt class="tit">
          <label for="payment_code"><em>*</em>付款方式 </label>
        </dt>
        <dd class="opt">
          <input type="hidden" name="etm_payment_code">
          <input type="hidden" name="etm_payment_name">
          <select name="payment_code" class="s-select">
            <option value="">—请选择—</option>
            <?php foreach($output['payment_list'] as $val) { ?>
            <option value="<?php echo $val['payment_code']; ?>"><?php echo $val['payment_name']; ?></option>
            <?php } ?>
          </select>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row pay_info" style="display: none;">
        <dt class="tit">
          <label for="trade_no"><em>*</em>第三方支付平台交易号</label>
        </dt>
        <dd class="opt">
          <input type="text" class="txt2" name="trade_no" id="trade_no" maxlength="40">
          <span class="err"></span>
          <p class="notic"><span class="vatop rowform">支付宝等第三方支付平台交易号</span></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
      <?php }else{ ?>
      <dl class="row">
        <dt class="tit">
          <label> 支付状态</label>
        </dt>
        <dd class="opt">
          <label><?php echo $output['earnest_info']['etm_payment_state'] > 0 ? '已支付' :'未支付'; ?></label>
        </dd>
      </dl>
      <?php if($output['earnest_info']['etm_payment_state'] > 0){?>
      <dl class="row pay_info">
        <dt class="tit">
          <label for="payment_time">付款时间</label>
        </dt>
        <dd class="opt">
          <?php echo date('Y-m-d',$output['earnest_info']['etm_payment_time'])?>
        </dd>
      </dl>
      <dl class="row pay_info">
        <dt class="tit">
          <label for="payment_code">付款方式 </label>
        </dt>
        <dd class="opt">
          <?php echo $output['earnest_info']['etm_payment_name']?>
        </dd>
      </dl>
      <dl class="row pay_info">
        <dt class="tit">
          <label for="trade_no">第三方支付平台交易号</label>
        </dt>
        <dd class="opt">
          <?php echo $output['earnest_info']['etm_trade_sn']?>
        </dd>
      </dl>
      <?php }?>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="history.back();">返回</a></div>
      <?php }?>
    </div>
  </form>
</div>

<script type="text/javascript">
$(function(){
  $('#payment_time').datepicker({dateFormat: 'yy-mm-dd',maxDate: '<?php echo date('Y-m-d',TIMESTAMP);?>'});
  $('input[name=etm_payment_state]').change(function(){
    var cur_val = $(this).val();
    if(cur_val == 1){
      $('.pay_info').show();
    }else{
      $('.pay_info').hide();
    }
  });
  $('select').change(function(){
    var cur_val = $(this).val();
    $('input[name=etm_payment_code]').val(cur_val);
    $('input[name=etm_payment_name]').val($(this).find('option:selected').text());
  });


	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
    if($("#user_form").valid()){
     $("#user_form").submit();
	}
	});
    $('#user_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
          payment_time : {
            required : function(){return $('input[name=etm_payment_state]:checked').val() == 1;}
          },
          etm_payment_code : {
            required : function(){return $('input[name=etm_payment_state]:checked').val() == 1;}
          },
          trade_no : {
            required : function(){return $('input[name=etm_payment_state]:checked').val() == 1;},
            remote   : {
              url :'index.php?act=earnest_money&op=ajax&branch=check_trade_no',
              type:'get',
              data:{
                  trade_no : function(){
                      return $('#trade_no').val();
                  }
              }
            }
          }
        },
        messages : {    		
          payment_time : {
            required : '<i class="fa fa-exclamation-circle"></i>支付时间不能为空'
          },
          etm_payment_code : {
            required : '<i class="fa fa-exclamation-circle"></i>请选择支付方式'
          },
          trade_no : {
            required : '<i class="fa fa-exclamation-circle"></i>第三方支付平台交易号不能为空',
            remote   : '<i class="fa fa-exclamation-circle"></i>该交易号已存在'
          }
        }
    });
});
</script> 
