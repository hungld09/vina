<?php
    $cs        = Yii::app()->clientScript;
    $themePath = Yii::app()->theme->baseUrl;

    /**
     * StyleSheets
     */
    $cs->registerCssFile($themePath . '/css/bootstrap.min.css');
    $cs->registerCssFile($themePath . '/css/style.css');
    $cs->registerCssFile($themePath . '/css/custom.css');

    /**
     * JavaScripts
     */
    $cs->registerScriptFile($themePath . '/js/jquery.js', CClientScript::POS_HEAD);
    $cs->registerScriptFile($themePath . '/js/jquery.scrollTo-1.4.3.1-min.js', CClientScript::POS_HEAD);
    $cs->registerScriptFile($themePath . '/js/main.js', CClientScript::POS_HEAD);

    //<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    //<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>
    <meta name="title" content="<?php echo CHtml::encode($this->titlePage); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
    <title><?php echo CHtml::encode($this->titlePage); ?></title>

    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo $themePath ?>/css/ie.css" media="screen, projection"/>
    <![endif]-->
</head>
<body>
<div id="wrapper">
    <?php $this->renderPartial('/layouts/header'); ?>
    <div id="wrapper-inner" class="container">
        <?php echo $content; ?>
    </div>
    <?php
        $this->renderPartial('/layouts/footer');
    ?>
</div>
<!-- page -->
</body>
</html>