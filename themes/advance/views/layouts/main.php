<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0;
        maximum-scale=1.0; user-scalable=0;"/>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl . '/css/vina/magnific-popup.css' ?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl . '/css/vina/styles.css' ?>"/>
        <title>VinaPhone</title>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl . '/js/vina/jquery-1.10.2.js' ?>"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl . '/js/vina/jquery.magnific-popup.min.js' ?>"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl . '/js/vina/main.js' ?>"></script>
        <style type="text/css">
            div.title span:after{ position: absolute; content: ""; background: url(<?php echo Yii::app()->theme->baseUrl ?>/images/title-after.png) no-repeat left top; width: 30px; height: 30px; color: #fff; font-size: 14px; text-transform: uppercase; right: 0; }
            .sub-menu ul li:first-child::before{ position: absolute; top: 10px; left: -29px; content:""; width: 22px; height: 19px; background: url(<?php echo Yii::app()->theme->baseUrl ?>/images/icon-home.png) no-repeat left top; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <?php $this->widget("application.widgets.Header", array()); ?>
            <!-- end header -->
            <div class="content-warp">
                <div class="content">
                    <?php $this->widget("application.widgets.Popup", array('msisdn'=>$this->msisdn, 'usingServices'=>$this->usingServices)); ?>
                    <?php echo $content ?>
                    <div class="lienket">
                        liên kết: <a href ="http://vinaphone.com.vn"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/vinaphone.png" alt="" /></a>
                    </div>
                </div>
            </div>
            <?php $this->widget("application.widgets.Footer", array()); ?>
        </div>
        <div class="uploadQuestion" style="position: fixed;z-index: 999999;bottom: 4%;right:15%;">
            <a class="" href="<?php echo Yii::app()->baseUrl . '/question/upload' ?>"><img width="65" src="<?php echo Yii::app()->theme->baseUrl .'/img/hoi.png'?>" alt="" /></a>
        </div>
    </body>
    
</html>
