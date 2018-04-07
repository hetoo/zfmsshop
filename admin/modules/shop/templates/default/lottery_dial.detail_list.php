<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <a class="back" href="index.php?act=lottery_dial&op=dial_list" title="返回活动列表">
        <i class="fa fa-arrow-circle-o-left"></i>
      </a>
      <div class="subject">
        <h3>大转盘活动管理 - 抽奖管理</h3>
        <h5>商城大转盘活动抽奖结果管理</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>显示所有参与抽奖用户的抽奖结果</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
  <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
  <div class="ncap-search-bar">
    <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
    <div class="title">
      <h3>高级搜索</h3>
    </div>
    <form method="get" name="formSearch" id="formSearch">
      <input type="hidden" name="advanced" value="1" />
      <div id="searchCon" class="content">
        <div class="layout-box">
          <dl>
            <dt>奖项名称</dt>
            <dd>
              <label>
                <input type="text" name="rate_name" class="s-input-txt" placeholder="请输入活动标题关键字" />
              </label>
            </dd>
          </dl>
          <dl>
            <dt>中奖类型</dt>
            <dd>
              <label>
                <select name="rate_type" class="s-select">
                  <option value="">-请选择-</option>
                  <option value="0">未中奖</option>
                  <option value="1">积分</option>
                  <option value="2">卡券</option>
                  <option value="4">实物</option>
                </select>
              </label>
            </dd>
          </dl>
          <dl>
            <dt>派奖状态</dt>
            <dd>
              <label>
                <select name="prize_state" class="s-select">
                  <option value="">-请选择-</option>
                  <option value="0">未派奖</option>
                  <option value="1">已派奖</option>
                </select>
              </label>
            </dd>
          </dl>
          <dl>
            <dt>抽奖时间筛选</dt>
            <dd>
              <label>
                <input type="text" name="pdate1" data-dp="1" class="s-input-txt" placeholder="抽奖时间不晚于" />
              </label>
              <label>
                <input type="text" name="pdate2" data-dp="1" class="s-input-txt" placeholder="抽奖时间不早于" />
              </label>
            </dd>
          </dl>
        </div>
      </div>
      <div class="bottom"> <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green">提交查询</a> <a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a> </div>
    </form>
  </div>
</div>
<script>
$(function(){
    var flexUrl = 'index.php?act=lottery_dial&op=detail_xml&lot_id=<?php echo $output['dial_info']['lot_id']?>';

    $("#flexigrid").flexigrid({
        url: flexUrl,
        colModel: [
            {display: '操作', name: 'operation', width: 150, sortable: false, align: 'center', className: 'handle'},
            {display: '会员名称', name: 'member_name', width : 300, sortable: 1, align: 'left'},
            {display: '抽奖时间', name: 'add_time', width: 120, sortable: 1, align: 'left'},
            {display: '奖项名称', name: 'rate_name', width: 120, sortable: false, align: 'center'},
            {display: '中奖类型', name: 'rate_type', width: 120, sortable: 1, align: 'center'},
            {display: '派奖状态', name: 'prize_state', width: 120, sortable: 1, align: 'center'}
        ],
        searchitems: [
            {display: '奖项名称', name: 'rate_name', isdefault: true}
        ],
        sortname: "add_time",
        sortorder: "desc",
        title: '【<?php echo $output['dial_info']['lot_name']?>】抽奖列表',
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
</script>
