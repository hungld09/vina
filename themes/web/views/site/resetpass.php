<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<div class="row">
<div class="resetpass" style="text-align: center">
    <form action='#' method="post">
        <input type="hidden" value="<?php echo $subId?>" name='subsId' class="subsId" />
        <label>Nhập mật khẩu mới: </label>
        <input type="password" value="" name='pass1' class="pass1"/><br/>
        <label>Nhập lại mật khẩu mới: </label>
        <input type="password" value="" name='pass2' class="pass2"/><br/>
        <button type="submit" class="confirmMail">Xác nhận</button>
    </form>
</div>
</div>
<script>
    $('.confirmMail').click(function(){
       var  subsId = $('.subsId').val();
       var  pass1 = $('.pass1').val();
       var  pass2 = $('.pass2').val();
       if(pass1.length < 4){
           alert('Password từ 4 ký tự trở lên');
           return false;
       }
       if(pass1 != pass2){
           alert('Password không trùng nhau');
           return false;
       }
       $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/site/confirmPass'?>",
            data: {'subsId':subsId,'pass1':pass1, 'pass2':pass2},
            dataType:'html',
            success: function(html){
                if(html == 1){
                    alert('Lỗi hệ thống, xin vui lòng admin'); return false;
                }else if(html == 0){
                    alert('Reset pass thành công. Bạn hay truy cập http://hocde.onedu.vn để đăng nhập'); 
                    return false;
                }else{
                    alert(html); return false;
                }
            }
        });
    });
</script>
