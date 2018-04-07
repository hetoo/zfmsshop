<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=sensitive" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>敏感词 - 设置</h3>
                <h5>敏感词用于前台过滤</h5>
            </div>
        </div>
    </div>

    <form id="search_form" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="word_id" value="<?php echo $output['current_info']['word_id'];?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="s_name"><em>*</em>敏感词</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="word_name" value="<?php echo $output['current_info']['word_name'];?>" id="word_name" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">是否开启</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="site_status1" class="cb-enable <?php if($output['current_info']['is_open'] == '1'){ ?>selected<?php } ?>" ><?php echo $lang['open'];?></label>
                        <label for="site_status0" class="cb-disable <?php if($output['current_info']['is_open'] == '0'){ ?>selected<?php } ?>" ><?php echo $lang['close'];?></label>
                        <input id="site_status1" name="is_open" <?php if($output['current_info']['is_open'] == '1'){ ?>checked="checked"<?php } ?>  value="1" type="radio">
                        <input id="site_status0" name="is_open" <?php if($output['current_info']['is_open'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
                    </div>
                    <p class="notic">关闭后，该词不用于敏感词过滤</p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>
<script>
    //按钮先执行验证再提交表单
    $(function(){$("#submitBtn").click(function(){
        if($("#search_form").valid()){
            $("#search_form").submit();
        }
    });
    });

    $(document).ready(function(){
        $('#search_form').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                word_name : {
                    required : true
                },
            },
            messages : {
                word_name : {
                    required : '<i class="fa fa-exclamation-circle"></i>请填写敏感词'
                }
            }
        });
    });
</script>