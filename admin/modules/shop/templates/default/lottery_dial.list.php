<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>大转盘活动管理</h3>
        <h5>商城大转盘活动管理</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>当平台发起活动，可设置所有会员或者会员每次订单完成后进行大转盘形式的抽奖活动</li>
      <li>可以设置相关奖品，奖品包含：积分、平台红包、实物奖、未中奖</li>
      <li>中奖后，积分、平台红包直接进入会员账户，实物奖品PC端后台生成中奖名单由平台线下处理</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script>
$(function(){
    var flexUrl = 'index.php?act=lottery_dial&op=dial_xml';

    $("#flexigrid").flexigrid({
        url: flexUrl,
        colModel: [
            {display: '操作', name: 'operation', width: 150, sortable: false, align: 'center', className: 'handle'},
            {display: '抽奖编号', name: 'lot_id', width : 80, sortable: 1, align: 'center'},
            {display: '活动标题', name: 'lot_name', width : 300, sortable: false, align: 'left'},
            {display: '中奖率', name: 'lot_weight', width: 80, sortable: 1, align: 'left'},
            {display: '开始时间', name: 'start_time', width: 120, sortable: 1, align: 'center'},
            {display: '结束时间', name: 'end_time', width: 120, sortable: 1, align: 'center'},
            {display: '抽取方式', name: 'lot_type', width: 120, sortable: 1, align: 'center'},
            {display: '抽奖次数', name: 'lot_count', width: 120, sortable: 1, align: 'center'}
        ],
        buttons: [
            {
                display: '<i class="fa fa-plus"></i>新增活动',
                name: 'add',
                bclass: 'add',
                title: '平台发起新活动',
                onpress: function() {
                    location.href = 'index.php?act=lottery_dial&op=add';
                }
            }
        ],
        searchitems: [
            {display: '活动标题', name: 'lot_name', isdefault: true}
        ],
        sortname: "lot_id",
        sortorder: "desc",
        title: '活动列表'
    });

    // 高级搜索提交
    $('#ncsubmit').click(function(){
        $("#flexigrid").flexOptions({url: flexUrl + '&' + $("#formSearch").serialize(),query:'',qtype:''}).flexReload();
    });

    // 高级搜索重置
    $('#ncreset').click(function(){
        $("#flexigrid").flexOptions({url: flexUrl}).flexReload();
        $("#formSearch")[0].reset();
    });

    $("input[data-dp='1']").datepicker({dateFormat: 'yy-mm-dd'});

});

$('a[data-href]').live('click', function() {
    if ($(this).hasClass('confirm-del-on-click') && !confirm('确定删除?')) {
        return false;
    }

    $.getJSON($(this).attr('data-href'), function(d) {
        if (d && d.result) {
            $("#flexigrid").flexReload();
        } else {
            alert(d && d.message || '操作失败！');
        }
    });
});
</script>
