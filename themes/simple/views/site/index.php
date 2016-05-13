<?php
/** @var $this DefaultController */
$this->pageTitle = "mobiphim - Trang chủ"; 
?>

<div class="divider header-bg"><h3 class="dtitle"> PHIM MỚI NHẤT</h3></div>
<?php
foreach ($newest as $i => $asset):
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:5px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0 || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
endforeach;
?>
<div class="read-more">
<?php echo CHtml::link("Đầu trang", '#top');?>&nbsp;
<?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/order/newest'));?>
</div>



<div class="divider header-bg"><h3 class="dtitle"> PHIM TUYỂN CHỌN</h3></div>
<?php
foreach ($featured as $i => $asset):
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:10px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0 || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
endforeach;
?>
<div class="read-more">
<?php echo CHtml::link("Đầu trang", '#top');?>&nbsp;
<?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/category/15'));?>
</div>

<!--<div><a href="http://mplus.vn" target="blank"><img src="<?php echo Yii::app()->baseUrl?>/images/mplus_banner.gif" alt="mPlus" style="width:100%"/></a></div>-->

<div class="divider header-bg"><h3 class="dtitle"> PHIM KINH ĐIỂN</h3></div>
<?php
foreach ($elClasico as $i => $asset):
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:10px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0  || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
endforeach;
?>
<div class="read-more">
<?php echo CHtml::link("Đầu trang", '#top');?>&nbsp;
<?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/category/33'));?>
</div>

<div class="divider header-bg"><h3 class="dtitle"> PHIM MIỄN PHÍ</h3></div>
<?php
foreach ($freeAssets as $i => $asset):
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:10px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0 || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
endforeach;
?>
<div class="read-more">
<?php echo CHtml::link("Đầu trang", '#top');?>&nbsp;
<?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/category/27'));?>
</div>
<div class="divider header-bg"><h3 class="dtitle"> PHIM XEM NHIỀU</h3></div>
<?php
foreach ($mostViewed as $i => $asset):
	$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:10px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0  || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
endforeach;
?>
<div class="read-more">
<?php echo CHtml::link("Đầu trang", '#top');?>&nbsp;
<?php echo CHtml::link("Xem thêm &raquo;", array('/video/browse/order/most_viewed'));?>
</div>
