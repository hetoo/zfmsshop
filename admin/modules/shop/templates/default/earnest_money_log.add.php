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
          <label for="lg_desc"><em>*</em>变更事由</label>
        </dt>
        <dd class="opt">
          <textarea name="lg_desc" id="lg_desc" class="textarea h60 w400"></textarea>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>变更类型</label>
        </dt>
        <dd class="opt">
          <label>
            <input type="radio" checked="checked" value="1" name="pay_type">
            增加</label>
          <label>
            <input type="radio" value="-1" name="pay_type">
            减少</label>
          <span class="err"></span></dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="etm_amount"><em>*</em>变更金额</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="etm_amount" name="etm_amount" class="input-txt">
          <span class="err"></span>
          <p class="notic">请输入变更金额。</p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>

<script type="text/javascript">
$(function(){
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
          etm_amount : {
            required : true,
            number : true,
            min : 0.01,
            max : 9999999
          },
          lg_desc : {
            required : true
          }
        },
        messages : {
    			member_name: {
    				required : '<i class="fa fa-exclamation-circle"></i>用户名不能为空',
    				maxlength: '<i class="fa fa-exclamation-circle"></i>用户名为3-15位字符，由中文、英文、数字及“_”、“-”组成',
    				minlength: '<i class="fa fa-exclamation-circle"></i>用户名为3-15位字符，由中文、英文、数字及“_”、“-”组成',
    				remote   : '<i class="fa fa-exclamation-circle"></i>用户名不存在'
    			},
          etm_amount : {
            required : '<i class="fa fa-exclamation-circle"></i>缴费金额不能为空',
            number : '<i class="fa fa-exclamation-circle"></i>缴费金额为0.01~9999999之间的数字',
            min : '<i class="fa fa-exclamation-circle"></i>缴费金额为0.01~9999999之间的数字',
            max : '<i class="fa fa-exclamation-circle"></i>缴费金额为0.01~9999999之间的数字'
          },
          lg_desc : {
            required : '<i class="fa fa-exclamation-circle"></i>第三方支付平台交易号不能为空'
          }
        }
    });
});
</script> 
