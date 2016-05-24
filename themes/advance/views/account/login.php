<div class="breadcrumbs">
    <ul>
        <li><a href="#">Menu</a></li>
        <li><span>&frasl;</span></li>
        <li><span>Tìm kiếm thư viện</span></li>
    </ul>
</div>
<div class="top-text">
    <p class="txt-bule">Vui lòng đăng nhập để sử dụng dịch vụ và tham gia
        HỌC DỄ</p>
    Hệ thống sẽ tự động gửi mật khẩu tới số điện thoại của bạn.<br />
    Vui lòng kiểm tra và nhập lại mật khẩu
</div>
<div class="box-text">Nhập số điện thoại và mật khẩu</div>
<div class="box-search box-login">
    <form action="<?php echo Yii::app()->baseUrl . '/account/login' ?>" method="post">
        <div class="input-box">
            <input type="text" value="" placeholder="Số điện thoại (SMS)" />
        </div>
        <div class="input-box">
            <input type="password" value="" placeholder="123456"/>
        </div>
        <?php
        $errors = Yii::app()->user->getFlash('responseToUser');
        if (isset($errors)) {
        ?>
            <?php echo Yii::app()->user->getFlash('responseToUser'); ?><br/>
        <?php
        }
        ?>
        <p><button type="submit" name="submit"><span><span>Đăng nhập</span></span></button></p>
        <p><button type="button" class="btn1"><span><span>Nhận mật khẩu qua SMS</span></span></button></p>
        <div class="text-center note">
        <a href="https://vinaphone.com.vn/auth/login?service=http://vhocde.vn/account/checkticket">Đăng nhập với user VinaPortal</a>
     </div>
    </form>
</div><!-- end box-search -->
<div class="box-text">Bạn chưa đăng ký dịch vụ?</div>
<div class="txt-register">
    Để đăng ký soạn:
    <ul class="gc">
        <li>- Gói cước ngày: <span>HDD</span> gửi <span>9XX</span> ( 2000đ/ngày )</li>
        <li>- Gói cước tuần: <span>HDW gửi</span> <span>9XX </span>( 10.000đ/ngày )</li>
    </ul>
    <p class="red">Dịch vụ dành riêng cho thuê bao 3G Vinaphone</p>
</div>


<script>
    $('.getPassword').click(function () {
        var mobile = $('#mobile').val();
        if (!validateMobileNumber(mobile)) {
            alert('Số điện thoại không hợp lệ, vui lòng chọn thuê bao của VINAPHONE và thử lại');
            return false;
        }
        $.ajax({
            type: 'GET',
            url: "<?php echo Yii::app()->baseUrl . '/account/getpassword' ?>",
            data: {'mobile': mobile},
            dataType: 'html',
            success: function (html) {
                alert(html);
                window.location.reload();
            }
        });
    });
    function validateMobileNumber(mobile_number) {
        if (mobile_number.length == 0) {
            return false;
        } else {
            if (mobile_number.match(/^\d+/)) {
                if (mobile_number.match(/^(84|0|)(91|94|123|124|125|127|129)\d{7}$/)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
</script>