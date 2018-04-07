<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.d_inline {
	display: inline;
}
</style>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back()" title="返回<?php echo $lang['manage'];?>列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>门店管理 - 编辑门店“<?php echo $output['chain_info']['chain_name'];?>”的店铺信息</h3>
        <h5>店铺的审核及经营管理操作</h5>
      </div>
    </div>
  </div>
  <div class="homepage-focus" nctype="editStoreContent">
  <div class="title">
  <h3>编辑门店信息</h3>
    </div>
    <form id="base_form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="chain_id" value="<?php echo $output['chain_info']['chain_id'];?>" />
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label>店主账号</label>
          </dt>
          <dd class="opt"><?php echo $output['chain_info']['chain_user'];?><span class="err"></span>
            <p class="notic"></p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_name"><em>*</em>门店名称</label>
          </dt>
          <dd class="opt">
            <input type="text" value="<?php echo $output['chain_info']['chain_name'];?>" id="chain_name" name="chain_name" class="input-txt">
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_cycle"><em>*</em>结算周期</label>
          </dt>
          <dd class="opt">
            <input type="text" value="<?php echo $output['chain_info']['chain_cycle'];?>" id="chain_cycle" name="chain_cycle" class="w70">&nbsp;天
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="area_id_1">所在地区</label>
          </dt>
          <dd class="opt">
            <input id="chain_region" name="area_info" type="hidden" value="<?php echo $output['chain_info']['area_info'];?>" >
            <input id="_area_1" name="area_id_1" type="hidden" value="<?php echo $output['chain_info']['area_id_1'];?>" >
            <input id="_area_2" name="area_id_2" type="hidden" value="<?php echo $output['chain_info']['area_id_2'];?>" >
            <input id="_area_3" name="area_id_3" type="hidden" value="<?php echo $output['chain_info']['area_id_3'];?>" >
            <input id="_area_4" name="area_id_4" type="hidden" value="<?php echo $output['chain_info']['area_id_4'];?>" >
            <input id="_area" name="area_id" type="hidden" value="<?php echo $output['chain_info']['area_id'];?>" >
            <span class="err"></span>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_address"><em>*</em>门店地址</label>
          </dt>
          <dd class="opt">
            <input type="text" value="<?php echo $output['chain_info']['chain_address'];?>" id="chain_address" name="chain_address" class="input-txt" />
            <span class="err"></span>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_address"><em>*</em>门店电话</label>
          </dt>
          <dd class="opt">
            <input type="text" value="<?php echo $output['chain_info']['chain_phone'];?>" id="chain_phone" name="chain_phone" class="input-txt" />
            <span class="err"></span>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_address"><em>*</em>营业时间</label>
          </dt>
          <dd class="opt">
            <input type="text" value="<?php echo $output['chain_info']['chain_opening_hours'];?>" id="chain_opening_hours" name="chain_opening_hours" class="input-txt" />
            <span class="err"></span>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_address">交通线路</label>
          </dt>
          <dd class="opt">
            <textarea  rows="3" class="tarea" id="chain_traffic_line" name="chain_traffic_line"><?php echo $output['chain_info']['chain_traffic_line'];?></textarea>
            <span class="err"></span>
          </dd>
        </dl>

        <dl class="row">
          <dt class="tit">
            <label for="chain_time">开店时间</label>
          </dt>
          <dd class="opt"><?php echo ($t = $output['chain_info']['chain_time'])?@date('Y-m-d H:i:s',$t):'';?><span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_img">门店图片</label>
          </dt>
          <dd class="opt">
            <a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>" alt="" style="max-height: 30px;" /> </a>
              <input class="w200" type="file" name="chain_img">
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_logo">门店LOGO</label>
          </dt>
          <dd class="opt">
            <a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_logo'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_logo'],$output['chain_info']['store_id']);?>" alt="" style="max-height: 30px;" /> </a>
              <input class="w200" type="file" name="chain_logo">
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="chain_banner">门店横幅</label>
          </dt>
          <dd class="opt">
            <a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>" alt="" style="max-height: 30px;" /> </a>
              <input class="w200" type="file" name="chain_banner">
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="state">门店开关</label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="chain_state1" class="cb-enable <?php if($output['chain_info']['chain_state'] == '1'){ ?>selected<?php } ?>" ><?php echo $lang['open'];?></label>
              <label for="chain_state0" class="cb-disable <?php if($output['chain_info']['chain_state'] == '0'){ ?>selected<?php } ?>" ><?php echo $lang['close'];?></label>
              <input id="chain_state1" name="chain_state" <?php if($output['chain_info']['chain_state'] == '1'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_close_info').hide();" value="1" type="radio">
              <input id="chain_state0" name="chain_state" <?php if($output['chain_info']['chain_state'] == '0'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_close_info').show();" value="0" type="radio">
            </div>
            <span class="err"></span>
            <p class="notic"></p>
          </dd>
        </dl>
        <dl class="row" id="tr_chain_close_info">
          <dt class="tit">
            <label for="chain_close_info">关闭原因</label>
          </dt>
          <dd class="opt">
            <textarea name="chain_close_info" rows="6" class="tarea" id="chain_close_info"><?php echo $output['chain_info']['chain_close_info'];?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.nyroModal.js"></script>

<script type="text/javascript">
var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';
$(function(){
    $("#chain_region").nc_region();
    $('a[nctype="nyroModal"]').nyroModal();
    $('input[name=chain_state][value=<?php echo $output['chain_info']['chain_state'];?>]').trigger('click');

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#base_form").valid()){
            $("#base_form").submit();
        }
    });

    $('#base_form').validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
             chain_name: {
                  required : true
              },
              chain_cycle : {
                  required : true,
                  digits   : true,
                  min      : 1,
                  max      : 366
              },
              area_info : {
                  checklast: true
              },
              chain_address : {
                  required : true
              },
              chain_phone : {
                  required : true
              },
              chain_opening_hours : {
                  required : true
              }
        },
        messages : {
              chain_name: {
                  required : '<i class="fa fa-exclamation-circle"></i>门店名称不能为空'
              },
              chain_cycle : {
                  required : '<i class="icon-exclamation-sign"></i>请填写门店结算周期',
                  digits   : '<i class="icon-exclamation-sign"></i>门店结算周期为1-366的正整数',
                  min      : '<i class="icon-exclamation-sign"></i>门店结算周期为1-366的正整数',
                  max      : '<i class="icon-exclamation-sign"></i>门店结算周期为1-366的正整数'
              },
              area_info : {
                  checklast : '<i class="icon-exclamation-sign"></i>请将地区选择完整'
              },
              chain_address : {
                  required : '<i class="icon-exclamation-sign"></i>请填写详细地址'
              },
              chain_phone : {
                  required : '<i class="icon-exclamation-sign"></i>请填写联系方式'
              },
              chain_opening_hours : {
                  required : '<i class="icon-exclamation-sign"></i>请填写营业时间'
              }
        }
    });
});
</script>