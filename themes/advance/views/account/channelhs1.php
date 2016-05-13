<html class="ui-mobile">
<head id="Head1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8" /> 
	<meta http-equiv="encoding" content="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta content="width=device-width, initial-scale=1" name="viewport" id="viewport" />
	<!-- Bootstrap -->
    <link href="<?php echo Yii::app()->theme->baseUrl ?>/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="<?php echo Yii::app()->theme->baseUrl ?>/css/bootstrap-select.css" rel="stylesheet"/>
	<link href="<?php echo Yii::app()->theme->baseUrl ?>/css/reset.css" rel="stylesheet"/>
	<link href="<?php echo Yii::app()->theme->baseUrl ?>/Plugin/Fancybox/fancybox.css" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl ?>/css/style.css"/>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
	<!--[if gte IE 9]>
	<style type="text/css">
		.gradient {
		   filter: none;
		}
	  </style>
	<![endif]-->
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/bootstrap.min.js"></script>	
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/bootstrap-select.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/Plugin/Fancybox/fancybox.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/jquery.touchSwipe.min.js"></script>	
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/site.js"></script>
        <style>
            .icon-reg img{width: 50%}
        </style>
</head>
<body>
    <div id="tpl-contaiter">
        <div class="sign-main">
            <div class="sign-form">
               <div class="sign-logo">
                    <a href="#" style="font-family: font-dep"  data-toggle="modal" data-target="#myModal7" ><img src="<?php echo Yii::app()->theme->baseUrl ?>/img/logo2.png" /></a>
                </div>
                <div class="sign-logo">
                    <a href="<?php echo $this->createUrl("/");?>"><img src="<?php echo Yii::app()->theme->baseUrl ?>/img/logo1.png" /></a>
                </div>
<!--                <div class="row icon-reg">
                    <div class="col-lg-4 col-xs-4 col-md-4">
                        <a href="#"><img src="<?php // echo Yii::app()->theme->baseUrl .'/img/dangnhap2.png'?>" /></a>
                    </div>
                    <div class="col-lg-4 col-xs-4 col-md-4">
                        <a href="https://facebook.com/dialog/oauth?client_id=1054223384618565&redirect_uri=http://hocde.onedu.vn/account/loginface"> <img src="<?php echo Yii::app()->theme->baseUrl .'/img/fb2.png'?>" /></a>
                    </div>
                    <div class="col-lg-4 col-xs-4 col-md-4">
                        <a href="https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=http%3A%2F%2Fhocde.onedu.vn%2Faccount%2FloginGoogle&client_id=416965466359-5ic1landcm6r2tonkchmnrkiu0ukfqlh.apps.googleusercontent.com&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fplus.login+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fplus.me&access_type=offline&approval_prompt=auto"> <img src="<?php echo Yii::app()->theme->baseUrl .'/img/gg2.png'?>" /></a>
                    </div>
                </div>-->
                <div class="sign-input">
                    <form action="<?php echo $this->createUrl("/account/login"); ?>" method="post">
                        <div class="form-group">
                          <input type="text" class="form-control" name="username" id="exampleInputEmail1" placeholder="Tài khoản">
                        </div>
                        <div class="form-group">
                          <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Mật khẩu">
                        </div>
                        <?php echo Yii::app()->user->getFlash('responseToUser'); ?><br/>
                        <button type="submit" class="btn btn-default sign-submit" name="submit">Đăng nhập</button>
                        <div class="row" style="margin-top: 10px">
                            <a type="submit" href="https://facebook.com/dialog/oauth?client_id=1054223384618565&redirect_uri=http://hocde.onedu.vn/account/channel1loginface?par=<?php echo $par; ?>" class="sign-submit"><img width="60%" src="<?php echo Yii::app()->theme->baseUrl ?>/img/dangnhapfb.png" /> </a>
                        </div>
                       <!--<a href="https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=http%3A%2F%2Fhocde.onedu.vn%2Faccount%2FloginGoogle&client_id=416965466359-5ic1landcm6r2tonkchmnrkiu0ukfqlh.apps.googleusercontent.com&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fplus.login+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fplus.me&access_type=offline&approval_prompt=auto" class="btn btn-default sign-submit">Google Plus</a>-->
                    </form>
