<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <!-- 页面导航 -->
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>门店代金券</h3>
        <h5>商城门店代金券活动管理</h5>
      </div>
    </div>
  </div>

  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li><?php echo $lang['admin_voucher_template_list_tip'];?></li>
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
              <dt>代金券名称</dt>
              <dd>
                <input type="text" name="voucher_t_title" class="s-input-txt" placeholder="请输入代金券名称关键字" />
              </dd>
            </dl>
            <dl>
              <dt>门店名称</dt>
              <dd>
                <input type="text" name="voucher_t_chain_name" class="s-input-txt" placeholder="请输入门店名称关键字" />
              </dd>
            </dl>
            <dl>
              <dt>修改时间</dt>
              <dd>
                  <label>
                    <input type="text" name="sdate" data-dp="1" class="s-input-txt" placeholder="请选择筛选时间段起点" />
                  </label>
                  <label>
                    <input type="text" name="edate" data-dp="1" class="s-input-txt" placeholder="请选择筛选时间段终点" />
                  </label>
              </dd>
            </dl>
            <dl>
              <dt>状态</dt>
              <dd>
                <select name="voucher_t_state" class="s-select">
                    <option value="0" selected>全部</option>
                    <?php if ($output['templateState']){ ?>
                    <?php foreach ($output['templateState'] as $k=>$v){ ?>
                    <option value="<?php echo $v[0];?>"><?php echo $v[1];?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
              </dd>
            </dl>
            <dl>
              <dt>推荐</dt>
              <dd>
                <select name="voucher_t_recommend" class="s-select">
                    <option value="" selected>全部</option>
                    <option value="1" >是</option>
                    <option value="0" >否</option>
                </select>
              </dd>
            </dl>
            <dl>
              <dt>活动时期筛选</dt>
              <dd>
                <label>
                    <input type="text" name="pdate1" data-dp="1" class="s-input-txt" placeholder="结束时间不晚于" />
                </label>
                <label>
                    <input type="text" name="pdate2" data-dp="1" class="s-input-txt" placeholder="开始时间不早于" />
                </label>
              </dd>
            </dl>

          </div>
        </div>
        <div class="bottom">
          <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green">提交查询</a>
          <a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a>
        </div>
      </form>
    </div>

</div>

<script>
$(function(){
    var flexUrl = 'index.php?act=chain_voucher&op=templatelist_xml';

    $("#flexigrid").flexigrid({
        url: flexUrl,
        colModel: [
            {display: '操作', name: 'operation', width: 60, sortable: false, align: 'center', className: 'handle'},
            {display: '代金券名称', name: 'voucher_t_title', width: 300, sortable: false, align: 'left'},
            {display: '门店名称', name: 'voucher_t_chain_name', width: 200, sortable: false, align: 'left'},
            {display: '面额', name: 'voucher_t_price', width: 80, sortable: true, align: 'left'},
            {display: '消费金额', name: 'voucher_t_limit', width: 80, sortable: true, align: 'left'},
            {display: '最后修改时间', name: 'add_time_text', width: 120, sortable: true, align: 'center'},
            {display: '开始时间', name: 'start_time_text', width: 120, sortable: true, align: 'center'},
            {display: '结束时间', name: 'end_time_text', width: 120, sortable: true, align: 'center'},
            {display: '状态', name: 'state_text', width: 80, sortable: false, align: 'center'},
            {display: '推荐', name: 'recommend', width: 80, sortable: false, align: 'center'}
        ],
        searchitems: [
            {display: '代金券名称', name: 'voucher_t_title', isdefault: true},
            {display: '门店名称', name: 'voucher_t_chain_name'}
        ],
        sortname: "voucher_t_id",
        sortorder: "desc",
        title: '门店代金券列表'
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

    $('[data-dp]').datepicker({dateFormat: 'yy-mm-dd'});

});

$('a.confirm-on-click').live('click', function() {
    return confirm('确定"'+this.innerHTML+'"?');
});
</script>
