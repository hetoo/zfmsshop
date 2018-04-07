<?php defined('InShopNC') or exit('Access Invalid!');?>

<form method="post" action="<?php echo CHAIN_SITE_URL?>/index.php?act=chain_reciver&op=pickup_parcel" id="pickup_parcel_form">
  <input type="hidden" name="form_submit" value="ok" />
  <input type="hidden" name="order_id" value="<?php echo $_GET['order_id'];?>">
    <div class="content">
      <div class="order-handle">
        <div class="title">
          <h3>提货验证</h3>
        </div>
        <label>
          <input class="text w200 vm" type="text" maxlength="6" name="pickup_code" placeholder="请输入买家提供的验证码" autocomplete="off">
          <span></span>
          <input type="submit" class="btn" value="提交"/>
        </label>
        <p>自动发送给收货人手机及买家订单详情中的提供的“6位验证码”。</p>
      </div>
    </div>
</form>
<script>
$(function(){
    //input焦点时隐藏/显示填写内容提示信息
    $('#pickup_parcel_form').validate({
        errorPlacement: function(error, element){
            element.next().append(error);
        },
        submitHandler:function(form){
            ajaxpost('pickup_parcel_form', '', '', 'onerror');
        },
        rules : {
            pickup_code : {
                required : true,
                digits : true,
                rangelength : [6,6]
            }
        },
        messages : {
            pickup_code : {
                required : '请输入提货码',
                digits : '请输入正确的提货码',
                rangelength : '请输入正确的提货码'
            }
        }
    });
});
</script>