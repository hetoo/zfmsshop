<div class="goods-gallery add-step2">
    <?php if(!empty($output['video_list'])){?>
        <ul class="list">
            <?php foreach ($output['video_list'] as $v){?>
                <li onclick="insert_video('<?php echo $v['video_cover'];?>','<?php echo goodsVideoPath($v['video_cover'] , 0);?>');"><a href="JavaScript:void(0);"><video src="<?php echo goodsVideoPath($v['video_cover'], $v['store_id']);?>" title='<?php echo $v['video_name']?>'></video></a></li>
            <?php }?>
        </ul>
    <?php }else{?>
        <div class="warning-option"><i class="icon-warning-sign"></i><span>空间中暂无视频</span></div>
    <?php }?>
    <div class="pagination"><?php echo $output['show_page']; ?></div>
</div>
<script>
    $(document).ready(function(){
        $('#video_demo .demo').ajaxContent({
            event:'click', //mouseover
            loaderType:'img',
            loadingMsg:'<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif',
            target:'#video_demo'
        });
    });
</script>