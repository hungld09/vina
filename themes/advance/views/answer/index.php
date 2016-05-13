<form enctype="multipart/form-data" id="formUpload" action="" method="post" data-ajax="false">
    <div class="question">
        <input type="hidden" name ="question_id" id ="question_id" value="<?php echo $question_id?>"/>
        <div class="question-title">
            <div class="question-title-1">
                <label><img src="<?php echo Yii::app()->theme->baseUrl .'/FileManager/avata.png'?>" /></label>
                <input type="text" name="title" id="title" readonly value="Đây là bài giải của em" />
            </div>
        </div>
        <div class="" style="padding-bottom: 10px">
            <a class="btn btn-primary insertImg" dem="0" style="display: none">Thêm ảnh</a>
        </div>
        <div class="question-images">
            <!--<img src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/ava_news.jpg" />-->
            <div class="ui-block-a img-upload1" id="img-upload1">
                <div id="filediv">
                    <input name="file[]" type="file" id="file" class="custom-file-input"style="position: absolute;  left: 40%;width: 70px;"/>
                </div>
            </div>
        </div>
        <div class="" style="width: 100%; text-align: center;">
             <a class="loadgif" style="display: none"><img width="50px" src="<?php echo Yii::app()->theme->baseUrl .'/img/load.gif'?>" /></a>
        </div>
        <div class="question-submit question-submit-answer">
            <button type="submit" class="btn btn-primary">Gửi</button>
            <!--<input type="button" id="add_more" class="upload" value="Add More Files">-->
        </div>
    </div>
</form>
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
                $('.custom-file-input').css('top','10%');
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
            $('.insertImg').css('display', 'block');
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
    $("#formUpload").on('submit',(function(e){
        var title = $('#title').val();
        var question_id= $('#question_id').val();
        var file= $('#file').val();
        var uid= <?php echo Yii::app()->session['user_id'] ?>;
        if(file == null|| file ==''){
            alert('Bạn chưa chọn ảnh'); return false;
        }
        if(title == null || title==''){
            alert('Bạn chưa điền tiêu đề'); return false;
        }
        if(question_id == null || question_id==''){
            alert('Bạn chưa nhập lớp'); return false;
        }
        e.preventDefault();
        showLoad();
        $('.question-submit-answer').hide();
//        $('.loadgif').css('display','block');
        $.ajax({
            url: "<?php echo Yii::app()->request->baseUrl . '/answer/saveUpload'?>",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data){
                hideLoad();
                if(data == 2){
                   alert('Ảnh upload câu trả lời của bạn bị lỗi, Bạn vui lòng upload lại ảnh.'); 
                   window.location.replace("<?php echo Yii::app()->homeurl . '/question/'.$question_id?>"); return false;
                }
                window.location.replace("<?php echo Yii::app()->homeurl . '/question/'.$question_id?>");
            },
            error: function(){}
        });
    }));
    function showLoad(){
        $('.loadgif').show();
    }
    function hideLoad(){
        $('.loadgif').hide();
    }
    $('.insertImg').click(function(){
       var i = parseInt($(this).attr('dem'));
       if(i >= 2){
           alert('Bạn không được thêm quá 3 ảnh.');return false;
       }
       $(this).attr('dem',i+1);
       $(this).css('display', 'none');
       $('#filediv').before("<div id='filediv'><input name='file[]'' type='file' id='file' class='custom-file-input' style='position: absolute;  left: 40%;width: 70px;'/></div>");
    });
</script>