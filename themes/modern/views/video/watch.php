<link href="<?php echo Yii::app()->request->baseUrl; ?>/js/video-js.min.css" rel="stylesheet">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/video.min.js"></script>
<?php
/** @var $asset VodAsset */
/** @var $this VideoController */
$this->pageTitle = "mobiphim - " . $asset->display_name;
$requestUrl = $this->createUrl('/video/watch/id/' . $asset['id']);
$hasService = count($this->usingServices) > 0 ? true : false;
?>
	<div class="page" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox");?>
		<div class="container play-video" id="fnBody">
			<?php $this->widget("application.widgets.Header", array('subscriber' => $this->subscriber, 'msisdn' => $this->msisdn, 'usingServices' => $this->usingServices)); ?>
			<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
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
				<!--<ul class="info-function">
					<li><a id="fnLike" class="icon-thumbs-up" href="javascript:void(0);"><span>Thích</span></a></li>
					<li><a id="fnDislike" class="icon-thumbs-down" href="javascript:void(0);"><span>Không Thích</span></a></li>
					<li><a id="fnGoBack" class="icon-undo" href="<?php echo CHtml::encode($this->createUrl("video/" . $asset['id']));?>"><span>Quay về</span></a></li>
				</ul>-->
			
				<div class="divider header-bg"><h3 class="dtitle">PHIM CÙNG THỂ LOẠI</h3></div>
				<div class="content">
<?php
if (count($related) > 0) {
	foreach ($related as $i => $vod) {
		$rPosterUrl = array_key_exists('poster_land', $vod) ? $vod['poster_land'] : null;
		$isFree = $hasService || $vod['is_free'] == 1;
		$this->widget("application.widgets.AssetItem", array('asset' => $vod, 'posterUrl' => $rPosterUrl, 'isFree' => $isFree));
	}
} else {?>
					<div class="content-items">
						<p style="color:black;">Không tìm thấy phim liên quan</p>
					</div>
<?php
}?>
				</div>
			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>
