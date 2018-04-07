<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
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
      <li>通过保证金管理，你可以进行查看、编辑保证金支付状态等操作</li>
      <li>你可以根据条件搜索保证金支付记录，然后选择相应的操作</li>
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
      <div id="searchCon" class="content">
        <div class="layout-box">
          <dl>
            <dt>会员名称</dt>
            <dd>
              <label>
                <input type="text" value="" name="lg_member_name" id="lg_member_name" class="s-input-txt" placeholder="输入会员全称或关键字">
              </label>
            </dd>
          </dl>
          <dl>
            <dt>会员ID</dt>
            <dd>
              <label>
                <input type="text" value="" name="lg_member_id" id="lg_member_id" class="s-input-txt" placeholder="输入会员ID">
              </label>
            </dd>
          </dl>
          <dl>
            <dt>记录时间</dt>
            <dd>
              <label>
                <input type="text" name="query_start_date" data-dp="1" class="s-input-txt" placeholder="请选择开始时间" />
              </label>
              <label>
                <input type="text" name="query_end_date" data-dp="1" class="s-input-txt" placeholder="请选择结束时间"  />
              </label>
            </dd>
          </dl>
        </div>
      </div>
      <div class="bottom"><a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green mr5">提交查询</a><a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a></div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=earnest_money&op=log_xml',
        colModel : [
            {display: '记录编号', name : 'lg_id', width : 60, sortable : false, align: 'center'},
            {display: '记录时间', name : 'lg_add_time', width: 80, sortable : true, align : 'center' },
            {display: '付款事由', name : 'lg_content', width : 320, sortable : false, align: 'left'},            
            {display: '保证金金额(元)', name : 'lg_av_amount', width : 100, sortable : true, align: 'center'},
            {display: '买家ID', name : 'lg_member_id', width : 40, sortable : true, align: 'center'},
            {display: '买家账号', name : 'lg_member_name', width : 150, sortable : true, align: 'left'},
            {display: '管理员', name : 'lg_admin_name', width: 150, sortable : true, align : 'left'},
            ],
        buttons : [
            {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation }
            ],
        searchitems : [
            {display: '会员ID', name : 'lg_member_id'},
            {display: '会员名称', name : 'lg_member_name'}
            ],
        sortname: "lg_id",
        sortorder: "desc",
        title: '保证金支付列表'
    });
	
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=earnest_money&op=add_log';
    }
}
</script> 

