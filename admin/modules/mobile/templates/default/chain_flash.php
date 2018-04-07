<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>门店轮播</h3>
        <h5>门店配送模块首页轮播设置</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>点击添加新的广告条按钮可以添加新的广告条</li>
      <li>鼠标移动到已有的广告条上点击出现的删除按钮可以删除对应的广告条</li>
      <li>操作完成后点击保存编辑按钮进行保存</li>
    </ul>
  </div>
  <form id="form_item" action="<?php echo urlAdminMobile('chain_flash', 'save_flash');?>" method="post">
    <?php $item_data = $output['flash_info'];?>
    <div id="item_edit_content" class="mb-item-edit-content">
        <div class="index_block adv_list">
          <div nctype="item_content" class="content">
            <h5>内容：</h5>
            <?php if(!empty($item_data) && is_array($item_data)) {?>
            <?php foreach($item_data as $item_key => $item_value) {?>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo UPLOAD_SITE_URL . DS . ATTACH_MOBILE . DS . 'ad' . DS .$item_value['image'];?>" alt="">
              <input nctype="image_name" name="item_data[<?php echo $item_key;?>][image]" type="hidden" value="<?php echo $item_value['image'];?>">
              <input nctype="image_type" name="item_data[<?php echo $item_key;?>][type]" type="hidden" value="<?php echo $item_value['type'];?>">
              <input nctype="image_data" name="item_data[<?php echo $item_key;?>][data]" type="hidden" value="<?php echo $item_value['data'];?>">
              <a nctype="btn_del_item_image" href="javascript:;"><i class="fa fa-trash-o
        "></i>删除</a>
            </div>
            <?php } ?>
            <?php } ?>
          </div>
          <a nctype="btn_add_item_image" class="ncap-btn" data-desc="640*340" href="javascript:;"><i class="fa fa-plus"></i>添加新的广告条</a>
        </div>
    </div>
    <div class="bot"><a id="btn_save" class="ncap-btn-big ncap-btn-green" href="javascript:;">保存编辑</a> </div>
  </form>
</div>

<div id="dialog_item_edit_image" style="display:none;">
  <div class="s-tips"><i class="fa fa-lightbulb-o"></i>请按提示尺寸制作上传图片，以达到手机客户端及Wap手机商城最佳显示效果。</div>
  <div class="upload-thumb"> <img style="display: block;margin: 0 auto;" id="dialog_item_image" src="" alt=""></div>
  <input id="dialog_item_image_name" type="hidden">
  <input id="dialog_type" type="hidden">
  <form id="form_image" action="">
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">选择要上传的图片：</dt>
        <dd class="opt">
          <div class="input-file-show"><span class="type-file-box">
            <input type='text' name='textfield' id='textfield' class='type-file-text' />
            <input type='button' name='button' id='button' value='选择上传...' class='type-file-button' />
            <input id="btn_upload_image" type="file" name="flash_image" class="type-file-file" size="30" hidefocus="true" >
            </span> </div>
          <p id="dialog_image_desc" class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">操作类型：</dt>
        <dd class="opt">
          <select id="dialog_item_image_type" name="" class="vatop">
            <option value="">-请选择-</option>
            <option value="keyword">关键字</option>
            <option value="url">链接</option>
          </select>
          <input id="dialog_item_image_data" type="text" class="txt w200 marginright marginbot vatop">
          <p id="dialog_item_image_desc" class="notic"></p>
        </dd>
      </dl>
      <div class="bot"><a id="btn_save_item" class="ncap-btn-big ncap-btn-green" href="javascript:;">保存</a></div>
    </div>
  </form>
</div>
<script id="item_image_template" type="text/html">
    <div nctype="item_image" class="item">
        <img nctype="image" src="<%=image%>" alt="">
        <input nctype="image_name" name="item_data[<%=image_name%>][image]" type="hidden" value="<%=image_name%>">
        <input nctype="image_type" name="item_data[<%=image_name%>][type]" type="hidden" value="<%=image_type%>">
        <input nctype="image_data" name="item_data[<%=image_name%>][data]" type="hidden" value="<%=image_data%>">
        <a nctype="btn_del_item_image" href="javascript:;">删除</a>
    </div>