<!--                    <form action="#" method="post">
                        <div class="row" style="margin-top: 10px">
                            <a type="submit" href="https://facebook.com/dialog/oauth?client_id=1054223384618565&redirect_uri=http://hocde.onedu.vn/account/channel1loginface?par=<?php echo $par; ?>" class="sign-submit"><img width="60%" src="<?php echo Yii::app()->theme->baseUrl ?>/img/dangnhapfb.png" /> </a>
                        </div>
                    </form>-->
                    <?php //// echo Yii::app()->session['user_id']; ?>
                </div>
                <div class="sign-more">
                    <a href="#" style="font-family: font-dep1">Bạn chưa có tài khoản?</a><br/>
                    <a href="#"style="font-family: font-dep"  data-toggle="modal" data-target="#myModal7">Đăng ký</a><br/>
                    <a href="#"style="font-family: font-dep"  data-toggle="modal" data-target="#myModal1">Quên mật khẩu</a> 
                </div>
<!--                <div class="sign-more">
                    <a href="#"style="font-family: font-dep"  data-toggle="modal" data-target="#myModal7">Đăng ký</a><br/>
                </div>-->
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal7" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
              <!--<form action="<?php // echo $this->createUrl("/account/register"); ?>" method="post">-->
              <form action="#" method="post">
                 <div class="row">
                      <div class="form-group col-xs-6">
                        <input type="text" class="form-control" name="firtname1" id="firtname1" placeholder="Họ">
                      </div>
                      <div class="form-group col-xs-6">
                        <input type="text" class="form-control" name="lastname1" id="lastname1" placeholder="Tên">
                      </div>
                 </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="username2" id="username2" placeholder="Tên đăng nhập">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="password1" id="password1" placeholder="Mật khẩu">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="password_confirm1" id="password_confirm1" placeholder="Nhập lại mật khẩu">
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="mobile1" id="mobile1" placeholder="Số điện thoại">
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="email1" id="email1" placeholder="Email">
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="partnerids1" id="partnerids1" placeholder="PartnerID">
                </div>
                <div class="form-group">
                  <select class="form-control" name="type_account1" id="type_account1">
                      <option value="1">Học sinh</option>
                      <option value="2">Giáo viên</option>
                  </select>
                </div>
                 <div class="modal-footer">
                     <a class="loadgif" style="display: none"><img width="60px" src="<?php echo Yii::app()->theme->baseUrl .'/img/load.gif'?>" /></a>
                    <button type="button" class="btn btn-primary register_sub_net2e">Đăng ký</button>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
</body>
<script>
    $(".register_sub_net2e").click(function(){
        var firtname =  $('#firtname1').val();
        var lastname =  $('#lastname1').val();
        var username =  $('#username2').val();
        var password =  $('#password1').val();
        var type =  $('#type_account1').val();
        var email =  $('#email1').val();
        var partnerid =  $('#partnerids1').val();
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        var password_confirm =  $('#password_confirm1').val();
        var mobile =  $('#mobile1').val();
        if(firtname == '' || firtname.length < 1 || firtname.trim() < 1){
            $("#firtname1").css('border','1px solid #f00'); return;
        }else{
            $("#firtname1").css('border','1px solid #ccc');
        }
        if(lastname == '' || lastname.length < 1 || lastname.trim() < 1){
            $("#lastname1").css('border','1px solid #f00');return;
        }else{
            $("#lastname1").css('border','1px solid #ccc');
        }
        if(username == '' || username.length < 4 || username.trim() < 4){
            $("#username2").css('border','1px solid #f00');return;
        }else{
            $("#username2").css('border','1px solid #ccc');
        }
        if(password == '' || password.length < 6 || password.trim() < 6){
            $("#password1").css('border','1px solid #f00');return;
        }else{
            $("#password1").css('border','1px solid #ccc');
        }
        if(password_confirm == '' || password_confirm.length < 6 || password_confirm.trim() < 6){
            $("#password_confirm1").css('border','1px solid #f00');return;
        }else{
            $("#password_confirm1").css('border','1px solid #ccc');
        }
        if(password_confirm != password){
            alert('Mat khau khong trung nhau'); return;
        }
        if(!re.test(email)){
            alert('Không đúng định dạng Email');return false;
        }
        showLoad();
        $.ajax({
            type: 'POST',
            url: "<?php echo $this->createUrl("/account/register") ?>",
            data: {'firtname':firtname,'lastname':lastname, 'username':username, 'password':password, 'password_confirm':password_confirm, 'mobile':mobile, 'email':email, 'partnerid':partnerid, 'type':type},
            dataType:'html',
            success: function(html){
                hideLoad();
                if(html == 1 || html == '1'){
                     alert('Username đã tồn tại'); return false;
                }
                alert(html);
                window.location.replace("<?php echo Yii::app()->homeurl?>");
//                window.location.href = "<?php // echo Yii::app()->homeurl?>";
            }
        });
    });
    function showLoad(){
        $('.loadgif').show();
    }
    function hideLoad(){
        $('.loadgif').hide();
    }
</script>
</html>
