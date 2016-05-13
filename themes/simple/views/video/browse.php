<?php
/** @var $this VideoController */
$this->pageTitle = "mobiphim - Danh sách phim";
$records = count($assets);
if ($type == 'browse') {
	$requestUrl = $this->createUrl('/video/browse');
	if (isset($category_id))
		$requestUrl .= "/category/$category_id";
	//$requestWithoutOrder = $requestUrl;

	if ($order_by != "")
		$requestUrl .= "/order/$order_by";
} else {
	$requestUrl = $this->createUrl('/video/search/q/' . $keyword);
}
?>
<div class="divider header-bg"><h3 class="dtitle"> <?php echo CHtml::encode(mb_strtoupper($category, "UTF-8"));?></h3></div>
<?php
if ($type == 'search') {?>
					<div class="content" style="color:black;">Kết quả tìm kiếm cho từ khóa: <b><?php echo CHtml::encode(utf8_encode($keyword))?></b></div>
<?php 
}
if ($type == 'search' && $records == 0) {
?>
					<div class="content" style="color:black;">Không tìm thấy phim nào phù hợp với điều kiện tìm kiếm.</div>
					<div class="content" style="color:black;">
						<?php echo CHtml::link("Quay về", "javascript:history.go(-1);");?> |
						<?php echo CHtml::link("Tìm với từ khoá khác", "javascript:$('#q').focus();");?> |
						<?php echo CHtml::link("Trang chủ", array("/"));?>
					</div>
<?php
} else {

	foreach ($assets as $i => $asset):
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
	if ($records > 0) { $this->widget("application.widgets.Pager", array('pager' => $pager, 'baseUrl' => $requestUrl)); }
}
?>
