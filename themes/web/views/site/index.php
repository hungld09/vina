<?php
$this->title = 'Hướng dẫn giải bài tập toán, lý, hóa online | Học Dễ OnEdu';
$this->description = 'Học dễ Onedu, Hoc de Onedu là ứng dụng hỗ trợ hướng dẫn giải bài tập toán, giải bài tập vật lý, giải bài tập hóa học online nhanh, hiệu quả nhất';
$this->keywords = 'học dễ, hoc de, học dễ onedu, hoc de onedu, giải bài tập toán, giải bài tập vật lý, giải bài tập hóa học, hướng dẫn giải bài tập, đáp án bài tập, để học tốt';
?>
<!--slider-->
<style>
    .partner{
        float: left;
        width: 100%;
        text-align: center;
        margin: auto;
        padding: 0;
        display: flex;
    }
    .partner img{width: 80%}
</style>

<section id="slider">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div id="slider-carousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#slider-carousel" data-slide-to="0" class="active" style="text-indent: 0px;width: 32px;height: auto;font-weight: bold;color: #fff;">1</li>
                        <li data-target="#slider-carousel" data-slide-to="1" style="text-indent: 0px;width: 32px;height: auto;font-weight: bold;color: #fff;">2</li>
                        <li data-target="#slider-carousel" data-slide-to="2" style="text-indent: 0px;width: 32px;height: auto;font-weight: bold;color: #fff;">3</li>
                    </ol>

                    <div class="carousel-inner" style="height: 361px;">
                        <div class="item active">
                            <div class="col-sm-6">
                                <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/slide1.png"  class="pricing" alt="" />
                            </div>
                        </div>
                        <div class="item">
                            <div class="col-sm-6">
                                <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/slide2.png"  class="pricing" alt="" />
                            </div>
                        </div>

                        <div class="item">
                            <div class="col-sm-6">
                                <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/slide3.png" class="pricing" alt="" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<img style="width: 100%" src="<?php echo Yii::app()->theme->baseUrl ?>/img/danhgia2.png">
<div class="partner">
    <div class="" style=" float: left; width: 70%;  margin: auto;   padding: 45px 0px;">
        <div class="" style="">
            <div class="col-sm-12">
                <div class="col-sm-2">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-toan'; ?>">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/toan_ic.png" alt="" />
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-ly'; ?>">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/ly_ic.png" alt="" />
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-hoa'; ?>">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/hoa_ic.png" alt="" />
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-anh'; ?>">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/anh_ic.png" alt="" />
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-van'; ?>">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/van_ic.png" alt="" />
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-sinh'; ?>">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/img/sinh_ic.png" alt="" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>