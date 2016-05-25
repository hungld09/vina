<div class="box-search question-list">
    <div class="col-md-12">
        <h1>Thư viện lời giải</h1>
        <div class="content">
            <div class="form-search">
                <form enctype="multipart/form-data" id="formSearch" action="" method="get">
                    <div class="input-box">
                        <input type="text" placeholder="Từ khóa tìm kiếm..." name="namequestion" id="namequestion"
                               value="<?php echo(isset($title) ? $title : '') ?>"/>
                    </div>
                    <div class="input-box">
                        <select name="class" class="classOption">
                            <option value="0">Danh sách lớp</option>
                            <?php
                                foreach ($class as $class) {
                                    $selected = '';
                                    if (isset($class_id) && $class_id == $class['id'])
                                        $selected = ' selected=""';
                                    ?>
                                    <option value="<?php echo $class['id'] ?>"<?php echo $selected ?>><?php echo $class['class_name'] ?></option>
                                <?php } ?>
                        </select><br/>
                        <span style="color: #f00; display: none" class="classValidate">Bạn chưa chọn lớp</span>
                    </div>
                    <div class="input-box">
                        <select name="subject" class="subjectOption">
                            <option value="0">Danh sách Môn</option>
                        </select><br/>
                        <span style="color: #f00; display: none" class="subjectValidate">Bạn chưa chọn môn</span>
                    </div>
                    <div class="input-box">
                        <select name="chapter" class="chapterOption">
                            <option value="0">Danh sách Chương</option>
                        </select><br/>
                        <span style="color: #f00; display: none" class="chapterValidate">Bạn chưa chọn chương</span>
                    </div>
                    <div class="input-box">
                        <select name="unit" class="unitOption">
                            <option value="0">Danh sách bài</option>
                        </select><br/>
                    </div>
                    <button type="submit" class="search-question-bank" name="submit"><span><span>Tìm kiếm</span></span></button>
                </form>
                <div class="clearfix"></div>

                <?php
                    if (isset($result) && count($result) > 0):
                        ?>
                        <?php
                        $this->widget(
                            'zii.widgets.CListView',
                            array(
                                'dataProvider'     => $result,
                                'template'         => "<div class=\"box-news box-result\">{items}</div>{pager}",
                                'enablePagination' => TRUE,
                                'itemView'         => 'questionBank/_block_item',
                                'ajaxUpdate'       => FALSE,
                                'ajaxType'         => 'GET',
                                'itemsTagName'     => 'ul',
                                'itemsCssClass'    => '',
                                'emptyText'        => 'Không có dữ liệu',
                                'pager'            => array(
//                                    'class'          => ' ',  // use if you want to extend CLinkPager
                                    'cssFile'        => FALSE,  // to redirect from using the css file in the framework.
                                    // Make sure you load your defined css file as you would with any other
                                    'header'         => '',
                                    'firstPageLabel' => '&laquo;',
                                    'prevPageLabel'  => '&lt;',
                                    'nextPageLabel'  => '&gt;',
                                    'lastPageLabel'  => '&raquo;',
                                ),
                            )
                        );
                        ?>
                        <script src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML" type="text/javascript">
                            $(document).ready(function () {
                                MathJax.Hub.Config({
                                    tex2jax: {
                                        inlineMath: [
                                            ['$', '$'],
                                            ['\\(', '\\)']
                                        ]
                                    }
                                });
                            });
                        </script>
                        <?php
                    endif;
                ?>

            </div>
        </div>
    </div>
</div>

<script>
    $('.chapterOption').change(function () {
        var subject_id = $('.subjectOption').val();
        var class_id = $('.classOption').val();
        var chapter_id = $(this).val();
        if (chapter_id == 0) {
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
                $('.unitOption').html(html);
            }
        });
    });

    $('.subjectOption').change(function () {
        var subject_id = $(this).val();
        var class_id = $('.classOption').val();
        if (subject_id == 0) {
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
                $('.chapterOption').html(html);
            }
        });
    });

    $('.classOption').change(function () {
        var class_id = $(this).val();
        if (class_id == 0) {
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
        if (class_id == 0 || class_id == '0') {
            $('.classValidate').show();
            $('.classOption').css('border', '1px solid #f00');
            return false;
        }
        if (subject_id == 0 || subject_id == '0') {
            $('.subjectValidate').show();
            $('.classOption').css('border', '1px solid #ccc');
            $('.classValidate').hide();
            $('.subjectOption').css('border', '1px solid #f00');
            return false;
        }
//        if(chapter_id == 0 || chapter_id=='0'){
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

    $(document).ready(function () {
        var class_id = <?php echo (isset($class_id)) ? $class_id : 0 ?>;
        var subject_id = <?php echo (isset($subject_id)) ? $subject_id : 0 ?>;
        var chapter_id = <?php echo (isset($chapter_id)) ? $chapter_id : 0 ?>;
        var unit_id = <?php echo (isset($unit_id)) ? $unit_id : 0 ?>;

        if (class_id > 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loadSubject' ?>",
                data: {'subject_id': subject_id, 'class_id': class_id},
                dataType: 'html',
                success: function (html) {
                    $('.subjectOption').html(html);

                    if (subject_id > 0) {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loadChapter' ?>",
                            data: {'subject_id': subject_id, 'class_id': class_id, 'chapter_id': chapter_id},
                            dataType: 'html',
                            success: function (html) {
                                $('.chapterOption').html(html);

                                if (chapter_id > 0) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loadUnit' ?>",
                                        data: {'subject_id': subject_id, 'class_id': class_id, 'chapter_id': chapter_id, 'unit_id': unit_id},
                                        dataType: 'html',
                                        success: function (html) {
                                            $('.unitOption').html(html);
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }
    });
</script>