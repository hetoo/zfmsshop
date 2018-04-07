<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
    .member-price dt {
        line-height: 30px;
        margin: 0;
        width: 15% !important;
    }
    .member-price dd{
        line-height: 30px;
        width: 84% !important;
    }
    .member-price dt i.required {
        font: 12px/16px Tahoma;
        color: #F30;
        vertical-align: middle;
        margin-right: 4px;
    }
</style>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="alert alert-info alert-block">
    <div class="faq-img"></div>
    <h4>说明：</h4>
    <ul>
        <li>1.商品如设置单品促销，以促销价格为准。</li>
        <li>2.根据会员不同级别分别设置会员价格。</li>
    </ul>
</div>
<form method="post" id="goods_form" action="<?php echo urlShop('store_goods_online', 'save_member_price');?>">
    <input type="hidden" name="form_submit" value="ok">
    <input type="hidden" name="ref_url" value="<?php echo $_GET['ref_url'];?>" />
    <input type="hidden" name="commonid" value="<?php echo intval($_GET['commonid']);?>" />
    <?php if (!empty($output['goods_array'])) {?>
        <?php foreach ($output['goods_array'] as $value) {?>
            <div class="ncsc-form-goods-gift" data-gid="<?php echo $value['goods_id'];?>">
                <div class="goods-pic"> <span><img src="<?php echo thumb($value, 240);?>"/></span></div>
                <div class="goods-summary">
                    <h2><?php echo $value['goods_name'];?><em>SKU：<?php echo $value['goods_id'];?></em></h2>
                    <dl>
                        <dt>商品价格：</dt>
                        <dd>￥<?php echo ncPriceFormat($value['goods_price']);?></dd>
                    </dl>
                    <dl>
                        <dt>库&nbsp;&nbsp;存&nbsp;&nbsp;量：</dt>
                        <dd><?php echo $value['goods_storage'];?></dd>
                    </dl>
                    <dl class="member-price">
                        <dt><i class="required">*</i>会员级别V1价格<?php echo $lang['nc_colon'];?></dt>
                        <dd>
                            <input name="memberprice1_<?php echo $value['goods_id'];?>" value="<?php echo ncPriceFormat($value['member_price_1']);?>" type="text" class="text w60" /><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>
                        </dd>
                    </dl>
                    <dl class="member-price">
                        <dt><i class="required">*</i>会员级别V2价格<?php echo $lang['nc_colon'];?></dt>
                        <dd>
                            <input name="memberprice2_<?php echo $value['goods_id'];?>" value="<?php echo ncPriceFormat($value['member_price_2']);?>" type="text" class="text w60" /><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>
                        </dd>
                    </dl>
                    <dl class="member-price">
                        <dt><i class="required">*</i>会员级别V3价格<?php echo $lang['nc_colon'];?></dt>
                        <dd>
                            <input name="memberprice3_<?php echo $value['goods_id'];?>" value="<?php echo ncPriceFormat($value['member_price_3']);?>" type="text" class="text w60" /><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>
                        </dd>
                    </dl>
                </div>
            </div>
        <?php }?>
    <?php }?>
    <div class="bottom tc">
        <label class="submit-border">
            <input type="submit" nctype="formSubmit" class="submit" value="确认提交" />
        </label>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        //凸显鼠标触及区域、其余区域半透明显示
        $("#goods_gift > div").jfade({
            start_opacity:"1",
            high_opacity:"1",
            low_opacity:".25",
            timing:"200"
        });
        // 防止重复提交
        var __formSubmit = false;
        $('input[nctype="formSubmit"]').click(function(){
            if (__formSubmit) {
                return false;
            }
            if($('#goods_form').valid()){
                __formSubmit = true;
            }
        });

        $('#goods_form').validate({
            errorPlacement: function(error, element){
                __formSubmit = false;
                $(element).nextAll('span').append(error);

            },
            submitHandler:function(form){
                ajaxpost('goods_form', '', '', 'onerror');
            },
            rules : {
                <?php if (!empty($output['goods_array'])) {?>
                <?php foreach ($output['goods_array'] as $value) {?>
                    memberprice1_<?php echo $value['goods_id'];?> : {
                        required    : true,
                        number      : true,
                        min         : 0.00,
                        max         : <?php echo $value['goods_price'] - 0.01;?>
                    },
                    memberprice2_<?php echo $value['goods_id'];?> : {
                        required    : true,
                        number      : true,
                        min         : 0.00,
                        max         : <?php echo $value['goods_price'] - 0.01;?>
                    },
                    memberprice3_<?php echo $value['goods_id'];?> : {
                        required    : true,
                        number      : true,
                        min         : 0.00,
                        max         : <?php echo $value['goods_price'] - 0.01;?>
                    },
                <?php }?>
                <?php }?>
            },
            messages : {
                <?php if (!empty($output['goods_array'])) {?>
                <?php foreach ($output['goods_array'] as $value) {?>
                    memberprice1_<?php echo $value['goods_id'];?> : {
                        required    : '<i class="icon-exclamation-sign"></i>请填写会员等级价格',
                        number      : '<i class="icon-exclamation-sign"></i>请填写正确的价格',
                        min         : '<i class="icon-exclamation-sign"></i>请填写0.00~<?php echo $value['goods_price'] - 0.01;?>之间的数字',
                        max         : '<i class="icon-exclamation-sign"></i>请填写0.01~<?php echo $value['goods_price'] - 0.01;?>之间的数字'
                    },
                    memberprice2_<?php echo $value['goods_id'];?> : {
                        required    : '<i class="icon-exclamation-sign"></i>请填写会员等级价格',
                        number      : '<i class="icon-exclamation-sign"></i>请填写正确的价格',
                        min         : '<i class="icon-exclamation-sign"></i>请填写0.00~<?php echo $value['goods_price'] - 0.01;?>之间的数字',
                        max         : '<i class="icon-exclamation-sign"></i>请填写0.01~<?php echo $value['goods_price'] - 0.01;?>之间的数字'
                    },
                    memberprice3_<?php echo $value['goods_id'];?> : {
                        required    : '<i class="icon-exclamation-sign"></i>请填写会员等级价格',
                        number      : '<i class="icon-exclamation-sign"></i>请填写正确的价格',
                        min         : '<i class="icon-exclamation-sign"></i>请填写0.00~<?php echo $value['goods_price'] - 0.01;?>之间的数字',
                        max         : '<i class="icon-exclamation-sign"></i>请填写0.01~<?php echo $value['goods_price'] - 0.01;?>之间的数字'
                    },
                <?php }?>
                <?php }?>
            }
        });
    });

</script>
