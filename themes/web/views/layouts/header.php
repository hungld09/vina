<header>
    <div class="container">
        <h1>
            <?php echo CHtml::link(CHtml::image(Yii::app()->theme->baseUrl . '/images/logo.png', ''), Yii::app()->createUrl('/'), array('class' => 'logo')) ?>
        </h1>
        <div class="pull-right text-right">
            <div class="nav-menu">
                <a class="link-menu" href="javascript:;">Vinaphone Portal</a>
                <a class="link-menu" href="javascript:;">CSKH</a>
                <a class="link-menu color-red" href="javascript:;">Đăng nhập</a>
            </div>
            <div class="clearfix"></div>
            <div class="wellcome">
            <?php if($this->msisdn != '' && $this->usingServices == null){?>
                Xin chào thuê bao 0911321055! Vui lòng <a href="#" class="color-red">Đăng ký</a> để nhận Xem video bài giảng miễn phí mỗi ngày
            <?php }else if($this->msisdn != '' && $this->usingServices != null){ ?>
               Xin chào thuê bao <?php echo $this->msisdn?>! Bạn đang sử dụng gói ngày của HOCDE.
            <?php }else{?>
               Chưa nhận diện được thuê bao, Bạn đăng nhập <a href ='<?php echo Yii::app()->baseUrl . '/account/login' ?>'>Tại đây</a>
            <?php }?>
            <!--<div class="wellcome">-->
                <!--Xin chào thuê bao 0911321055! Vui lòng <a href="#" class="color-red">Đăng ký</a> để nhận Xem video bài giảng miễn phí mỗi ngày-->
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="main_menu fix">
            <?php
                $this->widget('zii.widgets.CMenu', array(
                    'encodeLabel' => FALSE,
                    'htmlOptions' => array(
                        'class' => 'nav navbar-nav fix',
                    ),
                    'submenuHtmlOptions' => array(
                        'class' => 'child_menu off',
//                        'style' => 'display:none',
                    ),
                    'items' => array(
                        array(
                            'label' => 'Trang chủ',
                            'url'   => array('/'),
                        ),
                        array(
                            'label' => 'Clip bài giảng',
                            'url'   => array('/videos/list'),
                        ),
                        array(
                            'label' => 'Thư viện',
                            'url'   => array('/questionBank'),
                        ),
                        array(
                            'label' => 'Câu hỏi',
                            'url'   => array('/question/list'),
                        ),
                        array(
                            'label' => 'Tin tức',
                            'url'   => array('/blog'),
                        ),
                        array(
                            'label' => 'Giới thiệu',
                            'url'   => array('/gioi-thieu'),
                        ),
                        array(
                            'label' => 'Hướng dẫn',
                            'url'   => array('/huong-dan'),
                        ),
                    ),
                ));
            ?>
        </div>
    </div>
</header>

<div class="banner-top">
    <a href="#"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/demo/slide.png" alt=""/></a>
</div>