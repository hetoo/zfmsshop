<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
    <title><?php echo $output['html_title'];?></title>
    <meta name="keywords" content="<?php echo $output['seo_keywords']; ?>" />
    <meta name="description" content="<?php echo $output['seo_description']; ?>" />
    <meta name="author" content="ShopNC">
    <meta name="copyright" content="ShopNC Inc. All Rights Reserved">
    <meta name="renderer" content="webkit">
    <meta name="renderer" content="ie-stand">
    <?php echo html_entity_decode($output['setting_config']['qq_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['sina_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['share_qqzone_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['share_sinaweibo_appcode'],ENT_QUOTES); ?>

    <link href="<?php echo SHOP_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
    <link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_header.css" rel="stylesheet" type="text/css">
    <link href="<?php echo SHOP_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo SHOP_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome-ie7.min.css">
    <![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/html5shiv.js"></script>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/respond.min.js"></script>
    <![endif]-->
    <script>
        var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var LOGIN_SITE_URL = '<?php echo LOGIN_SITE_URL;?>';var MEMBER_SITE_URL = '<?php echo MEMBER_SITE_URL;?>';var SITEURL = '<?php echo SHOP_SITE_URL;?>';var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SHOP_TEMPLATES_URL;?>';
    </script>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
    <script type="text/javascript">
        var PRICE_FORMAT = '<?php echo $lang['currency'];?>%s';
        $(function(){
            //首页左侧分类菜单
            $(".category ul.menu").find("li").each(
                function() {
                    $(this).hover(
                        function() {
                            var cat_id = $(this).attr("cat_id");
                            var menu = $(this).find("div[cat_menu_id='"+cat_id+"']");
                            menu.show();
                            $(this).addClass("hover");
                            var menu_height = menu.height();
                            if (menu_height < 60) menu.height(80);
                            menu_height = menu.height();
                            var li_top = $(this).position().top;
                            $(menu).css("top",-li_top + 38);
                        },
                        function() {
                            $(this).removeClass("hover");
                            var cat_id = $(this).attr("cat_id");
                            $(this).find("div[cat_menu_id='"+cat_id+"']").hide();
                        }
                    );
                }
            );
            $(".head-user-menu dl").hover(function() {
                    $(this).addClass("hover");
                },
                function() {
                    $(this).removeClass("hover");
                });
            $('.head-user-menu .my-mall').mouseover(function(){// 最近浏览的商品
                load_history_information();
                $(this).unbind('mouseover');
            });
            $('.head-user-menu .my-cart').mouseover(function(){// 运行加载购物车
                load_cart_information();
                $(this).unbind('mouseover');
            });
            $('#button').click(function(){
                if ($('#keyword').val() == '') {
                    if ($('#keyword').attr('data-value') == '') {
                        return false
                    } else {
                        var op_val = $('#search_op').val();
                        window.location.href="<?php echo SHOP_SITE_URL?>/index.php?act=search&op="+op_val+"&keyword="+$('#keyword').attr('data-value');
                        return false;
                    }
                }
            });
            $(".head-search-bar").hover(null,
                function() {
                    $('#search-tip').hide();
                });
            // input ajax tips
            $('#keyword').focus(function(){
                if ($('#search_op').val() == 'index') {
                  $('#search-tip').show();
                }
            }).autocomplete({
                //minLength:0,
                source: function (request, response) {
                    $.getJSON('<?php echo SHOP_SITE_URL;?>/index.php?act=search&op=auto_complete', request, function (data, status, xhr) {
                        $('#top_search_box > ul').unwrap();
                        response(data);
                        if (status == 'success') {
                            $('#search-tip').hide();
                            $(".head-search-bar").unbind('mouseover');
                            $('body > ul:last').wrap("<div id='top_search_box'></div>").css({'zIndex':'1000','width':'362px'});
                        }
                    });
                },
                select: function(ev,ui) {
                    $('#keyword').val(ui.item.label);
                    $('#top_search_form').submit();
                }
            });
            $('#search-his-del').on('click',function(){$.cookie('<?php echo C('cookie_pre')?>his_sh',null,{path:'/'});$('#search-his-list').empty();});
        });
    </script>
</head>
<body>
<!-- PublicTopLayout Begin -->
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<?php if ($output['hidden_nctoolbar'] != 1) {?>
    <div id="ncToolbar" class="nc-appbar">
        <div class="nc-appbar-tabs" id="appBarTabs">
            <div class="ever">
                <?php if (!$output['hidden_rtoolbar_cart']) { ?>
                    <div class="cart"><a href="javascript:void(0);" id="rtoolbar_cart"><span class="icon"></span> <span class="name">购物车</span><i id="rtoobar_cart_count" class="new_msg" style="display:none;"></i></a></div>
                <?php } ?>
                <div class="chat"><a href="javascript:void(0);" id="chat_show_user"><span class="icon"></span><i id="new_msg" class="new_msg" style="display:none;"></i><span class="tit">在线联系</span></a></div>
            </div>
            <div class="variation">
                <div class="middle">
                    <?php if ($_SESSION['is_login']) {?>
                        <div class="user" nctype="a-barUserInfo">
                            <a href="javascript:void(0);">
                                <div class="avatar"><img src="<?php echo getMemberAvatar($_SESSION['avatar']);?>"/></div>
                                <span class="tit">我的账户</span>
                            </a></div>
                        <div class="user-info" nctype="barUserInfo" style="display:none;"><i class="arrow"></i>
                            <div class="avatar"><img src="<?php echo getMemberAvatar($_SESSION['avatar']);?>"/>
                                <div class="frame"></div>
                            </div>
                            <dl>
                                <dt>Hi, <?php echo $_SESSION['member_name'];?></dt>
                                <dd>当前等级：<strong nctype="barMemberGrade"><?php echo $output['member_info']['level_name'];?></strong></dd>
                                <dd>当前经验值：<strong nctype="barMemberExp"><?php echo $output['member_info']['member_exppoints'];?></strong></dd>
                            </dl>
                        </div>
                    <?php } else {?>
                        <div class="user" nctype="a-barLoginBox">
                            <a href="javascript:void(0);" >
                                <div class="avatar"><img src="<?php echo getMemberAvatar($_SESSION['avatar']);?>"/></div>
                                <span class="tit">会员登录</span>
                            </a>
                        </div>
                        <div class="user-login-box" nctype="barLoginBox" style="display:none;"> <i class="arrow"></i> <a href="javascript:void(0);" class="close-a" nctype="close-barLoginBox" title="关闭">X</a>
                            <form id="login_form" method="post" action="index.php?act=login&op=login" onsubmit="ajaxpost('login_form', '', '', 'onerror')">
                                <?php Security::getToken();?>
                                <input type="hidden" name="form_submit" value="ok" />
                                <input name="nchash" type="hidden" value="<?php echo getNchash('login','index');?>" />
                                <dl>
                                    <dt><strong>登录名</strong></dt>
                                    <dd>
                                        <input type="text" class="text" autocomplete="off"  name="user_name" autofocus >
                                        <label></label>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt><strong>登录密码</strong><a href="<?php echo urlLogin('login', 'forget_password');?>" target="_blank">忘记登录密码？</a></dt>
                                    <dd>
                                        <input type="password" class="text" name="password" autocomplete="off">
                                        <label></label>
                                    </dd>
                                </dl>
                                <?php if(C('captcha_status_login') == '1') { ?>
                                    <dl>
                                        <dt><strong>验证码</strong><a href="javascript:void(0)" class="ml5" onclick="javascript:document.getElementById('codeimage').src='index.php?act=seccode&op=makecode&nchash=<?php echo getNchash('login','index');?>&t=' + Math.random();">更换验证码</a></dt>
                                        <dd>
                                            <input type="text" name="captcha" autocomplete="off" class="text w130" id="captcha" maxlength="4" size="10" />
                                            <img src="" name="codeimage" border="0" id="codeimage" class="vt">
                                            <label></label>
                                        </dd>
                                    </dl>
                                <?php } ?>
                                <div class="bottom">
                                    <input type="submit" class="submit" value="确认">
                                    <input type="hidden" value="<?php echo $_GET['ref_url']?>" name="ref_url">
                                    <a href="<?php echo urlLogin('login', 'register', array('ref_url' => $output['ref_url']));?>" target="_blank">注册新用户</a>
                                    <?php if (C('weixin_isuse') == 1){?>
                                        <a href="javascript:void(0);" onclick="weixin_login();" title="微信账号登录" class="mr20">微信</a>
                                    <?php } ?>
                                    <?php if (C('sina_isuse') == 1){?>
                                        <a href="<?php echo MEMBER_SITE_URL;?>/index.php?act=connect_sina&ref_url=<?php echo urlencode($output['ref_url']);?>" title="新浪微博账号登录" class="mr20">新浪微博</a>
                                    <?php } ?>
                                    <?php if (C('qq_isuse') == 1){?>
                                        <a href="<?php echo MEMBER_SITE_URL;?>/index.php?act=connect_qq&ref_url=<?php echo urlencode($output['ref_url']);?>" title="QQ账号登录" class="mr20">QQ账号</a>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    <?php }?>
                    <div class="prech">&nbsp;</div>
                    <?php if (!$output['hidden_rtoolbar_compare']) { ?>
                        <div class="compare"><a href="javascript:void(0);" id="compare"><span class="icon"></span><span class="tit">商品对比</span></a></div>
                    <?php } ?>
                </div>
                <div class="gotop"><a href="javascript:void(0);" id="gotop"><span class="icon"></span><span class="tit">返回顶部</span></a></div>
            </div>
            <div class="content-box" id="content-compare">
                <div class="top">
                    <h3>商品对比</h3>
                    <a href="javascript:void(0);" class="close" title="隐藏"></a></div>
                <div id="comparelist"></div>
            </div>
            <div class="content-box" id="content-cart">
                <div class="top">
                    <h3>我的购物车</h3>
                    <a href="javascript:void(0);" class="close" title="隐藏"></a></div>
                <div id="rtoolbar_cartlist"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        //登录开关状态
        var connect_qq = "<?php echo C('qq_isuse');?>";
        var connect_sn = "<?php echo C('sina_isuse');?>";
        var connect_wx = "<?php echo C('weixin_isuse');?>";

        var connect_weixin_appid = "<?php echo C('weixin_appid');?>";
        //返回顶部
        backTop=function (btnId){
            var btn=document.getElementById(btnId);
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            window.onscroll=set;
            btn.onclick=function (){
                btn.style.opacity="0.5";
                window.onscroll=null;
                this.timer=setInterval(function(){
                    scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                    scrollTop-=Math.ceil(scrollTop*0.1);
                    if(scrollTop==0) clearInterval(btn.timer,window.onscroll=set);
                    if (document.documentElement.scrollTop > 0) document.documentElement.scrollTop=scrollTop;
                    if (document.body.scrollTop > 0) document.body.scrollTop=scrollTop;
                },10);
            };
            function set(){
                scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                btn.style.opacity=scrollTop?'1':"0.5";
            }
        };
        backTop('gotop');

        //动画显示边条内容区域
        $(function() {
            ncToolbar();
            $(window).resize(function() {
                ncToolbar();
            });
            function ncToolbar() {
                if ($(window).width() >= 1240) {
                    $('#appBarTabs >.variation').show();
                } else {
                    $('#appBarTabs >.variation').hide();
                }
            }
            $('#appBarTabs').hover(
                function() {
                    $('#appBarTabs >.variation').show();
                },
                function() {
                    ncToolbar();
                }
            );
            $("#compare").click(function(){
                if ($("#content-compare").css('right') == '-210px') {
                    loadCompare(false);
                    $('#content-cart').animate({'right': '-210px'});
                    $("#content-compare").animate({right:'35px'});
                } else {
                    $(".close").click();
                    $(".chat-list").css("display",'none');
                }
            });
            $("#rtoolbar_cart").click(function(){
                if ($("#content-cart").css('right') == '-210px') {
                    $('#content-compare').animate({'right': '-210px'});
                    $("#content-cart").animate({right:'35px'});
                    if (!$("#rtoolbar_cartlist").html()) {
                        $("#rtoolbar_cartlist").load('index.php?act=cart&op=ajax_load&type=html');
                    }
                } else {
                    $(".close").click();
                    $(".chat-list").css("display",'none');
                }
            });
            $(".close").click(function(){
                $(".content-box").animate({right:'-210px'});
            });

            $(".quick-menu dl").hover(function() {
                    $(this).addClass("hover");
                },
                function() {
                    $(this).removeClass("hover");
                });

            // 右侧bar用户信息
            $('div[nctype="a-barUserInfo"]').click(function(){
                $('div[nctype="barUserInfo"]').toggle();
            });
            // 右侧bar登录
            $('div[nctype="a-barLoginBox"]').click(function(){
                $('div[nctype="barLoginBox"]').toggle();
                document.getElementById('codeimage').src='index.php?act=seccode&op=makecode&nchash=<?php echo getNchash('login','index');?>&t=' + Math.random();
            });
            $('a[nctype="close-barLoginBox"]').click(function(){
                $('div[nctype="barLoginBox"]').toggle();
            });
            <?php if ($output['cart_goods_num'] > 0) { ?>
            $('#rtoobar_cart_count').html(<?php echo $output['cart_goods_num'];?>).show();
            <?php } ?>
        });
    </script>
<?php } ?>
<div class="public-top-layout w">
    <div class="topbar wrapper">
        <div class="user-entry">
            <?php if($_SESSION['is_login'] == '1'){?>
                <?php echo $lang['nc_hello'];?> <span> <a href="<?php echo urlShop('member','home');?>" target="_blank"><?php echo $_SESSION['member_name'];?></a>
                    <?php if ($output['member_info']['level_name']){ ?>
                        <div class="nc-grade-mini" style="cursor:pointer;" onclick="javascript:go('<?php echo urlShop('pointgrade','index');?>');"><?php echo $output['member_info']['level_name'];?></div>
                    <?php } ?>
      </span> <?php echo $lang['nc_comma'],$lang['welcome_to_site'];?> <a href="<?php echo SHOP_SITE_URL;?>"  title="<?php echo $lang['homepage'];?>" alt="<?php echo $lang['homepage'];?>"><span><?php echo $output['setting_config']['site_name']; ?></span></a> <span>[<a href="<?php echo urlLogin('login','logout');?>" target="_blank"><?php echo $lang['nc_logout'];?></a>] </span>
            <?php }else{?>
                <?php echo $lang['nc_hello'].$lang['nc_comma'].$lang['welcome_to_site'];?> <a href="<?php echo SHOP_SITE_URL;?>" title="<?php echo $lang['homepage'];?>" alt="<?php echo $lang['homepage'];?>" target="_blank"><?php echo $output['setting_config']['site_name']; ?></a> <span>[<a href="<?php echo urlLogin('login');?>" target="_blank"><?php echo $lang['nc_login'];?></a>]</span> <span>[<a href="<?php echo urlLogin('login','register');?>" target="_blank"><?php echo $lang['nc_register'];?></a>]</span>
            <?php }?>
        </div>
        <div class="quick-menu">
            <dl>
                <dt><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_order" target="_blank">我的订单</a><i></i></dt>
                <dd>
                    <ul>
                        <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_order&state_type=state_new" target="_blank">待付款订单</a></li>
                        <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_order&state_type=state_send" target="_blank">待确认收货</a></li>
                        <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_order&state_type=state_noeval" target="_blank">待评价交易</a></li>
                    </ul>
                </dd>
            </dl>
            <dl>
                <dt><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_favorite_goods&op=fglist" target="_blank"><?php echo $lang['nc_favorites'];?></a><i></i></dt>
                <dd>
                    <ul>
                        <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_favorite_goods&op=fglist" target="_blank">商品收藏</a></li>
                        <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_favorite_store&op=fslist" target="_blank">店铺收藏</a></li>
                    </ul>
                </dd>
            </dl>
            <dl>
                <dt>客户服务<i></i></dt>
                <dd>
                    <ul>
                        <li><a href="<?php echo urlMember('article', 'article', array('ac_id' => 2));?>" target="_blank">帮助中心</a></li>
                        <li><a href="<?php echo urlMember('article', 'article', array('ac_id' => 5));?>" target="_blank">售后服务</a></li>
                        <li><a href="<?php echo urlMember('article', 'article', array('ac_id' => 6));?>" target="_blank">客服中心</a></li>
                    </ul>
                </dd>
            </dl>
            <?php
            if(!empty($output['nav_list']) && is_array($output['nav_list'])){
                foreach($output['nav_list'] as $nav){
                    if($nav['nav_location']<1){
                        $output['nav_list_top'][] = $nav;
                    }
                }
            }
            if(!empty($output['nav_list_top']) && is_array($output['nav_list_top'])){
                ?>
                <dl>
                    <dt>站点导航<i></i></dt>
                    <dd>
                        <ul>
                            <?php foreach($output['nav_list_top'] as $nav){?>
                                <li><a
                                        <?php
                                        echo ' href="';
                                        switch($nav['nav_type']) {
                                            case '0':echo $nav['nav_url'];break;
                                            case '1':echo urlShop('search', 'index', array('cate_id'=>$nav['item_id']));break;
                                            case '2':echo urlMember('article', 'article', array('ac_id'=>$nav['item_id']));break;
                                            case '3':echo urlShop('activity', 'index', array('activity_id'=>$nav['item_id']));break;
                                        }
                                        echo '"';
                                        ?> target="_blank"><?php echo $nav['nav_title'];?></a></li>
                            <?php }?>
                        </ul>
                    </dd>
                </dl>
            <?php } ?>
            <?php if (C('mobile_wx')) { ?>
                <dl class="weixin">
                    <dt>关注我们<i></i></dt>
                    <dd>
                        <h4>扫描二维码<br/>
                            关注商城微信号</h4>
                        <img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.C('mobile_wx');?>" > </dd>
                </dl>
            <?php } ?>
        </div>
    </div>
</div>
<!-- PublicHeadLayout Begin -->
<div class="header-wrap">
    <header class="public-head-layout wrapper">
        <h1 class="site-logo"><a href="<?php echo SHOP_SITE_URL;?>"><img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_COMMON.DS.$output['setting_config']['site_logo']; ?>" class="pngFix"></a></h1>
        <?php if (C('mobile_isuse') && C('mobile_app')){?>
            <div class="head-app"><span class="pic"></span>
                <div class="download-app">
                    <div class="qrcode"><img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_COMMON.DS.C('mobile_app');?>" ></div>
                    <div class="hint">
                        <h4>扫描二维码</h4>
                        下载手机客户端</div>
                    <div class="addurl">
                        <?php if (C('mobile_apk')){?>
                            <a href="<?php echo C('mobile_apk');?>" target="_blank"><i class="icon-android"></i>Android</a>
                        <?php } ?>
                        <?php if (C('mobile_ios')){?>
                            <a href="<?php echo C('mobile_ios');?>" target="_blank"><i class="icon-apple"></i>iPhone</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="head-search-layout">
            <div class="head-search-bar" id="head-search-bar">
                <script type="text/javascript">
                $(function(){
                    $(".searchMenu").hover(function(){
                        $("#searchTab").show();
                        $(this).addClass("searchOpen");
                        $("#i1").attr("class","up block");
                    },function(){
                        $(this).removeClass("searchOpen");
                        $("#i1").removeAttr("class","up block").attr("class","down block");
                        $("#searchTab").hide();
                    });
                    $("#searchTab li").hover(function(){
                      $(this).addClass("selected")},function(){
                        $(this).removeClass("selected")}
                    );
                    $("#searchTab li").click(function(){
                          if ($(this).attr('id') == 'search_goods'){
                              $('#search_op').val('index');
                              $('#keyword').attr('placeholder','请输入您要搜索的商品关键字');
                          }
                          if ($(this).attr('id') == 'search_store'){
                             $('#search_op').val('store');
                             $('#search-tip').hide();
                             $('#keyword').attr('placeholder','请输入您要搜索的店铺关键字');
                          }
                          $("#searchSelected").html($(this).html());
                          $("#searchTab").hide();
                          $("#i1").attr("class","down block");
                          $("#searchSelected").removeClass("searchOpen");
                    });
                });    
                </script>
                <form action="<?php echo SHOP_SITE_URL;?>" method="get" class="search" id="top_search_form">
                    <input name="act" id="search_act" value="search" type="hidden">
                    <input name="op" id="search_op" value="index" type="hidden">
                    <div id="searchTxt" class="searchTxt" onmouseout="this.className='searchTxt';" onmouseover="this.className='searchTxt searchTxtHover';">
                        <div class="searchMenu">
                          <div id="searchSelected" class="searchSelected">商品</div>
                          <i id="i1" class="down block"></i>
                          <div id="searchTab" class="searchTab" style="display: none;">
                            <ul>
                              <li id="search_goods" class="">商品</li>
                              <li id="search_store" class="">店铺</li>
                            </ul>
                          </div>
                        </div>
                        <input id="keyword" class="ui-autocomplete-input" placeholder="请输入您要搜索的商品关键字" value="" name="keyword" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" type="text">
                    </div>
                    <div class="searchBtn">
                        <button id="searchBtn" type="submit">搜索</button>
                    </div>
                </form>
                <div class="search-tip" id="search-tip">
                    <div class="search-history">
                        <div class="title">历史记录<a href="javascript:void(0);" id="search-his-del">清除</a></div>
                        <ul id="search-his-list">
                            <?php if (is_array($output['his_search_list']) && !empty($output['his_search_list'])) { ?>
                                <?php foreach($output['his_search_list'] as $v) { ?>
                                    <li><a href="<?php echo urlShop('search', 'index', array('keyword' => $v));?>" target="_blank"><?php echo $v ?></a></li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="search-hot">
                        <div class="title">热门搜索...</div>
                        <ul>
                            <?php if (is_array($output['rec_search_list']) && !empty($output['rec_search_list'])) { ?>
                                <?php foreach($output['rec_search_list'] as $v) { ?>
                                    <li><a href="<?php echo urlShop('search', 'index', array('keyword' => $v['value']));?>" target="_blank"><?php echo $v['value']?></a></li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="keyword">
                <ul>
                    <?php if(is_array($output['hot_search']) && !empty($output['hot_search'])) { foreach($output['hot_search'] as $val) { ?>
                        <li><a href="<?php echo urlShop('search', 'index', array('keyword' => $val));?>" target="_blank"><?php echo $val; ?></a></li>
                    <?php } }?>
                </ul>
            </div>
        </div>
        <div class="head-user-menu">
            <dl class="my-mall">
                <dt><span class="ico"></span>我的商城<i class="arrow"></i></dt>
                <dd>
                    <div class="sub-title">
                        <h4><?php echo $_SESSION['member_name'];?>
                            <?php if ($output['member_info']['level_name']){ ?>
                                <div class="nc-grade-mini" style="cursor:pointer;" onclick="javascript:go('<?php echo urlShop('pointgrade','index');?>');"><?php echo $output['member_info']['level_name'];?></div>
                            <?php } ?>
                        </h4>
                        <a href="<?php echo urlShop('member', 'home');?>" class="arrow" target="_blank">我的用户中心<i></i></a></div>
                    <div class="user-centent-menu">
                        <ul>
                            <li><a href="<?php echo MEMBER_SITE_URL;?>/index.php?act=member_message&op=message" target="_blank">站内消息(<span><?php echo $output['message_num']>0 ? $output['message_num']:'0';?></span>)</a></li>
                            <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_order" class="arrow" target="_blank">我的订单<i></i></a></li>
                            <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_consult&op=my_consult" target="_blank">咨询回复(<span id="member_consult">0</span>)</a></li>
                            <li><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_favorite_goods&op=fglist" class="arrow" target="_blank">我的收藏<i></i></a></li>
                            <?php if (C('voucher_allow') == 1){?>
                                <li><a href="<?php echo MEMBER_SITE_URL;?>/index.php?act=member_voucher" target="_blank">代金券(<span id="member_voucher">0</span>)</a></li>
                            <?php } ?>
                            <?php if (C('points_isuse') == 1){ ?>
                                <li><a href="<?php echo MEMBER_SITE_URL;?>/index.php?act=member_points" class="arrow" target="_blank">我的积分<i></i></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="browse-history">
                        <div class="part-title">
                            <h4>最近浏览的商品</h4>
                            <span style="float:right;"><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=member_goodsbrowse&op=list" target="_blank">全部浏览历史</a></span> </div>
                        <ul>
                            <li class="no-goods"><img class="loading" src="<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif" /></li>
                        </ul>
                    </div>
                </dd>
            </dl>
            <dl class="my-cart">
                <?php if ($output['cart_goods_num'] > 0) { ?>
                    <div class="addcart-goods-num"><?php echo $output['cart_goods_num'];?></div>
                <?php } ?>
                <dt><span class="ico"></span>购物车结算<i class="arrow"></i></dt>
                <dd>
                    <div class="sub-title">
                        <h4>最新加入的商品</h4>
                    </div>
                    <div class="incart-goods-box">
                        <div class="incart-goods"> <img class="loading" src="<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif" /> </div>
                    </div>
                    <div class="checkout"> <span class="total-price">共<i><?php echo $output['cart_goods_num'];?></i><?php echo $lang['nc_kindof_goods'];?></span><a href="<?php echo SHOP_SITE_URL;?>/index.php?act=cart" class="btn-cart" target="_blank">结算购物车中的商品</a> </div>
                </dd>
            </dl>
        </div>
    </header>
</div>
<!-- PublicHeadLayout End -->

<!-- publicNavLayout Begin -->
<nav class="public-nav-layout <?php if($output['channel']) {echo 'channel-'.$output['channel']['channel_style'].' channel-'.$output['channel']['channel_id'];} ?>">
    <div class="wrapper">
        <div class="all-category">
            <?php require template('layout/home_goods_class');?>
        </div>
        <ul class="site-menu">
            <li><a href="<?php echo SHOP_SITE_URL;?>" <?php if($output['index_sign'] == 'index' && $output['index_sign'] != '0') {echo 'class="current"';} ?> target="_blank"><?php echo $lang['nc_index'];?></a></li>
            <?php if (C('groupbuy_allow')){ ?>
                <li><a href="<?php echo urlShop('show_groupbuy', 'index');?>" <?php if($output['index_sign'] == 'groupbuy' && $output['index_sign'] != '0') {echo 'class="current"';} ?> target="_blank"> <?php echo $lang['nc_groupbuy'];?></a></li>
            <?php } ?>
            <li><a href="<?php echo urlShop('brand', 'index');?>" <?php if($output['index_sign'] == 'brand' && $output['index_sign'] != '0') {echo 'class="current"';} ?> target="_blank"> <?php echo $lang['nc_brand'];?></a></li>
            <?php if (C('points_isuse') && C('pointshop_isuse')){ ?>
                <li><a href="<?php echo urlShop('pointshop', 'index');?>" <?php if($output['index_sign'] == 'pointshop' && $output['index_sign'] != '0') {echo 'class="current"';} ?> target="_blank"> <?php echo $lang['nc_pointprod'];?></a></li>
            <?php } ?>
            <?php if (C('cms_isuse')){ ?>
                <li><a href="<?php echo urlShop('special', 'special_list');?>" <?php if($output['index_sign'] == 'special' && $output['index_sign'] != '0') {echo 'class="current"';} ?> target="_blank"> 专题</a></li>
            <?php } ?>
            <?php if(!empty($output['nav_list']) && is_array($output['nav_list'])){?>
                <?php foreach($output['nav_list'] as $nav){?>
                    <?php if($nav['nav_location'] == '1'){?>
                        <li><a
                                <?php
                                switch($nav['nav_type']) {
                                    case '0':
                                        echo ' href="' . $nav['nav_url'] . '"';
                                        break;
                                    case '1':
                                        echo ' href="' . urlShop('search', 'index',array('cate_id'=>$nav['item_id'])) . '"';
                                        if (isset($_GET['cate_id']) && $_GET['cate_id'] == $nav['item_id']) {
                                            echo ' class="current"';
                                        }
                                        break;
                                    case '2':
                                        echo ' href="' . urlMember('article', 'article',array('ac_id'=>$nav['item_id'])) . '"';
                                        if (isset($_GET['ac_id']) && $_GET['ac_id'] == $nav['item_id']) {
                                            echo ' class="current"';
                                        }
                                        break;
                                    case '3':
                                        echo ' href="' . urlShop('activity', 'index', array('activity_id'=>$nav['item_id'])) . '"';
                                        if (isset($_GET['activity_id']) && $_GET['activity_id'] == $nav['item_id']) {
                                            echo ' class="current"';
                                        }
                                        break;
                                }
                                ?> target="_blank"><?php echo $nav['nav_title'];?></a></li>
                    <?php }?>
                <?php }?>
            <?php }?>
        </ul>
    </div>
</nav>
<div class="nch-breadcrumb-layout">
    <?php if(!empty($output['nav_link_list']) && is_array($output['nav_link_list'])){?>
        <div class="nch-breadcrumb wrapper"><i class="icon-home"></i>
            <?php foreach($output['nav_link_list'] as $nav_link){?>
                <?php if(!empty($nav_link['link'])){?>
                    <span><a href="<?php echo $nav_link['link'];?>" target="_blank"><?php echo $nav_link['title'];?></a></span><span class="arrow">></span>
                <?php }else{?>
                    <span><?php echo $nav_link['title'];?></span>
                <?php }?>
            <?php }?>
        </div>
    <?php }?>
</div>
<?php require_once($tpl_file);?>
<?php require_once template('footer');?>
</body>
</html>