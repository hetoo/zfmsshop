<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo ADMIN_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css">
<link href="<?php echo ADMIN_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<link href="<?php echo SHOP_SITE_URL; ?>/resource/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<style>
.ncsc-form-goods .btn {font: 12px/28px "microsoft yahei";  color: #434A54;  background-color: #F6F7FB;  width: 64px;  height: 28px;  padding: 0;  border: 0;  border-radius: 0;  cursor: pointer;}
.ncsc-form-goods .btn:hover{ background-color: #36bc9b; color: #fff; }
.search-result{margin-top:10px;}
.ncsc-form-radio-list li{ display: block}
.ncsc-form-goods textarea{ display: block}

.ncsc-goods-default-video .goodsvideo-uplaod .handle {
    height: 30px;
    margin: 10px 0 20px;
}
.ncsc-goods-default-pic .goodspic-upload .handle {
    height: 30px;
    margin: 10px 0 20px;
}
.ncsc-goods-default-video .goodsvideo-uplaod .upload-thumb {
    line-height: 0;
    background-color: #FFF;
    text-align: center;
    vertical-align: middle;
    display: table-cell;
    width: 160px;
    height: 160px;
    border: solid 1px #F5F5F5;
    overflow: hidden; margin-top: 10px;}
.ncsc-goods-default-video .goodsvideo-uplaod .upload-thumb video {
    max-width: 160px;
    max-height: 160px;}


.ncsc-goods-default-pic .goodspic-upload .upload-thumb {
    position: relative;
    background-color: #FFF;
    text-align: center;
    vertical-align: middle;
    display: table-cell;
    width: 160px;
    height: 160px;
    overflow: hidden;border: solid 1px #F5F5F5;
}
.ncsc-goods-default-pic .goodspic-upload .upload-thumb img {
    max-width: 160px;
    max-height: 160px;}

.ncsc-form-goods textarea {
    width: 300px;
    height: 100px;
}
</style>
<div class="page item-publish">
    <div class="fixed-bar">
        <div class="item-title">
            <a class="back" href="index.php?act=mb_demand&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>点播管理 - 新增</h3>
                <h5>管理数据的新增、编辑、删除</h5>
            </div>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" id="post_form">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default ncsc-form-goods">
            <dl>
                <dt><i class="required">*</i>推荐店铺：</dt>
                <dd>
                    <select class="selected" name="qtype">
                        <option selected="selected" value="store_name">店铺名称</option>
                        <option value="member_name">店主账号</option>
                        <option value="seller_name">商家账号</option>
                    </select>
                    <input nctype="search_keyword" class="qsbox" type="text" placeholder="搜索相关数据..." name="search_keyword" size="30">
                    <input class="btn" nctype="btn_search_store" type="button" value="<?php echo $lang['nc_search'];?>">
                    <p class="hint">不输入名称直接搜索将显示所有开启状态的店铺</p>
                    <div nctype="div_search_result" class="search-result"></div>
                    <input type="hidden" name="store" value="">
                    <span></span>
                </dd>
            </dl>

            <dl>
                <dt><i class="required">*</i>视频分类：</dt>
                <dd>
                    <select name="video_cate">
                        <option value="">---请选择---</option>
                        <?php foreach($output['video_cate_list'] as $v){ ?>
                            <option value="<?php echo $v['cate_id']; ?>"><?php echo $v['cate_name']; ?></option>
                        <?php } ?>
                    </select>
                    <span></span>
                    <p class="hint"></p>
                </dd>
            </dl>

            <dl>
                <dt><i class="required">*</i>推广位：</dt>
                <dd>
                    <ul class="ncsc-form-radio-list">
                        <li>
                            <input id="promote_0" nctype="promote" name="promote" class="radio" type="radio" checked="checked" value="0">
                            <label for="promote_0">6秒短视频/文字</label>
                            <div nctype="div_promote" style="display: block;">
                                <div class="ncsc-goods-default-video">
                                    <div class="goodsvideo-uplaod">
                                        <div class="upload-thumb">
                                            <video poster="<?php echo UPLOAD_SITE_URL .'/shop/common/'.C('default_goods_video')?>" nctype="promote_video" src=""></video>
                                        </div>
                                        <input type="hidden" name="promote_video_path" id="promote_video_path" nctype="promote_video" value="">
                                        <span class="err2"></span>
                                        <p class="hint">上传商品视频；支持mp4格式上传，建议使用
                                            <font color="red">大小不超过5M的视频</font>，上传后的视频将会自动保存在视频空间的默认分类中。</p>
                                        <div class="handle">
                                            <div class="ncsc-upload-btn">
                                                <a href="javascript:void(0);"><span>
                                                      <input type="file" hidefocus="true" size="1" class="input-file" name="promote_video" id="promote_video">
                                                      </span>
                                                    <p><i class="icon-upload-alt"></i>视频上传</p>
                                                </a> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div style="margin:10px 0;">
                                    <textarea name="promote_text"></textarea>
                                    <span class="err2"></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <input id="promote_1" nctype="promote" name="promote" class="radio" type="radio" value="1">
                            <label for="promote_1">图片</label>
                            <div nctype="div_promote" style="display: none;">
                                <div class="ncsc-goods-default-pic">
                                    <div class="goodspic-upload">
                                        <div class="upload-thumb selected"><img nctype="promote_image" src="<?php echo UPLOAD_SITE_URL .'/shop/common/'.C('default_goods_image')?>"> </div>
                                            <input type="hidden" name="promote_image_path" id="promote_image_path" nctype="promote_image" value="">
                                        <span class="err2"></span>
                                        <p class="hint">上传商品默认图，支持jpg、gif、png格式上传或从图片空间中选择，建议使用
                                            <font color="red">尺寸750*440像素以上</font></p>
                                        <div class="handle">
                                            <div class="ncsc-upload-btn"> <a href="javascript:void(0);">
                                                    <span>
                                                      <input type="file" hidefocus="true" size="1" class="input-file" name="promote_image" id="promote_image">
                                                    </span>
                                                    <p><i class="icon-upload-alt"></i>图片上传</p>
                                                </a> </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </dd>
            </dl>

            <dl id="li_video">
                <dt><i class="required">*</i>点播视频：</dt>
                <dd>
                    <div class="ncsc-goods-default-video">
                        <div class="goodsvideo-uplaod">
                            <div class="upload-thumb">
                                <video poster="<?php echo UPLOAD_SITE_URL .'/shop/common/'.C('default_goods_video')?>" nctype="demand_video" src=""></video>
                            </div>
                            <input type="hidden" name="demand_video_path" id="demand_video_path" nctype="demand_video" value="">
                            <span class="err2"></span>
                            <p class="hint">上传商品视频；支持mp4格式上传，建议使用
                                <font color="red">大小不超过20M的视频</font>，上传后的视频将会自动保存在视频空间的默认分类中。</p>
                            <div class="handle">
                                <div class="ncsc-upload-btn">
                                    <a href="javascript:void(0);">
                                        <span>
                                          <input type="file" hidefocus="true" size="1" class="input-file" name="demand_video" id="demand_video">
                                        </span>
                                        <p><i class="icon-upload-alt"></i>视频上传</p>
                                    </a> </div>
                                </div>
                        </div>
                    </div>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function(){
        var ADMIN_SITE_URL = "<?php echo ADMIN_SITE_URL; ?>";
        var ADMIN_TEMPLATES_URL = "<?php echo ADMIN_TEMPLATES_URL; ?>";
        var DEFAULT_PROMOTE_VIDEO = "<?php echo goodsVideoPath('');?>";
        var DEFAULT_DEMAND_VIDEO = "<?php echo goodsVideoPath('');?>";
        var DEFAULT_GOODS_IMAGE = "<?php echo thumb(array(), 60);?>";

        // 搜索店铺
        $('input[nctype="btn_search_store"]').click(function(){
            _url = '<?php echo urlAdminMobile('mb_demand', 'select_recommend_store');?>';
            $('div[nctype="div_search_result"]').html('').load(_url + '&qtype='+$('.selected').val() + '&search_keyword='+$('input[nctype="search_keyword"]').val());
        });


        // 推广位显示隐藏
        $('input[nctype="promote"]').click(function(){
            $('input[nctype="promote"]').nextAll('div[nctype="div_promote"]').hide();
            $(this).nextAll('div[nctype="div_promote"]').show();
        });

        $('#promote_0').click(function() {
            if($('#promote_0').prop("checked") == true){
                $('#li_video').show();
            }
        });
        
        $('#promote_1').click(function() {
            if($('#promote_1').prop("checked") == true){
                $('#li_video').hide();
            }
        });

        /* 推广位视频ajax上传 */
        $('#promote_video').fileupload({
            dataType: 'json',
            url: ADMIN_SITE_URL + '/index.php?act=mb_demand&op=video_upload&upload_type=promotefile',
            formData: {name:'promote_video',size_type:1},
            add: function (e,data) {
                $('video[nctype="promote_video"]').attr('poster', ADMIN_TEMPLATES_URL + '/images/loading.gif');
                $('video[nctype="promote_video"]').attr('src', "");
                data.submit();
            },
            pasteZone: null,
            done: function (e,data) {
                var param = data.result;
                if (typeof(param.error) != 'undefined') {
                    alert(param.error);
                    $('video[nctype="promote_video"]').attr('poster',DEFAULT_PROMOTE_VIDEO);
                    $('video[nctype="promote_video"]').attr('src', "");
                } else {
                    $('input[nctype="promote_video"]').val(param.name);
                    $('video[nctype="promote_video"]').attr('poster', "");
                    $('video[nctype="promote_video"]').attr('src',param.video_file);
                }
            }
        });
        /* 点播视频ajax上传 */
        $('#demand_video').fileupload({
            dataType: 'json',
            url: ADMIN_SITE_URL + '/index.php?act=mb_demand&op=video_upload&upload_type=demandfile',
            formData: {name:'demand_video',size_type:2},
            add: function (e,data) {
                $('video[nctype="demand_video"]').attr('poster', ADMIN_TEMPLATES_URL + '/images/loading.gif');
                $('video[nctype="demand_video"]').attr('src', "");
                data.submit();
            },
            pasteZone: null,
            done: function (e,data) {
                var param = data.result;
                if (typeof(param.error) != 'undefined') {
                    alert(param.error);
                    $('video[nctype="demand_video"]').attr('poster',DEFAULT_DEMAND_VIDEO);
                    $('video[nctype="demand_video"]').attr('src', "");
                } else {
                    $('input[nctype="demand_video"]').val(param.name);
                    $('video[nctype="demand_video"]').attr('poster', "");
                    $('video[nctype="demand_video"]').attr('src',param.video_file);
                }
            }
        });
        /* 推广位图片ajax上传 */
        $('#promote_image').fileupload({
            dataType: 'json',
            url: ADMIN_SITE_URL+'/index.php?act=mb_demand&op=image_upload&upload_type=promote_image',
            formData: {name:'promote_image'},
            add: function (e,data) {
                $('img[nctype="promote_image"]').attr('src', ADMIN_TEMPLATES_URL + '/images/loading.gif');
                data.submit();
            },
            done: function (e,data) {
                var param = data.result;
                if (typeof(param.error) != 'undefined') {
                    alert(param.error);
                    $('img[nctype="promote_image"]').attr('src',DEFAULT_GOODS_IMAGE);
                } else {
                    $('input[nctype="promote_image"]').val(param.name);
                    $('img[nctype="promote_image"]').attr('src',param.thumb_name);
                }
            }
        });

        $("#submitBtn").click(function(){
            if($("#post_form").valid()){
                $("#post_form").submit();
            }
        });


        $('#post_form').validate({
            errorPlacement: function(error, element){
                $(element).nextAll('span').append(error);
            },
            rules : {
                promote_text : {
                    required    : function() {if ($("#promote_0").prop("checked")) {return true;} else {return false;}},
                    maxlength   : 50
                },
                store : {
                    required    : true
                },
                video_cate : {
                    required    : true
                },
                promote_video_path : {
                    required	: function () {if ($('#promote_0').prop("checked")) {return true;} else {return false;}}
                },
                promote_image_path : {
                    required	: function () {if ($('#promote_1').prop("checked")) {return true;} else {return false;}}
                },
                demand_video_path : {
                    required    : function () {if ($('#promote_0').prop("checked")) {return true;} else {return false;}}
                }
            },
            messages : {
                promote_text  : {
                    required    : '<i class="fa fa-exclamation-circle"></i>推广位文字不能为空',
                    maxlength   : '<i class="fa fa-exclamation-circle"></i>推广位文字长度最长50个汉字'
                },
                store  : {
                    required    : '<i class="fa fa-exclamation-circle"></i>推荐店铺不能为空',
                },
                video_cate  : {
                    required    : '<i class="fa fa-exclamation-circle"></i>视频分类不能为空',
                },
                promote_video_path : {
                    required	: '<i class="fa fa-exclamation-circle"></i>推广位视频不能为空',
                },
                promote_image_path : {
                    required	: '<i class="fa fa-exclamation-circle"></i>推广位图片不能为空',
                },
                demand_video_path : {
                    required    : '<i class="fa fa-exclamation-circle"></i>点播视频不能为空',
                }
            }
        });
    });
</script>

