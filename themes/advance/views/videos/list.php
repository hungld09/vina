<div class="box-ques box-ques1">
    <div class="item-top txt-center space">
        <select name="class" class="classOption">
            <option value="-1">Danh sách lớp</option>
            <?php
            foreach ($class as $class) {
                ?>
                <option value="<?php echo $class['id'] ?>"><?php echo $class['class_name'] ?></option>
            <?php } ?>
        </select>
        <select  name="subject" class="subjectOption"><option value="-1">Danh sách Môn</option></select><br/>
        <span style="color: #f00; display: none" class="subjectValidate">Bạn chưa chọn môn</span>
    </div>
    <div class="box-video">
        <div class="box-video-inner">
            <div class="video"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/clip.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img alt="" src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png"></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>                                   
            </div>
            <div class="calculator">
                <img alt="" src="<?php echo Yii::app()->theme->baseUrl ?>/images/can.png">
                Môn toán, Lớp 12
            </div>
        </div>
        <div class="box-video-inner">
            <div class="video"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/clip.png" alt="" /></div>
            <div class="thums">
                <div class="thums-img"><img alt="" src="<?php echo Yii::app()->theme->baseUrl ?>/images/thume.png"></div>
                <a href="#">Nrgô bảo châu</a>
                <div class="date">10phút</div>                                   
            </div>
            <div class="calculator">
                <img alt="" src="<?php echo Yii::app()->theme->baseUrl ?>/images/can.png">
                Môn toán, Lớp 12
            </div>
        </div>
    </div>

<script>
$('.classOption').change(function () {
        var class_id = $(this).val();
        if (class_id == -1) {
            $('.classValidate').show();
            $(this).css('border', '1px solid #f00');
            return false;
        } else {
            $(this).css('border', '1px solid #ccc');
            $('.classValidate').hide();
        }
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loadSubject' ?>",
            data: {'class_id': class_id},
            dataType: 'html',
            success: function (html) {
//                $('#subject').remove();
                $('.subjectOption').html(html);
            }
        });
    });
     $('.subjectOption').change(function () {
        var subject_id = $(this).val();
        var class_id = $('.classOption').val();
        if (subject_id == -1) {
            $('.subjectValidate').show();
            $(this).css('border', '1px solid #f00');
            return false;
        } else {
            $(this).css('border', '1px solid #ccc');
            $('.subjectValidate').hide();
        }
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loadChapter' ?>",
            data: {'subject_id': subject_id, 'class_id': class_id},
            dataType: 'html',
            success: function (html) {
//                $('#subject').remove();
                $('.chapterOption').html(html);
            }
        });
    });
</script>