<div class="title"><span>Ngân hàng câu hỏi</span></div>
<div class="list-quest">
    <ul>
        <?php
        $i = 5;
        foreach ($class as $class):
            $i++;
            ?>
            <li><a href="<?php echo Yii::app()->baseUrl.'/questionBank' ?>"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/<?php echo $i . '.png' ?>" alt="" /><?php echo $class['class_name'] ?></a></li>
            <?php
        endforeach;
        ?>
    </ul>
</div>
<div class="title">
    <span>video bài giảng</span>
</div>
<div class="box-video">
    <ul class="list-video">
        <li>
            <div class="img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video1.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png" alt="" /></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>                                   
            </div>
            <div class="calculator">
                <img src="<?php echo Yii::app()->theme->baseUrl ?>/images/can.png" alt="" />
                Môn toán, Lớp 12
            </div>
        </li>
        <li>
            <div class="img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video1.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png" alt="" /></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>
            </div>
            <div class="calculator">
                <img src="<?php echo Yii::app()->theme->baseUrl ?>/images/can.png" alt="" />
                Môn toán, Lớp 12
            </div>
        </li>
        <li>
            <div class="img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video1.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png" alt="" /></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>
            </div>
            <div class="calculator">
                <img src="<?php echo Yii::app()->theme->baseUrl ?>images/can.png" alt="" />
                Môn toán, Lớp 12
            </div>
        </li>
        <li>
            <div class="img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video1.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png" alt="" /></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>
            </div>
            <div class="calculator">
                <img src="<?php echo Yii::app()->theme->baseUrl ?>/images/can.png" alt="" />
                Môn toán, Lớp 12
            </div>
        </li>
        <li>
            <div class="img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video1.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png" alt="" /></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>
            </div>
            <div class="calculator">
                <img src="<?php echo Yii::app()->theme->baseUrl ?>images/can.png" alt="" />
                Môn toán, Lớp 12
            </div>
        </li>
        <li>
            <div class="img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video1.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png" alt="" /></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>
            </div>
            <div class="calculator">
                <img src="<?php echo Yii::app()->theme->baseUrl ?>/images/can.png" alt="" />
                Môn toán, Lớp 12
            </div>
        </li>
    </ul>
</div>