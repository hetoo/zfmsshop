<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.content{  margin: 20px 0;  }
.content dl {  vertical-align: middle;  letter-spacing: normal;  word-spacing: normal;  display: inline-block;  width: 475px;  height: 155px;  margin: -1px 0 0 -1px;  border: solid #F7F7F7;  border-width: 1px 0 0 1px;  position: relative;  z-index: 1;  overflow: hidden;  zoom: 1;  }
.content dl .p-name { font: 16px/20px "microsoft yahei"; color: #333; position: absolute; z-index: 1; top: 24px; left: 20px;}
.content dl .p-amount { font-size: 16px; color: #3A87AD; position: absolute; z-index: 1; top: 24px; right: 20px;}
.content dl .p-info { font: 12px/16px "microsoft yahei"; color: #AAA; width: 450px; height: 32px; padding-top: 8px; border-top: dotted 1px #F5F5F5; position: absolute; z-index: 1; left: 15px; top: 96px; overflow: hidden;}
</style>

<div class="alert mt10" style="clear:both;">
    <ul class="mt5">
        <li>默认为当月的店铺交易统计</li>
    </ul>
</div>
<form method="get" action="index.php" target="_self">
    <input type="hidden" name="act" value="statistics_trade" />
    <input type="hidden" name="op" value="index" />
    <table class="search-form">
        <tr>
            <td class="tr">
                <div class="fr">
                    <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_common_search'];?>" /></label>
                </div>
                <div class="fr">
                    <div class="fl" style="margin-right:3px;">
                        <select name="search_type" id="search_type" class="querySelect">
                            <option value="day" <?php echo $output['search_arr']['search_type']=='day'?'selected':''; ?>>按照天统计</option>
                            <option value="week" <?php echo $output['search_arr']['search_type']=='week'?'selected':''; ?>>按照周统计</option>
                            <option value="month" <?php echo $output['search_arr']['search_type']=='month'?'selected':''; ?>>按照月统计</option>
                        </select>
                    </div>
                    <div id="searchtype_day" style="display:none;" class="fl">
                        <input type="text" class="text w70" name="search_time" id="search_time" value="<?php echo @date('Y-m-d',$output['search_arr']['day']['search_time']);?>" /><label class="add-on"><i class="icon-calendar"></i></label>
                    </div>
                    <div id="searchtype_week" style="display:none;" class="fl">
                        <select name="searchweek_year" class="querySelect">
                            <?php foreach ($output['year_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <select name="searchweek_month" class="querySelect">
                            <?php foreach ($output['month_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <select name="searchweek_week" class="querySelect">
                            <?php foreach ($output['week_arr'] as $k=>$v){?>
                                <option value="<?php echo $v['key'];?>" <?php echo $output['search_arr']['week']['current_week'] == $v['key']?'selected':'';?>><?php echo $v['val']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="searchtype_month" style="display:none;" class="fl">
                        <select name="searchmonth_year" class="querySelect">
                            <?php foreach ($output['year_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <select name="searchmonth_month" class="querySelect">
                            <?php foreach ($output['month_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</form>

<div id="container">
    <div class="content">
        <dl>
            <dt class="p-name">订单支付金额</dt>
            <dd class="p-amount"><?php echo $output['order_amount_sum'];?> 元</dd>
            <dd class="p-info">期间内包含实物和虚拟订单(元)</dd>
        </dl>
        <dl style="border-width: 1px;">
            <dt class="p-name">退款金额</dt>
            <dd class="p-amount"><?php echo $output['refund_amount_sum'];?> 元</dd>
            <dd class="p-info">期间内包含实物和虚拟订单退款(元)</dd>
        </dl>
        <dl style="border-width: 1px;">
            <dt class="p-name">店铺费用</dt>
            <dd class="p-amount"><?php echo $output['store_amount_sum'];?> 元</dd>
            <dd class="p-info">期间内的总金额(元)</dd>
        </dl>
    </div>
</div>

<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<script type="text/javascript">
    //展示搜索时间框
    function show_searchtime(){
        s_type = $("#search_type").val();
        $("[id^='searchtype_']").hide();
        $("#searchtype_"+s_type).show();
    }

    $(function(){
        $('#search_time').datepicker({dateFormat: 'yy-mm-dd'});

        show_searchtime();
        $("#search_type").change(function(){
            show_searchtime();
        });
        //更新周数组
        $("[name='searchweek_month']").change(function(){
            var year = $("[name='searchweek_year']").val();
            var month = $("[name='searchweek_month']").val();
            $("[name='searchweek_week']").html('');
            $.getJSON('index.php?act=index&op=getweekofmonth',{y:year,m:month},function(data){
                if(data != null){
                    for(var i = 0; i < data.length; i++) {
                        $("[name='searchweek_week']").append('<option value="'+data[i].key+'">'+data[i].val+'</option>');
                    }
                }
            });
        });

    });
</script>