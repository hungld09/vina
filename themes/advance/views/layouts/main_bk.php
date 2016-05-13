<?php
$this->widget("application.widgets.Header", array());
?>
<?php
$requestUrl = Yii::app()->request->getUrl() . '/' . Yii::app()->controller->action->id;
//$currentUrl = Yii::app()->request->getUrl();
$currentAction = '/hocde.vn/questionBank/index';
//echo $requestUrl;die;
?>
<!DOCTYPE HTML>
<html class="ui-mobile">
<head id="Head1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8"/>
    <meta http-equiv="encoding" content="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta content="width=device-width, initial-scale=1" name="viewport" id="viewport"/>
    <title><?php echo $this->titlePage; ?></title>
    <link rel="icon" href="<?php echo Yii::app()->theme->baseUrl . '/FileManager/favicon.jpg' ?>" />
    <!-- Bootstrap -->
    <link href="<?php echo Yii::app()->theme->baseUrl ?>/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo Yii::app()->theme->baseUrl ?>/css/bootstrap-select.css" rel="stylesheet"/>
    <link href="<?php echo Yii::app()->theme->baseUrl ?>/css/reset.css" rel="stylesheet"/>
    <link href="<?php echo Yii::app()->theme->baseUrl ?>/Plugin/Fancybox/fancybox.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl ?>/css/style.css"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]>
    <style type="text/css">
        .gradient {
            filter: none;
        }
    </style>
    <![endif]-->
    <?php if ($requestUrl != $currentAction) { ?>
        <?php if ($requestUrl != '/questionBank/index') { ?>
            <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/jquery.js"></script>
        <?php }
    } ?>
    <?php if ($requestUrl == $currentAction) { ?>
        <?php if ($requestUrl != '/hocde.vn/questionBank/index') { ?>
            <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/jquery.js"></script>
        <?php }
    } ?>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/Plugin/Fancybox/fancybox.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/jquery.touchSwipe.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/site.js"></script>
    <script>
        window.fbAsyncInit = function () {
            FB.init({
                appId: '891467350909145',
                xfbml: true,
                version: 'v2.5'
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
</head>
<body>
<div id="tpl-contaiter">
    <!-- Begin Header -->
    <div id="tpl-header">
        <div class="top_header">
            <div class="row">
                <div class="rightmenu col-md-2 col-sm-2 col-xs-2 text-center">
                    <a class="menu-right ui-link" href=""><img
                            src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/ic_menu_1.png"/></a>
                </div>
                <div class="center col-md-8 col-sm-8 col-xs-8 text-center"
                     style="margin-top: 4px;font-size: 20px;"><?php echo $this->titlePage; ?></div>
                <div class="search col-md-2 col-sm-2 col-xs-2 text-center">
                    <?php if($this->userName != null){ ?>
                        <a href="<?php echo Yii::app()->baseUrl .'/questionBank'?>"><img
                            src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/icon_search.png" class="icon-search1"/>
                        </a>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div id="tpl-main">
        <div class="tpl-main-middle">
            <div class="webpart">
                <?php
                echo $content;
                ?>
            </div>
        </div>
    </div>
    <!--End Main-->
    <!-- End Footer-->
    <?php
    if ($this->userName != null) {
        $uid = $this->userName->id;
    } else {
        $uid = -1;
    }
    $this->widget("application.widgets.SearchBox", array());
    $this->widget("application.widgets.NavMenu", array('userName' => $this->userName));
    ?>
</div>
<script>
    $('body a.comment-home, body .like a, .comment a, .name-title a, .name-detail a, body .uploadQuestion a').click(function () {
        var a = <?php echo $uid ?>;
        if (a == -1) {
            if (confirm("Bạn phải đăng nhập để sử dụng")) {
                window.location.href = "<?php echo Yii::app()->baseUrl .'/account/'?>";
            }
            return false;
        }

    });
</script>
</body>
</html>