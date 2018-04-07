<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>敏感词</h3>
                <h5>敏感词过滤检测功能设置</h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>平台可分别设置各功能敏感词过滤或检测</li>
        </ul>
    </div>
    <form name='form1' method='post'>
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <table class="flex-table">
            <thead>
            <tr>
                <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
                <th width="60" align="center" class="handle-s"><?php echo $lang['nc_handle'];?></th>
                <th width="300" align="left">功能描述</th>
                <th width="100" align="center">是否开启</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($output['sensitive_list'])){?>
                <?php foreach($output['sensitive_list'] as $val){?>
                    <tr>
                        <td class="sign"><i class="ico-check"></i></td>
                        <td class="handle-s"><a class="btn blue" href="index.php?act=sensitive&op=sensitive_set_edit&code=<?php echo $val['name']; ?>"><i class="fa fa-pencil-square-o"></i><?php echo $lang['nc_edit'];?></a></td>
                        <td><?php echo $val['title']; ?></td>
                        <td><?php echo ($val['is_open']) ? '<span class="on"><i class="fa fa-toggle-on"></i>开启</span>' : '<span class="off"><i class="fa fa-toggle-off"></i>关闭</span>';?></td>
                        <td></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>
<script>
    $(function(){
        $('.flex-table').flexigrid({
            height:'auto',// 高度自动
            usepager: false,// 不翻页
            striped: true,// 使用斑马线
            resizable: false,// 不调节大小
            title: '功能设置列表',// 表格标题
            reload: false,// 不使用刷新
            columnControl: false// 不使用列控制
        });
    });
</script>