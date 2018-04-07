<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
    <div class="tabmenu">
        <?php include template('layout/submenu');?>
    </div>

    <div class="ncm-default-form">
        <form method="post" id="certification_form" action="<?php echo MEMBER_SITE_URL;?>/index.php?act=member_security&op=certification">
            <input type="hidden" name="form_submit" value="ok"  />
            <dl>
                <dt><i class="required">*</i>真实姓名：</dt>
                <dd>
                    <input type="text"  maxlength="20" name="real_name" id="real_name"/>
                    <label for="real_name" generated="true" class="error"></label>
               </dd>
            </dl>
            <dl>
                <dt><i class="required">*</i>身份证号码：</dt>
                <dd>
                    <input type="text" maxlength="20" name="id_card_code" id="id_card_code" />
                    <label for="id_card_code" generated="true" class="error"></label>
                </dd>
            </dl>
            <dl>
                <dt><i class="required">*</i>上传身份证正面：</dt>
                <dd>
                    <div class="user-avatar"><span><img id="pic1" src="" alt="" nc_type="avatar" height="260"/></span></div>
                    <div class="ncm-upload-btn">
                        <a href="javascript:void(0);"><span>
                        <input type="file" hidefocus="true" size="1" class="input-file" name="pic1" file_id="0" multiple="" maxlength="0"/>
                        </span>
                            <p><i class="icon-upload-alt"></i>图片上传</p>
                        </a>
                    </div>
                    <input type="hidden" name="id_card_img1">
                    <label for="id_card_img1" generated="true" class="error"></label>
                    <p class="hint mt5">请上传清晰彩色完整的原件扫描件或照片，身份证各项信息及头像清晰可见容易识别；证件必须真实拍摄，不能使用复印件。</p>
                </dd>
            </dl>
            <dl>
                <dt><i class="required">*</i>上传身份证反面：</dt>
                <dd>
                    <div class="user-avatar"><span><img id="pic2" src="" alt="" nc_type="avatar" height="260"/></span></div>
                    <div class="ncm-upload-btn">
                        <a href="javascript:void(0);"><span>
                        <input type="file" hidefocus="true" size="1" class="input-file" name="pic2" file_id="0" multiple="" maxlength="0"/>
                        </span>
                            <p><i class="icon-upload-alt"></i>图片上传</p>
                        </a>
                    </div>
                    <input type="hidden" name="id_card_img2">
                    <label for="id_card_img2" generated="true" class="error"></label>
                    <p class="hint mt5">请上传清晰彩色完整的原件扫描件或照片，身份证各项信息及头像清晰可见容易识别；证件必须真实拍摄，不能使用复印件。</p>
                </dd>
            </dl>
            <dl class="bottom">
                <dt>&nbsp;</dt>
                <dd><label class="submit-border">
                        <input type="submit" class="submit" value="确认提交" /></label>
                </dd>
            </dl>
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<script type="text/javascript">
    $(function(){
        //上传图片
        $('input[name=pic1]').fileupload({
            dataType: 'json',
            url: '<?php echo MEMBER_SITE_URL;?>/index.php?act=member_security&op=ajax_upload_image',
            formData: '',
            add: function (e,data) {
                data.submit();
            },
            done: function (e,data) {
                if (!data.result){
                    alert('上传失败，请尝试上传小图或更换图片格式');return;
                }
                if(data.result.state) {
                    $('#pic1').attr('src', data.result.pic_url);
                    $('input[name=id_card_img1]').val(data.result.pic_name);
                } else {
                    alert(data.result.message);
                }
            },
            fail: function(){
                alert('上传失败，请尝试上传小图或更换图片格式');
            }
        });
        $('input[name=pic2]').fileupload({
            dataType: 'json',
            url: '<?php echo MEMBER_SITE_URL;?>/index.php?act=member_security&op=ajax_upload_image',
            formData: '',
            add: function (e,data) {
                data.submit();
            },
            done: function (e,data) {
                if (!data.result){
                    alert('上传失败，请尝试上传小图或更换图片格式');return;
                }
                if(data.result.state) {
                    $('#pic2').attr('src', data.result.pic_url);
                    $('input[name=id_card_img2]').val(data.result.pic_name);
                } else {
                    alert(data.result.message);
                }
            },
            fail: function(){
                alert('上传失败，请尝试上传小图或更换图片格式');
            }
        });


        //身份证号码的验证规则
        function isIdCardNo(num){
            var len = num.length, re;
            if (len == 15)
                re = new RegExp(/^(\d{6})()?(\d{2})(\d{2})(\d{2})(\d{2})(\w)$/);
            else if (len == 18)
                re = new RegExp(/^(\d{6})()?(\d{4})(\d{2})(\d{2})(\d{3})(\w)$/);
            else {
                return false;
            }
            var a = num.match(re);
            if (a != null)
            {
                if (len==15)
                {
                    var D = new Date("19"+a[3]+"/"+a[4]+"/"+a[5]);
                    var B = D.getYear()==a[3]&&(D.getMonth()+1)==a[4]&&D.getDate()==a[5];
                }
                else
                {
                    var D = new Date(a[3]+"/"+a[4]+"/"+a[5]);
                    var B = D.getFullYear()==a[3]&&(D.getMonth()+1)==a[4]&&D.getDate()==a[5];
                }
                if (!B) {
                    return false;
                }
            }
            if(!re.test(num)){
                return false;
            }
            return true;
        }

        // 身份证号码验证
        jQuery.validator.addMethod("isIdCardNo", function(value, element) {
            return this.optional(element) || isIdCardNo(value);
        }, '<i class="icon-exclamation-sign"></i>请正确输入身份证号');

        $('#certification_form').validate({
            errorPlacement: function(error, element){
                element.nextAll('span').first().after(error);
            },
            rules : {
                real_name : {
                    required   : true
                },
                id_card_code : {
                    required   : true,
                    isIdCardNo : true
                },
                id_card_img1 : {
                    required : true
                },
                id_card_img2 : {
                    required : true
                }
            },
            messages : {
                real_name  : {
                    required  : '<i class="icon-exclamation-sign"></i>请正确输入真实姓名'
                },
                id_card_code : {
                    required   : '<i class="icon-exclamation-sign"></i>请正确输入身份证号'
                },
                id_card_img1 : {
                    required : '<i class="icon-exclamation-sign"></i>请上传身份证正面'
                },
                id_card_img2 : {
                    required : '<i class="icon-exclamation-sign"></i>请上传身份证反面'
                }
            }
        });

    });
</script>
