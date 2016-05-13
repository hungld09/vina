<div class="web_body">
    <form method="post" action="#">
        <div class="form-group">
            <label>Nhập đối tác</label>
        </div>
        <div class="form-group">
            <select class="form-control" name="type_account" id="type_account">
                <option value="1">Học sinh</option>
                <option value="2">Giáo viên</option>
            </select>
        </div>
        <div class="form-group" id = "notifi-email">
            <input type="text" class="form-control" name="email" id="email" placeholder="Email">
            <label style="color: #f00" class="tea">Chú ý: Bạn cần nhập chính xác và xác nhận địa chỉ e-mail để nhận được câu hỏi miễn phí.</label>
            <label style="color: #f00; display: none" class="student" >Chú ý: Bạn cần nhập chính xác và xác nhận địa chỉ e-mail để nhận được thông báo từ Học Dễ.</label>
          </div>
        <?php echo Yii::app()->user->getFlash('responseToUser'); ?>
        <button type="submit" class="btn btn-default submit-partner" name="submit">Đồng ý</button>
        <button type="reset" class="btn btn-default" name="reset">Hủy</button>
    </form>
</div>
<script>
    $('#type_account').change(function(){
        var type = $('#type_account').val();
        if(type == 2){
            $('.tea').hide();
            $('.student').show();
        }else if(type == 1){
            $('.student').hide();
           $('.tea').show();
        }
    });
    $('.submit-partner').click(function(){
        var type = $('#type_account').val();
        var email = $('#email').val();
        var subId = <?php echo Yii::app()->session['user_id']?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->homeurl .'/account/saveType' ?>",
            data: {'type':type,'subId':subId,'email':email},
            dataType:'html',
            success: function(html){
                window.location.replace("<?php echo Yii::app()->homeurl .'/site/'?>");
            }
        });
    });
</script>