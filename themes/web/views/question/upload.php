<?php
    //    print_r($this->userName->url_avatar);die;
    if ($this->msisdn != '') {
        $SubID = TRUE;
    } else {
        $SubID = FALSE;
    }
    if (!$SubID) {
        $this->redirect(Yii::app()->homeurl);
    }
?>
<div class="question-list">
    <div class="col-md-12">
        <h1>Gửi câu hỏi</h1>

        <form enctype="multipart/form-data" id="formUpload" action="" method="post">
            <div class="question">
                <div class="question-title">
                    <div class="question-title-1">
                        <!--<label><img width="50px" src="<?php // echo $avata ?>" /></label>-->
                        <input type="text" name="title" id="title" placeholder="Tiêu đề ..."/>
                    </div>
                    <div class="question-title-2">
                        <div class="row col-xs-12 col-md-12 col-lg-12">
                            <div class="col-lg-6 col-md-6 col-xs-6">
                                <div class="question-title-class">
                                    <select name="class1" id="class1">
                                        <option value="-1">Chọn lớp</option>
                                        <?php foreach ($class as $class) { ?>
                                            <option value="<?php echo $class['id'] ?>"><?php echo $class['class_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xs-6">
                                <div class="question-title-subject">
                                    <select name="subject" id="subject">
                                        <option value="-1">Chưa chọn lớp</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="question-images">
                    <!--<img src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/ava_news.jpg" />-->
                    <div class="ui-block-a img-upload1" id="img-upload1">
                        <div id="filediv">
                            <input name="file[]" type="file" id="file" class="custom-file-input" style="position: absolute;  left: 50%; margin-left: -125px"/>
                        </div>
                    </div>
                </div>
                <div class="" style="width: 100%; text-align: center;">
                    <a class="loadgif" style="display: none"><img width="50" src="<?php echo Yii::app()->theme->baseUrl . '/images/load.gif' ?>"/></a>
                </div>
                <div class="question-submit question-submit-question">
                    <button type="submit" class="btn btn-primary">Gửi câu hỏi</button>
                    <!--<input type="button" id="add_more" class="upload" value="Add More Files">-->
                </div>
                <div class="note-upload" style="color: #f00;line-height: 20px;">
                    <p style="font-weight: bold">Chú ý:</p>
                    <p>- Mỗi ảnh chỉ được hỏi cho 1 câu độc lập</p>
                    <p>- Với câu hỏi tập làm văn thì HỌC DỄ chỉ cung cấp dàn ý chi tiết.</p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var abc = 0; //Declaring and defining global increement variable
    $(document).ready(function () {
//To add new input file field dynamically, on click of "Add More Files" button below function will be executed
        $('#add_more').click(function () {
            $(".upload").before($("<div/>", {id: 'filediv', class: 'ui-block-a img-upload1'}).fadeIn('slow').append(
                $("<input/>", {name: 'file[]', type: 'file', id: 'file'})
            ));
        });
//following function will executes on change event of file input to select different file	
        $('body').on('change', '#file', function () {
            if (this.files && this.files[0]) {
                abc += 1; //increementing global variable by 1
                var z = abc - 1;
                var x = $(this).parent().find('#previewimg' + z).remove();
                $(this).before("<div id='abcd" + abc + "' class='abcd'><img id='previewimg" + abc + "' src=''/></div>");
                $('.custom-file-input').css('top', '10%');
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
                $('#file').show();
//			    $(this).hide();
                $("#abcd" + abc).append($("<img/>", {
                    id: 'img',
                    class: 'delete',
                    src: '<?php echo Yii::app()->theme->baseUrl?>/img/x.png',
                    alt: 'delete'
                }).click(function () {
//                        $('#file').show();            
//                        alert($(this));return false;
                    $(this).parent().remove();

                }));
            }
        });

//To preview image     
        function imageIsLoaded(e) {
            $('#previewimg' + abc).attr('src', e.target.result);
        };

        $('#upload').click(function (e) {
            var name = $(":file").val();
            if (!name) {
                alert("First Image Must Be Selected");
                e.preventDefault();
            }
        });
    });
    $("#formUpload").on('submit', (function (e) {
        var title = $('#title').val();
        var subject = $('#subject').val();
        var class1 = $('#class1').val();
        var file = $('#file').val();
        if (file == null || file == '') {
            alert('Bạn chưa chọn ảnh');
            return false;
        }
        if (title == null || title == '') {
            alert('Bạn chưa điền tiêu đề');
            return false;
        }
        if (class1 == null || class1 == '-1') {
            alert('Bạn chưa nhập lớp');
            return false;
        }
        if (subject == null || subject == '' || subject == '-1') {
            alert('Bạn chưa nhập môn học');
            return false;
        }
        e.preventDefault();
        showLoad();
        $('.question-submit-question').hide();
//        $('.loadgif').css('display','block');
        $.ajax({
            url: "<?php echo Yii::app()->request->baseUrl . '/question/saveUpload'?>",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                hideLoad();
                if (data == 1) {
                    alert('Bạn không đủ tiền, vui lòng nạp thêm tiền để đăng câu hỏi.');
                    window.location.replace("<?php echo Yii::app()->homeurl . '/account/useCard'?>");
                    return false;
                } else if (data == 2) {
                    alert('Ảnh upload câu hỏi của bạn bị lỗi, Bạn vui lòng upload lại ảnh.');
                    window.location.replace("<?php echo Yii::app()->homeurl . '/question/upload'?>");
                    return false;
                } else if (data == 3) {
                    alert('Ảnh upload của bạn không được quá 2M.');
                    window.location.replace("<?php echo Yii::app()->homeurl . '/question/upload'?>");
                    return false;
                } else {
                    window.location.replace("<?php echo Yii::app()->homeurl . '/question/list'?>");
                }
            },
            error: function () {
            }
        });
    }));
    $('#class1').change(function () {
        var class_id = $('#class1').val();
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->request->baseUrl . '/question/loadSubject'?>",
            data: {'class_id': class_id},
            dataType: 'html',
            success: function (html) {
//                $('#subject').remove();
                $('#subject').html(html);
            }
        });
    });
    function showLoad() {
        $('.loadgif').show();
    }
    function hideLoad() {
        $('.loadgif').hide();
    }
</script>