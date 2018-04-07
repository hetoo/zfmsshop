<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
    .sticky .tabmenu { padding: 0;  position: relative; }
    #texpress1 table {
        border-top: 1px solid #E6E6E6;
    }
    #sheet tr td:nth-child(2) {
        text-align: left;
    }
    #texpress1 .btn-group {
        display: inline-block;
        vertical-align: middle;
    }
    #texpress1 .btn {
        display: inline-block;
        font-size: 12px;
        padding: 8px 16px;
        line-height: 14px;
        border-radius: 3px;
        vertical-align: middle;
        font-weight: 400;
        cursor: pointer;
    }
    #texpress1 .btn:first-child {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    #texpress1 .btn:last-child {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    #texpress1 .btn-default {
        color: #666;
        border: 1px solid #DDD;
        background-color: #F7F7F7;
    }
    #texpress1 .btn.active {
        background: #1ABC9C;
        border-color: #17A88B;
        color: #fff;
    }
    .deliver-item {
        border: 1px solid #E6E6E6;
        padding: 5px 10px;
    }
    .simple-field{
        display: inline-block;
    }

</style>
<div class="wrap">
    <div class="step-title"><em><?php echo $lang['store_deliver_first_step'];?></em><?php echo $lang['store_deliver_confirm_daddress'];?>
        <span class="fr mr5"> <a href="<?php echo urlShop('store_order', 'batch_order_print', array('order_id' => $output['order_id']));?>" class="ncbtn-mini" target="_blank" title="批量打印发货单"/><i class="icon-print"></i>批量打印发货单</a></span>
    </div>
    <div class="deliver-sell-info"><strong class="fl"><?php echo $lang['store_deliver_my_daddress'].$lang['nc_colon'];?></strong>
        <a href="javascript:void(0);" onclick="ajax_form('modfiy_daddress', '<?php echo $lang['store_deliver_select_daddress'];?>', 'index.php?act=store_deliver&op=send_address_select&order_id=<?php echo $output['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i><?php echo $lang['nc_edit'];?></a> <span id="seller_address_span">
      <?php if (empty($output['daddress_info'])) {?>
          <?php echo $lang['store_deliver_none_set'];?>
      <?php } else { ?>
          <?php echo $output['daddress_info']['seller_name'];?>&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>
      <?php } ?>
      </span>
    </div>
    <form name="deliver_form" method="POST" id="deliver_form" action="index.php?act=store_deliver&op=batch_send&order_id=<?php echo $_GET['order_id'];?>" onsubmit="ajaxpost('deliver_form', '', '', 'onerror');return false;">
        <input type="hidden" value="<?php echo getReferer();?>" name="ref_url">
        <input type="hidden" value="ok" name="form_submit">
        <input type="hidden" value="<?php echo $output['daddress_info']['address_id'];?>" name="daddress_id" id="daddress_id">
        <input type="hidden" name="shipping_type" id="shipping_type" value="1">
        <div class="step-title mt30"><em><?php echo $lang['store_deliver_second_step'];?></em><?php echo $lang['store_deliver_express_select'];?></div>
        <div class="alert alert-success"><?php echo $lang['store_deliver_express_note'];?></div>
        <div class="tabmenu">
            <ul class="tab pngFix">
                <li id="eli1" class="active"><a href="javascript:void(0);" onclick="etab(1)"><?php echo $lang['store_deliver_express_zx'];?></a></li>
                <li id="eli2" class="normal"><a href="javascript:void(0);" onclick="etab(2)"><?php echo $lang['store_deliver_express_wx'];?></a></li>
            </ul>
        </div>
        <div id="texpress1" class="deliver-item">
            <div class="simple-field">
                <span>物流公司：</span>
                <span>
                      <select name="shipping_express_id" class="w120 mt5 mb5">
                          <option value="0">-请选择-</option>
                          <?php if (is_array($output['my_express_list']) && !empty($output['my_express_list'])){?>
                              <?php foreach ($output['my_express_list'] as $v){?>
                                  <?php if (!isset($output['express_list'][$v])) continue;?>
                                  <option value="<?php echo $v;?>"><?php echo $output['express_list'][$v]['e_name'];?></option>
                              <?php }?>
                          <?php }?>
                      </select>
                </span>
            </div>
            <div class="btn-group fr mt5">
                <label class="btn btn-default general active fl" title="普通快递">普通快递</label>
                <label class="btn btn-default sheet fl" title="电子面单">电子面单</label>
            </div>

        </div>
        <table class="ncsc-default-table order" id="texpress2" style="display:none">
            <tbody>
            <tr>
                <td class="bdl" style="border-top: 1px solid #E6E6E6;border-right: 1px solid #E6E6E6; ">无需物流配送，商家自行送货。</td>
            </tr>
            </tbody>
        </table>
        <div class="step-title mt30"><em><?php echo $lang['store_deliver_third_step'];?></em>确认收货信息并填写运单号</div>
        <?php if (is_array($output['order_list']) and !empty($output['order_list'])) { ?>
        <?php foreach($output['order_list'] as $order_info) { ?>
        <table class="ncsc-default-table order deliver">
            <tbody>
            <?php if (is_array($order_info) and !empty($order_info)) { ?>
                <tr>
                    <td colspan="20" class="sep-row"></td>
                </tr>
                <tr>
                    <th colspan="20"><span class="fr mr30"></span><span class="ml10"><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?><?php echo $order_info['order_sn']; ?></span><span class="ml20"><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?><em class="goods-time"><?php echo date("Y-m-d H:i:s",$order_info['add_time']); ?></em></span></th>
                </tr>
                <?php foreach($order_info['extend_order_goods'] as $k => $goods_info) { ?>
                    <tr>
                        <td class="bdl w10"></td>
                        <td class="w50"><div class="pic-thumb"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank"><img src="<?php echo cthumb($goods_info['goods_image'],'60',$order_info['store_id']); ?>" /></a></div></td>
                        <td class="tl"><dl class="goods-name">
                                <dt><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>"><?php echo $goods_info['goods_name']; ?></a></dt>
                                <dd><strong>￥<?php echo ncPriceFormat($goods_info['goods_price']); ?></strong>&nbsp;x&nbsp;<em><?php echo $goods_info['goods_num'];?></em>件</dd>
                            </dl></td>
                        <?php if ((count($order_info['extend_order_goods']) > 1 && $k ==0) || (count($order_info['extend_order_goods']) == 1)){?>
                            <td class="bdl bdr order-info w500" rowspan="<?php echo count($order_info['extend_order_goods']);?>"><dl>
                                    <dt><?php echo $lang['store_deliver_shipping_amount'].$lang['nc_colon'];?></dt>
                                    <dd>
                                        <?php if (!empty($order_info['shipping_fee']) && $order_info['shipping_fee'] != '0.00'){?>
                                            <?php echo $order_info['shipping_fee'];?>
                                        <?php }else{?>
                                            <?php echo $lang['nc_common_shipping_free'];?>
                                        <?php }?>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><?php echo $lang['store_deliver_forget'].$lang['nc_colon'];?></dt>
                                    <dd>
                                        <textarea name="deliver_explain_<?php echo $order_info['order_id'];?>" cols="100" rows="2" class="w400 tip-t" title="<?php echo $lang['store_deliver_forget_tips'];?>"><?php echo $order_info['extend_order_common']['deliver_explain'];?></textarea>
                                    </dd>
                                </dl></td>
                        <?php }?>
                    </tr>
                <?php }?>
                <tr>
                    <td colspan="20" class="tl bdl bdr" style="padding:8px" id="address"><strong class="fl"><?php echo $lang['store_deliver_buyer_adress'].$lang['nc_colon'];?></strong><span id="buyer_address_span"><?php echo $order_info['extend_order_common']['reciver_name'];?>&nbsp;<?php echo $order_info['extend_order_common']['reciver_info']['phone'];?>&nbsp;<?php echo $order_info['extend_order_common']['reciver_info']['address'];?></span><?php echo $order_info['extend_order_common']['reciver_info']['dlyp'] ? '[门店代收]' : '';?>
                        <a href="javascript:void(0)" nc_type="dialog" dialog_title="<?php echo $lang['store_deliver_buyer_adress'];?>" dialog_id="edit_buyer_address" uri="index.php?act=store_deliver&op=batch_buyer_address_edit&order_id=<?php echo $order_info['order_id'];?>" dialog_width="550" class="ncbtn-mini fr"><i class="icon-edit"></i><?php echo $lang['nc_edit'];?></a></td>
                </tr>
                <tr>
                    <td colspan="20" class="tl bdl bdr" style="padding:8px">
                        <strong class="fl mt5">配送方式：</strong>
                        <span class="shipping_name_area">
                            <span class="shipping_name mr5" style="vertical-align:middle;"></span>
                            <input name="shipping_code_<?php echo $order_info['order_id'];?>" type="text" class="text w200 tip-r shipping_code" style="vertical-align: middle;" title="正确填写物流单号，确保快递跟踪查询信息正确" maxlength="20" nc_value>
                            <a class="ncbtn ncbtn-mint logistic_code" style="display: none;" nc_value="<?php echo $order_info['order_id'];?>">获取物流单</a>
                            <a class="ncbtn btn-warning print" style="display: none;" nc_value="<?php echo $order_info['order_id'];?>">打印电子面单</a>
                        </span>
                        <span class="shipping_type_area" style="display: none;vertical-align:middle;">无需物流</span>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php } ?>
        <?php } ?>
        <div class="bottom tc">
            <label class="submit-border">
                <input type="button" class="submit" value="批量发货">
            </label>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
    function etab(t){
        if (t==1){
            $('#eli1').removeClass('normal').addClass('active');
            $('#eli2').removeClass('active').addClass('normal');
            $('#texpress1').css('display','');
            $('#texpress2').css('display','none');
            $('.shipping_name_area').show();
            $('.shipping_type_area').hide();
            $('#shipping_type').val(1);
        }else{
            $('#eli1').removeClass('active').addClass('normal');
            $('#eli2').removeClass('normal').addClass('active');
            $('#texpress1').css('display','none');
            $('#texpress2').css('display','');
            $('.shipping_name_area').hide();
            $('.shipping_type_area').show();
            $('#shipping_type').val(2);
        }
    }
    //获取物流单号
    function getLogisticCode(order_id){
        if($('input[name=shipping_code_'+order_id+']').attr('nc_value') == 1){
            showDialog('请勿重复获取物流单', 'error','','','','','','','','',2);
            return false;
        }
        var code = $('select[name=shipping_express_id]').val();
        if(code <= 0){
            showDialog('请选择物流公司', 'error','','','','','','','','',2);
            return false;
        }
        $.post("index.php?act=store_deliver&op=get_logistic_code",
            {order_id: order_id, shipping_express_id: code},
            function(data) {
                if(data.code == '200'){
                    $('input[name=shipping_code_'+order_id+']').val(data.logisticCode);
                    $('input[name=shipping_code_'+order_id+']').attr('nc_value',1);
                }else {
                    showDialog(data.result, 'error','','','','','','','','',2);
                    return false;
                }
            }, 'json'
        );
    }
    //打印电子面单
    function getPrint(order_id) {
        window.open("index.php?act=store_deliver&op=e_waybill_print&order_id="+order_id);
    }
    $(function(){
        //表单提示
        $('.tip-t').poshytip({
            className: 'tip-yellowsimple',
            showOn: 'focus',
            alignTo: 'target',
            alignX: 'center',
            alignY: 'top',
            offsetX: 0,
            offsetY: 2,
            allowTipHover: false
        });
        $('.tip-r').poshytip({
            className: 'tip-yellowsimple',
            showOn: 'focus',
            alignTo: 'target',
            alignX: 'right',
            alignY: 'center',
            offsetX: -50,
            offsetY: 0,
            allowTipHover: false
        });

        $('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
        $('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});

        $('#texpress1 .general').on('click',function () {
            $('#texpress1 .btn').removeClass('active');
            $(this).addClass('active');
            $('#sheet').hide();
            $('#general').show();
            $('#general-print').show();
            $('.logistic_code').hide();
            $('.print').hide();

        });
        $('#texpress1 .sheet').on('click',function () {
            $('#texpress1 .btn').removeClass('active');
            $(this).addClass('active');
            $('#general').hide();
            $('#sheet').show();
            $('#general-print').hide();
            $('.logistic_code').show();
            $('.print').show();
        });
        $('select[name=shipping_express_id]').change(function () {
            if($(this).val() > 0){
                var shipping_name = $(this).children('option:selected').text();
                $('.shipping_name').html(shipping_name);
            }else {
                $('.shipping_name').html('');
            }
        });
        $('.logistic_code').on('click',function () {
            var order_id = $(this).attr('nc_value');
            getLogisticCode(order_id);
        });
        $('.print').on('click',function () {
            var order_id = $(this).attr('nc_value');
            getPrint(order_id);
        });
        $('.submit').on('click',function () {
            if($('#shipping_type').val() == 1){
                var code = $('select[name=shipping_express_id]').val();
                if(code <= 0){
                    showDialog('请选择物流公司', 'error','','','','','','','','',2);
                    return false;
                }
                $(".shipping_code").each(function () {
                    if($(this).val() == ''){
                        showDialog('请填写快递单号', 'error','','','','','','','','',2);
                        return false;
                    }
                });
                $('#deliver_form').submit();
            }
        });
        

    });
</script>
