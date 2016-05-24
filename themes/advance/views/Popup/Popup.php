<div class="message">
    <?php if($msisdn != '' && $usingServices == null){?>
        Xin chào thuê bao <?php echo $msisdn?>! Vui lòng <a href="#popup-form" class="show-popup">Đăng ký</a> để nhận<br />
        Xem video bài giảng miễn phí mỗi ngày
    <?php }else if($msisdn != '' && $usingServices != null){ ?>
       Xin chào thuê bao <?php echo $msisdn?>! Bạn đang sử dụng gói ngày của HOCDE.
    <?php }else{?>
       Chưa nhận diện được thuê bao, Bạn đăng nhập <a href ='<?php echo Yii::app()->baseUrl . '/account/login' ?>'>Tại đây</a>
    <?php }?>
</div>
<div class="banner">
    <?php if($msisdn != '' && $usingServices == null){?>
        <a href="#popup-form" class="show-popup"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/banner.png" alt="" /></a>
    <?php }else if($msisdn != '' && $usingServices != null){ ?>
       Xin chào thuê bao <?php echo $msisdn?>! Bạn đang sử dụng gói ngày của HOCDE.
    <?php }else{?>
       <a href ='<?php echo Yii::app()->baseUrl . '/account/login' ?>'><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/banner.png" alt="" /></a>
    <?php }?>
    <form id="popup-form" class="mfp-hide white-popup-block">
        <div class="box-register">
            <div class="title-res">
                Đăng ký gói dịch vụ
            </div>
            <div class="frm-register">
                <p class="txt-g">Quý khách đã chọn đăng ký gói cước:</p>
                <div class="date-txt">Gói Học Dễ theo ngày</div>
                <ul class="list">
                    <li>
                        <label>Dịch vụ:</label>
                        <input type="text" value="Học Dễ"  />
                    </li>
                    <li>
                        <label>Giá gói:</label>
                        <input type="text" value="2000đ/ngày"  />
                    </li>
                </ul>
                <p class="red txt-center">Miễn phí một ngày cước TB cho khách hàng
                    đăng ký lần đầu!</p>
                <p class="txt-center"><button class="button" type="button"><span><span>Đồng ý</span></span></button></p>
            </div>
        </div>
        <!-- end dangky -->
    </form>
</div>