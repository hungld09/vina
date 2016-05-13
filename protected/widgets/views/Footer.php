<?php if ($showBanner):?>
<div><a href="http://mplus.vn" target="blank"><img src="<?php echo Yii::app()->baseUrl?>/images/mplus_banner.gif" alt="mPlus" style="width:100%"/></a></div>
<?php endif;?>
<div class="divider"><h3 class="dtitle">TRUY CẬP NHANH</h3></div>
<div class="menu_div">
	<ul>
        <li><a href="<?php echo Yii::app()->request->baseUrl?>/" class="icon-home">&nbsp;Trang chủ</a></li>
<?php if (isset($dontGoBack) && $dontGoBack != true) { ?>
		<li><?php echo CHtml::link("&nbsp;Trang trước", 'javascript:history.go(-1)', array('class' => 'icon-undo'));?></li>
<?php } ?>
		<li><a href="<?php echo Yii::app()->baseUrl?>/account/service" class="icon-newspaper">&nbsp;Giới thiệu dịch vụ</a></li>
        <li><?php echo CHtml::link("&nbsp;Tài khoản", array('/account'), array('class' => 'icon-phone'));?></li>
    </ul>
</div>
<div class="divider"><h3 class="dtitle">DANH SÁCH PHIM</h3></div>
<div class="menu_div">
	<ul>
<?php
	/* @var $cat VodCategory */
	foreach ($categories as $cat) { ?>
		<li><?php echo CHtml::link($cat->display_name, array("/video/browse/category/" . $cat->id)); ?></li>
<?php
	}
?>
    </ul>
</div>
<div class="divider"><h3 class="dtitle">LIÊN KẾT KHÁC</h3></div>
<div class="menu_div">
	<ul>
		<li><a href="http://wap.Vinaphone.com.vn" target="blank">Vinaphone Portal</a></li>
		<li><a href="http://mgame.vn" target="blank">Game di động</a></li>
		<li><a href="http://m.funring.vn" target="blank">Nhạc chờ</a></li>
		<li><a href="http://zoota.vn" target="blank">Mạng xã hội Zoota</a></li>
	</ul>
</div>
<div class="divider" style="text-align:center;background: url('<?php echo Yii::app()->theme->baseUrl?>/images/bg.png') repeat-x left bottom #DADADA;padding-bottom:15px;">
<small>&copy; 2013 <img src="<?php echo Yii::app()->baseUrl?>/images/Vinaphone_small.png" alt="VMS Vinaphone">. Powered by Nam Viet Corporation</small>
</div>

