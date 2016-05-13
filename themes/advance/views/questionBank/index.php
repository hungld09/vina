<div class="breadcrumbs">
    <ul>
        <li><a href="#">Menu</a></li>
        <li><span>&frasl;</span></li>
        <li><span>Tìm kiếm thư viện</span></li>
    </ul>
</div>
<div class="box-search">
    <form  enctype="multipart/form-data" id="formSearch" action="<?php echo $this->createUrl("/questionBank/list"); ?>" method="get">
        <div class="input-box">
            <input type="text" placeholder="Từ khóa..."  name="namequestion" id="namequestion" />
        </div>
        <div class="input-box">
            <select name="class" class="classOption">
                <option value="-1">Danh sách lớp</option>
                 <?php
                    foreach ($class as $class) {
                        ?>
                        <option value="<?php echo $class['id'] ?>"><?php echo $class['class_name'] ?></option>
                    <?php } ?>
            </select><br/>
            <span style="color: #f00; display: none" class="classValidate">Bạn chưa chọn lớp</span>
        </div>
        <div class="input-box">
            <select  name="subject" class="subjectOption"><option value="-1">Danh sách Môn</option></select><br/>
            <span style="color: #f00; display: none" class="subjectValidate">Bạn chưa chọn môn</span>
        </div>
        <div class="input-box">
            <select name="chapter" class="chapterOption"><option value="-1">Danh sách Chương</option></select><br/>
            <span style="color: #f00; display: none" class="chapterValidate">Bạn chưa chọn chương</span>
        </div>
        <div class="input-box">
            <select name="unit" class="unitOption"><option value="-1">Danh sách bài</option></select><br/>
        </div>
        <button type="submit" class="search-question-bank" name="submit"><span><span >Tìm kiếm</span></span></button>
    </form>
</div><!-- end box-search -->

<script>
    $('.chapterOption').change(function () {
        var subject_id = $('.subjectOption').val();
        var class_id = $('.classOption').val();
        var chapter_id = $(this).val();
        if (chapter_id == -1) {
            $('.chapterValidate').show();
            $(this).css('border', '1px solid #f00');
            return false;
        } else {
            $(this).css('border', '1px solid #ccc');
            $('.chapterValidate').hide();
        }
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loadUnit' ?>",
            data: {'subject_id': subject_id, 'class_id': class_id, 'chapter_id': chapter_id},
            dataType: 'html',
            success: function (html) {
//                $('#subject').remove();
                $('.unitOption').html(html);
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
    $(".search-question-bank").click(function () {
        var class_id = $('.classOption').val();
        var subject_id = $('.subjectOption').val();
        var chapter_id = $('.chapterOption').val();
        var unit_id = $('.unitOption').val();
        var tag_id = $('.tagOption').val();
        var uid = <?php echo 1 ?>;
        if (class_id == -1 || class_id == '-1') {
            $('.classValidate').show();
            $('.classOption').css('border', '1px solid #f00');
            return false;
        }
        if (subject_id == -1 || subject_id == '-1') {
            $('.subjectValidate').show();
            $('.classOption').css('border', '1px solid #ccc');
            $('.classValidate').hide();
            $('.subjectOption').css('border', '1px solid #f00');
            return false;
        }
//        if(chapter_id == -1 || chapter_id=='-1'){
//            $('.chapterValidate').show();
//            $('.subjectOption').css('border','1px solid #ccc');
//            $('.subjectValidate').hide();
//            $('.chapterOption').css('border','1px solid #f00');return false;
//        }
//        $('.loadgif').css('display','block');
//        $.ajax({
//            type: "POST",
//            url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/list' ?>",
//            data:  {
//                'subject_id':subject_id, 'class_id':class_id, 'chapter_id':chapter_id, 'unit_id':unit_id, 'tag_id':tag_id
//            },
//            dataType:'html',
//            success: function(html){
////                alert(html);
//            }
//        });
    });
</script>