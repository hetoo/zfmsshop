<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>商城账户统计</h3>
                <h5>平台商城账户变动明细</h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>默认为当月的平台商城账户情况</li>
        </ul>
    </div>
    <div class="ncap-form-all ncap-stat-general"></div>
    <div id="flexigrid"></div>
    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
    <div class="ncap-search-bar">
        <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
        <div class="title">
            <h3>高级搜索</h3>
        </div>
        <form method="get" action="index.php" name="formSearch" id="formSearch">
            <div id="searchCon" class="content">
                <div class="layout-box">
                    <dl>
                        <dt>日期筛选</dt>
                        <dd>
                            <label>
                                <input readonly id="query_start_date" placeholder="请选择起始时间" name="query_start_date" value="" type="text" class="s-input-txt" />
                            </label>
                            <label>
                                <input readonly id="query_end_date" placeholder="请选择结束时间" name="query_end_date" value="" type="text" class="s-input-txt" />
                            </label>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="bottom"> <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green mr5">提交查询</a><a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a></div>
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL?>/js/jquery.numberAnimation.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL?>/js/statistics.js"></script>
<script>
    function update_flex(){
        $('.ncap-stat-general').load('index.php?act=stat_account&op=get_plat_income&'+$("#formSearch").serialize(),
            function(){
                $('.timer').each(count);
            });
    }
    $(function () {
        //绑定时间控件
        $('#query_start_date').datepicker();
        $('#query_end_date').datepicker();

        update_flex();
        $('#ncsubmit').click(function(){
            $('.flexigrid').after('<div id="flexigrid"></div>').remove();
            update_flex();
        });

        // 高级搜索重置
        $('#ncreset').click(function(){
            $('.flexigrid').after('<div id="flexigrid"></div>').remove();
            update_flex();
        });

        $('#searchBarOpen').click();
    });
</script>