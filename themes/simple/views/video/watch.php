<?php
/** @var $asset VodAsset */
/** @var $this VideoController */
$this->pageTitle = "mobiphim - " . $asset->display_name;
$requestUrl = $this->createUrl('/video/watch/id/' . $asset['id']);

//$this->redirect($url);
//echo $url;
$info = CUtils::getDeviceInfo();
if ($info['os'] == 'ios' || $info['os'] == 'android') { // ios hoac android
?>
<link href="<?php echo Yii::app()->request->baseUrl; ?>/js/video-js.min.css" rel="stylesheet">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/video.min.js"></script>

<div class="divider header-bg"><h3 class="dtitle"><?php echo CHtml::encode($asset['display_name']) . ($asset['is_series'] ? " - Tập $episode" : "");?></h3></div>
<div class="content">
	<div>
		<div style="width:320px; margin: 10px auto; min-height:240px;">
			<video id="videoplayer" class="video-js vjs-default-skin" controls
					preload="auto" autoplay width="320" height="240" poster="<?php echo CHtml::encode($posterLandUrl); ?>"
					data-setup="{}" style="min-height:240px;">
				<source src="<?php echo CHtml::encode($url)?>" type='video/mp4'>
			</video>
		</div>
<?php if ($asset['is_series']) {?>
		<div style="padding: 10px; width:320px; margin: 10px auto; text-align:center;"><?php if ($episode > 1) { ?><a href="<?php echo CHtml::encode("$requestUrl/episode/" . ($episode - 1));?>" class="pager gradient">&laquo; Tập trước</a><?php }?><span class="pager active"><?php echo CHtml::encode("Tập $episode");?></span><a href="<?php echo CHtml::encode("$requestUrl/episode/" . ($episode + 1));?>" class="pager gradient">Tập sau &raquo;</a></div>
<?php }?>
	</div>
<?php
if ($asset['is_series']) {
	$episode_count = count($asset->vodEpisodes);
?>
	<div class="divider"><h3 class="dtitle">Chọn tập</h3></div>
	<div class="pagination" style="text-align:center;">
<?php
	for ($i = 1; $i <= $episode_count; $i++) {
		if ($i == $episode) {
			echo "<span class=\"pager active\">$i</span>";
		} else {
			echo "<a class=\"pager gradient\" href=\"$requestUrl/episode/$i\">$i</a>";
		}
?>
<?php
	}?>
	</div>
<?php
}?>
<div class="divider header-bg"><h3 class="dtitle">PHIM CÙNG THỂ LOẠI</h3></div>
<?php
if (count($related) > 0) {
	foreach ($related as $i => $asset):
		$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:10px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase/type/watch/id/" . $asset['id'];?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0 || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
	endforeach;
} else {
	echo "<p>Không tìm thấy phim cùng thể loại</p>";
}?>
</div>

		
<?php
} else { // khong phai ios/android
?>
<script type="text/javascript">
location.href="<?php echo $url;?>";
</script>
<?php
}?>
