<?php /* @var $this Controller */ ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta name="description" content="MPhim, movies on your mobile">
    <meta name="author" content="Nha Tran">
    <!--Mobile specific meta goodness :)-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!--css-->
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl ?>/css/simple.css">
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Favicons-->
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl;?>/images/film.png">
</head>
<body id="home">
    <div class="wrapper">
        <header>
            <h1 class="logo"><?php echo CHtml::link("mobiphim", array("/mphim")); ?></h1>
            <a class="to_nav" href="#primary_nav">Menu</a>
        </header>
        <marquee>Để đăng ký sử dụng dịch vụ, soạn tin DK TênGói gửi 9033.</marquee>
        
        <?php echo $content ?>
        
        <nav id="primary_nav">
		    <ul>
		        <li><?php echo CHtml::link("Trang chủ", array("/mphim")); ?></li>
		        <li><?php echo CHtml::link("Thể loại", array("/mphim/category")); ?></li>
		        <li><a href="#">Tài khoản</a></li>
		        <li class="top"><a href="#home">Về đầu trang</a></li>
		    </ul>
		</nav><!--end primary_nav-->
        <footer>
            <p>Copyright &copy;2012 mPhim - Product of <a href="http://www.Vinaphone.com.vn" target="blank">VMS Vinaphone</a></p>
        </footer>
    </div><!--end wrapper-->
</body>
</html>