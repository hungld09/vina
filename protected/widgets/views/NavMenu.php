	<div id="fnNav" class="sidebar none">
		<div class="slogin">
			<img class="logo" src="<?php echo Yii::app()->request->baseUrl ?>/images/mobiphim_nav_logo.png" alt="logo" width="102" />
			<!--<a href="/user/login" class="button login">Đăng nhập</a>-->
		</div>
		<ul class="navigator">
			<li><a href="<?php echo Yii::app()->request->baseUrl?>/" class="icon-home">&nbsp;Trang chủ</a></li>
			<li><?php echo CHtml::link("Tài khoản", array('/account'), array('class' => 'icon-phone'))?></li>
		</ul>
		<div class="glist">
			<h3 class="stitle">TOP PHIM</h3>
			<ul class="slist">
				<li><?php echo CHtml::link("Phim mới nhất", array('/video/browse/order/newest'));?></li>
				<li><?php echo CHtml::link("Xem nhiều nhất", array('/video/browse/order/most_viewed'));?></li>
			</ul>
		</div><!--End .glist-->
		<div class="glist">
			<h3 class="stitle">PHIM THEO THỂ LOẠI</h3>
			<ul class="slist">
<?php
			/* @var $cat VodCategory */
			foreach ($categories as $cat) { ?>
				<li><?php echo CHtml::link($cat->display_name, array("/video/browse/category/" . $cat->id)); ?></li>
<?php
			}
?>
			</ul>
		</div><!--End .glist-->
		<div class="glist">
			<h3 class="stitle">THÔNG TIN HỖ TRỢ</h3>
			<ul class="slist">
				<!--<li><a href="#">Ứng Dụng mobiphim</a></li>-->
				<li><a href="<?php echo Yii::app()->baseUrl?>/account/service">Giới thiệu dịch vụ</a></li>
				<li><a href="<?php echo Yii::app()->baseUrl?>/news">Tin tức</a></li>
				<!--<li><a href="#" class="fnFbShow">Góp Ý &amp; Báo Lỗi</a></li>-->
			</ul>
		</div><!--End .glist-->
		<div class="s-footer">
			<span>&copy; 2013 - VMS Vinaphone.</span>
		</div>
	</div><!--End #fnNav -->
