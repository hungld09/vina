<?php
$this->title = 'Hướng dẫn giải bài tập toán, lý, hóa online | Học Dễ OnEdu';
$this->description = 'Học dễ Onedu, Hoc de Onedu là ứng dụng hỗ trợ hướng dẫn giải bài tập toán, giải bài tập vật lý, giải bài tập hóa học online nhanh, hiệu quả nhất';
$this->keywords = 'học dễ, hoc de, học dễ onedu, hoc de onedu, giải bài tập toán, giải bài tập vật lý, giải bài tập hóa học, hướng dẫn giải bài tập, đáp án bài tập, để học tốt';
?>
<!--slider-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Học dễ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <!-- css -->
    <!--<link href="<?php echo Yii::app()->theme->baseUrl ?>/css/style.css" rel="stylesheet"/>-->
    <!--<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>-->

</head>
<body style="margin: 0">
<div class="main" style="position: relative">
    <div class="download" style="position: absolute;top: 630px;left: 211px;">
        <a href="https://itunes.apple.com/us/app/hoc-de/id1053367970?mt=8#" ><img style="width: 190px;height: 57px;" src="<?php echo Yii::app()->theme->baseUrl ?>/img/button2.png"></a>
        <a href="https://play.google.com/store/apps/details?id=com.bkt.hocde" ><img style="width: 190px;height: 57px;" src="<?php echo Yii::app()->theme->baseUrl ?>/img/button1.png"></a>
        <a href="https://www.hocde.vn/" ><img style="width: 190px;" src="<?php echo Yii::app()->theme->baseUrl ?>/img/iconwap.png"></a>
    </div>
    <img style="width: 100%" src="<?php echo Yii::app()->theme->baseUrl ?>/img/<?php echo $imageName; ?>">
</div>
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.5";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<div style="position:fixed; z-index:9999999; right:10px; bottom:10px;" class="fb-page" data-tabs="messages" data-

     href="https://www.facebook.com/hocde.vn" data-width="250" data-height="300" data-small-header="false" data-adapt-

     container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"></div>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-69873321-1', 'auto');
    ga('send', 'pageview');

</script>
</body>
</html>