</script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/template.min.js" charset="utf-8"></script> 
<script type="text/javascript">
    var url_upload_image = '<?php echo urlAdminMobile('chain_flash', 'flash_image_upload');?>';

    $(document).ready(function(){
        var $current_content = null;
        var $current_image = null;
        var $current_image_name = null;
        var $current_image_type = null;
        var $current_image_data = null;
        var old_image = '';
        var $dialog_item_image = $('#dialog_item_image');
        var $dialog_item_image_name = $('#dialog_item_image_name');

        //保存
        $('#btn_save').on('click', function() {
            $('#form_item').submit();
        });

        //编辑图片
        $('[nctype="btn_edit_item_image"]').on('click', function() {
            //初始化当前图片对象
            $item_image = $(this).parents('[nctype="item_image"]');
            $current_image = $item_image.find('[nctype="image"]');
            $current_image_name = $item_image.find('[nctype="image_name"]');
            $current_image_type = $item_image.find('[nctype="image_type"]');
            $current_image_data = $item_image.find('[nctype="image_data"]');

            $('#dialog_item_image').attr('src', $current_image.attr('src'));
            $('#dialog_item_image_name').val($current_image_name.val());
            $('#dialog_item_image_type').val($current_image_type.val());
            $('#dialog_item_image_data').val($current_image_data.val());
            $('#dialog_image_desc').text('推荐图片尺寸' + $(this).attr('data-desc'));
            $('#dialog_type').val('edit');
            change_image_type_desc($('#dialog_item_image_type').val());
            $('#dialog_item_edit_image').nc_show_dialog({
                width: 600,
                title: '编辑'
            });
        });

        //添加图片
        $('[nctype="btn_add_item_image"]').on('click', function() {
            $dialog_item_image.hide();
            $dialog_item_image_name.val('');
            $current_content = $(this).parent().find('[nctype="item_content"]');
            $('#dialog_image_desc').text('推荐图片尺寸' + $(this).attr('data-desc'));
            $('#dialog_type').val('add');
            change_image_type_desc($('#dialog_item_image_type').val());
            $('#dialog_item_edit_image').nc_show_dialog({
                width: 600,
                title: '添加'
            });
        });

        //删除图片
        $('#item_edit_content').on('click', '[nctype="btn_del_item_image"]', function() {
            $(this).parents('[nctype="item_image"]').remove();
        });

        //图片上传
        $("#btn_upload_image").fileupload({
            dataType: 'json',
            url: url_upload_image,
            add: function(e, data) {
                old_image = $dialog_item_image.attr('src');
                $dialog_item_image.attr('src', LOADING_IMAGE);
                data.submit();
            },
            pasteZone: null,
            done: function (e, data) {
                var result = data.result;
                if(typeof result.error === 'undefined') {
                    $dialog_item_image.attr('src', result.image_url);
                    $dialog_item_image.show();
                    $dialog_item_image_name.val(result.image_name);
                } else {
                    $dialog_item_image.attr('src') = old_image;
                    showError(result.error);
                }
            }
        });

        $('#btn_save_item').on('click', function() {
            var type = $('#dialog_type').val();
            if(type == 'edit') {
                edit_item_image_save();
            } else {
                if($dialog_item_image_name.val() == '') {
                    showError('请上传图片');
                    return false;
                }
                add_item_image_save();
            }
            $('#dialog_item_edit_image').hide();
        });

        function edit_item_image_save() {
            $current_image.attr('src', $('#dialog_item_image').attr('src'));
            $current_image_name.val($('#dialog_item_image_name').val());
            $current_image_type.val($('#dialog_item_image_type').val());
            $current_image_data.val($('#dialog_item_image_data').val());
        }

        function add_item_image_save() {
            var $html_item_image = $('#html_item_image');
            var item = {};
            item.image = $('#dialog_item_image').attr('src');
            item.image_name = $('#dialog_item_image_name').val();
            item.image_type = $('#dialog_item_image_type').val();
            item.image_data = $('#dialog_item_image_data').val();
            $current_content.append(template.render('item_image_template', item));
        }


        $('#dialog_item_image_type').on('change', function() {
            change_image_type_desc($(this).val());
        });

        function change_image_type_desc(type) {
            var desc_array = {};
            var desc = '操作类型一共四种，对应点击以后的操作。';
            if(type != '') {
                desc_array['keyword'] = '关键字类型会根据搜索关键字跳转到搜索页面，输入框填写搜索关键字。';
                desc_array['url'] = '链接会跳转到指定链接，输入框填写完整的URL。';
                desc = desc_array[type];
            }
            $('#dialog_item_image_desc').text(desc);
        }
    });
    </script> 
