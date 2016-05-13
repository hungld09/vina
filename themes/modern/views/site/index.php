<?php
/** @var $this DefaultController */
$this->pageTitle = "mobiphim - Trang chủ";
$hasService = count($this->usingServices) > 0 ? true : false;
?>
<!-- include carousel.css -->
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/js/carousel/carousel.css">
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/js/carousel/carousel-style.css">
<!-- include carousel.js -->
<script src="<?php echo Yii::app()->request->baseUrl;?>/js/carousel/carousel.js"></script>

	<div class="home" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox", array('searchUrl' => "#"));?>
		<div class="container" id="fnBody">
			<!--<div class="filter"></div>-->
			<div class="content" id="fnBodyContent">
<?php $this->widget("application.widgets.Header", array('subscriber' => $this->subscriber, 'msisdn' => $this->msisdn, 'usingServices' => $this->usingServices)); ?>
				<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
				
				<div class="divider header-bg"><h3 class="dtitle icon-film"> PHIM MỚI NHẤT</h3></div>
<?php
$info = CUtils::getDeviceInfo();
if ($info['os'] == 'ios' || ($info['os'] == 'android' && $info['major'] > 2 && $info['minor'] >= 1)) {
?>
				<!-- the viewport -->
				<div>
				<div class="m-carousel m-fluid m-carousel-photos" style="height:200px;margin:10px auto;">
				  <!-- the slider -->
				  <div class="m-carousel-inner">
				    <!-- the items -->
<?php
	foreach ($newest as $i => $asset) {
		$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
					<a class="m-item" href="<?php echo $this->createUrl("/video/" . $asset['id']);?>">
						<img src="<?php echo CHtml::encode($posterUrl);?>" alt="" style="height:160px;" />
						<p class="m-caption" style="color:white"><?php echo CHtml::encode($asset['display_name']);?></p>
					</a>
<?php
	}
?>
				  </div>
				  <div class="m-carousel-controls m-carousel-hud">
				    <a class="m-carousel-prev" href="#" data-slide="prev">Previous</a>
				    <a class="m-carousel-next" href="#" data-slide="next">Next</a>
				  </div>
				  <!-- the controls -->
				  <div class="m-carousel-controls m-carousel-bulleted">
<?php for ($i = 1; $i <= count($newest); $i++) {
	$opts = array('data-slide' => $i);
	if ($i == 1) $opts['class'] = 'm-active';
	echo CHtml::link($i, "#", $opts);
}?>
				  </div>
				</div>
				<div><?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/order/newest'), array('class' => 'read-more'));?></div>
				</div>
<?php
} else {?>				
				<div class="tab-content videos fnRTContent" id="fnRTFeatured">
<?php
	foreach ($newest as $i => $asset) {
		$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
		$isFree = $hasService || $asset['is_free'] == 1;
		$this->widget("application.widgets.AssetItem", array('asset' => $asset, 'posterUrl' => $posterUrl, 'isFree' => $isFree));
	}
?>
				<div><?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/order/newest'), array('class' => 'read-more'));?></div>
				</div>
<?php
}?>
<div class="content-items"><a href="<?php echo Yii::app()->baseUrl?>/news"><span class="icon-newspaper"> </span>Vinaphone ch&iacute;nh thức cung cấp dịch vụ xem phim tr&ecirc;n di động mobiphim</a></div>
				
				<div class="divider header-bg"><h3 class="dtitle icon-film"> PHIM TUYỂN CHỌN</h3></div>
				<div class="tab-content videos fnRTContent" id="fnRTNewest">
<?php
foreach ($featured as $i => $asset) {
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
	$isFree = $hasService || $asset['is_free'] == 1;
	$this->widget("application.widgets.AssetItem", array('asset' => $asset, 'posterUrl' => $posterUrl, 'isFree' => $isFree));
}
?>
				<div><?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/category/15'), array('class' => 'read-more'));?></div>
				</div>
				
				<!--<div><a href="http://mplus.vn" target="blank"><img src="<?php echo Yii::app()->baseUrl?>/images/mplus_banner.gif" alt="mPlus" style="width:100%"/></a></div>-->
				
				<div class="divider header-bg"><h3 class="dtitle icon-film"> PHIM KINH ĐIỂN</h3></div>
				<div class="tab-content videos fnRTContent" id="fnRTNewest">
<?php
foreach ($elClasico as $i => $asset) {
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
	$isFree = $hasService || $asset['is_free'] == 1;
	$this->widget("application.widgets.AssetItem", array('asset' => $asset, 'posterUrl' => $posterUrl, 'isFree' => $isFree));
}
?>
				<div><?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/category/33'), array('class' => 'read-more'));?></div>
				</div>
			<div class="divider header-bg"><h3 class="dtitle icon-film"> PHIM MIỄN PHÍ</h3></div>
                                <div class="tab-content videos fnRTContent" id="fnRTNewest">
<?php
foreach ($freeAssets as $i => $asset) {
        $posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
        $isFree = $hasService || $asset['is_free'] == 1;
        $this->widget("application.widgets.AssetItem", array('asset' => $asset, 'posterUrl' => $posterUrl, 'isFree' => $isFree));
}
?>
                                <div><?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/category/27'), array('class' => 'read-more'));?></div>
                                </div>
	
				<div class="divider header-bg"><h3 class="dtitle icon-film"> XEM NHIỀU NHẤT</h3></div>
				<div class="tab-content videos fnRTContent" id="fnRTNewest">
<?php
foreach ($mostViewed as $i => $asset) {
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
	$isFree = $hasService || $asset['is_free'] == 1;
	$this->widget("application.modules.widgets.AssetItem", array('asset' => $asset, 'posterUrl' => $posterUrl, 'isFree' => $isFree));
}
?>
				<div><?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/order/most_viewed'), array('class' => 'read-more'));?></div>
				</div>
			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories, 'dontGoBack' => true, 'showBanner' => false)); ?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.m-carousel').carousel();
			});
		</script>
		<a class="mask fn-body-mask"></a>
	</div>


