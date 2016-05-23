<?php
/**
 * Created by Lorge
 *
 * User: Only Love.
 * Date: 12/12/13 - 4:10 PM
 *
 * @var SiteController $this
 */
$currentUrl = $this->action->Id;
$currentAction = Yii::app()->controller->id . '/' . Yii::app()->controller->action->id;
$requestUrl = Yii::app()->request->getUrl();
//echo $currentAction;echo '<br>';
//echo $requestUrl;echo '<br>';
//echo $currentUrl;die;
?>
<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content="<?php echo $this->description; ?>" />
        <meta name="keywords" content="<?php echo $this->keywords; ?>" />
        <!-- css -->
        <link href="<?php echo Yii::app()->theme->baseUrl ?>/css/style.css" rel="stylesheet"/>
        <link href="<?php echo Yii::app()->theme->baseUrl ?>/css/bootstrap.min.css" rel="stylesheet"/>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
        <script language="javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/jquery.js"></script>
        <script language="javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/bootstrap.min.js"></script>
        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-75239897-1', 'auto');
            ga('send', 'pageview');

        </script>
    </head>
    <body>
        <div class="main">
            <ul class="menu">
                <li><a style="color: #124F84" href="https://hocde.vn/">Trang Chủ</a></li>
                <li class="huong_dan" style="position: relative">
                    <a style="color: #124F84" href="">Hướng Dẫn</a>
                    <ul class="huong_dan_2">
                        <li style="margin-top: 13px">
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/registerLogin' ?>">Đăng ký - đăng nhập</a>
                        </li>
                        <li>
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/searchAnswer' ?>">Tìm kiếm lời giải</a>
                        </li>
                        <li>
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/postQuestion' ?>">Gửi ảnh câu hỏi</a>
                        </li>
                        <li>
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/getAnswer' ?>">Nhận câu trả lời</a>
                        </li>
                        <li>
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/forgotPassword' ?>">Quên mật khẩu</a>
                        </li>
                        <li>
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/rechargeCard' ?>">Nạp thẻ</a>
                        </li>
                        <li style="border: none">
                            <a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/pc_laptop' ?>">PC/Laptop</a>
                        </li>
                    </ul>
                </li>
                <li><a style="color: #124F84" href="<?php echo Yii::app()->baseUrl . '/site/clause' ?>">Điều khoản dịch vụ</a></li>
            </ul>
            <div class="download">
                <a href="https://itunes.apple.com/us/app/hoc-de/id1053367970?mt=8#" ><img style="width: 190px;" src="<?php echo Yii::app()->theme->baseUrl ?>/img/button2.png"></a>
                <a href="https://play.google.com/store/apps/details?id=com.bkt.hocde" ><img style="width: 190px;" src="<?php echo Yii::app()->theme->baseUrl ?>/img/button1.png"></a>
            </div>
            <img style="width: 100%" src="<?php echo Yii::app()->theme->baseUrl ?>/img/header.jpg">
<?php
echo $content;
?>
            <div class="fotter">
                <img style="width: 100%" src="<?php echo Yii::app()->theme->baseUrl ?>/img/fotter.png">
            </div>
        </div>
        <div id="fb-root" style="position: fixed;bottom: 300px;right: 0px;">
            <button class="fbb1" style="display: none"> <img style="width: 30px" src="<?php echo Yii::app()->theme->baseUrl ?>/img/muiten.png" alt="" /> </button>
            <button class="fbb2"> <img style="width: 70px" src="<?php echo Yii::app()->theme->baseUrl ?>/img/chat_fb.png" alt="" /> </button>
        </div>
        <script>(function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.5";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>

        <div style="position:fixed; z-index:9999999; right:0px; bottom:0px; display: none" class="fb-page" data-tabs="messages" data-
             href="https://www.facebook.com/hocde.vn" data-width="250" data-height="300" data-small-header="false" data-adapt-
             container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false">
        </div>
    </body>
</html>
<script>
    $(".fbb2").click(function () {
        $(".fb-page").show();
        $(".fbb2").hide();
        $(".fbb1").show();
    });
    $(".fbb1").click(function () {
        $(".fb-page").hide();
        $(".fbb2").show();
        $(".fbb1").hide();
    });
</script>
