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
      <li>新增支付记录后可从保证金支付记录列表中找到该条数据，并再次对未付款记录支付状态进行编辑操作。</li>
    </ul>
  </div>
  <form id="user_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="member_name"><em>*</em>用户名</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="member_name" id="member_name" class="input-txt">
          <span class="err"></span>
          <p class="notic">3-15位字符，可由中文、英文、数字及“_”、“-”组成（非商家账号）。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="etm_sn"><em>*</em>支付单号</label>
        </dt>
        <dd class="opt">
          <input type="text" id="etm_sn" name="etm_sn" class="input-txt" maxlength="18">
          <span class="err"></span>
          <p class="notic">18位数字组成。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="etm_amount"><em>*</em>缴费金额</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="etm_amount" name="etm_amount" class="input-txt">
          <span class="err"></span>
          <p class="notic">请输入缴费金额。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="etm_chain_id"><em>*</em>门店编号</label>
        </dt>
        <dd class="opt">
          <input type="text" value="0" id="etm_chain_id" name="etm_chain_id" class="input-txt">
          <span class="err"></span>
          <p class="notic">请输入要缴纳保证金门店编号，0为店铺入驻保证金</p>
        </dd>
      </dl>
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
    			member_name : {
    				required : true,
    				minlength: 3,
    				maxlength: 20,
    				remote   : {
              url :'index.php?act=earnest_money&op=ajax&branch=check_user_name',
              type:'get',
              data:{
                  user_name : function(){
                      return $('#member_name').val();
                  }
              }
            }
    			},
          etm_sn : {
				    required : true,
            number : true,
            maxlength: 18,
            minlength: 18,
            remote   : {
              url :'index.php?act=earnest_money&op=ajax&branch=check_etm_sn',
              type:'get',
              data:{
                  etm_sn : function(){
                      return $('#etm_sn').val();
                  }
              }
            }
          },
          etm_amount : {
            required : true,
            number : true,
            min : 0.01,
            max : 9999999
          },
          etm_chain_id : {
            required : true,
            digits : true,            
            remote   : {
              url :'index.php?act=earnest_money&op=ajax&branch=check_chain_id',
              type:'get',
              data:{
                  chain_id : function(){
                      return $('#etm_chain_id').val();
                  }
              }
            }
          },
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
    			member_name: {
    				required : '<i class="fa fa-exclamation-circle"></i>用户名不能为空',
    				maxlength: '<i class="fa fa-exclamation-circle"></i>用户名为3-15位字符，由中文、英文、数字及“_”、“-”组成',
    				minlength: '<i class="fa fa-exclamation-circle"></i>用户名为3-15位字符，由中文、英文、数字及“_”、“-”组成',
    				remote   : '<i class="fa fa-exclamation-circle"></i>用户名不存在'
    			},
          etm_sn : {
				    required : '<i class="fa fa-exclamation-circle"></i>支付单号不能为空',
            number : '<i class="fa fa-exclamation-circle"></i>支付单号为18位数字组成',
            maxlength: '<i class="fa fa-exclamation-circle"></i>支付单号为18位数字组成',
            minlength: '<i class="fa fa-exclamation-circle"></i>支付单号为18位数字组成',
            remote   : '<i class="fa fa-exclamation-circle"></i>该支付单已存在'
          },
          etm_amount : {
            required : '<i class="fa fa-exclamation-circle"></i>缴费金额不能为空',
            number : '<i class="fa fa-exclamation-circle"></i>缴费金额为0.01~9999999之间的数字',
            min : '<i class="fa fa-exclamation-circle"></i>缴费金额为0.01~9999999之间的数字',
            max : '<i class="fa fa-exclamation-circle"></i>缴费金额为0.01~9999999之间的数字'
          },
          etm_chain_id : {
            required : '<i class="fa fa-exclamation-circle"></i>门店编号不能为空',
            digits : '<i class="fa fa-exclamation-circle"></i>门店编号为非负整数',
            remote   : '<i class="fa fa-exclamation-circle"></i>该门店已存在支付记录'
          },
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
