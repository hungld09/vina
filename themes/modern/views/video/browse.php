<?php
$this->pageTitle = "mobiphim - Danh sách phim";
$requestUrl = "";
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
$hasService = count($this->usingServices) > 0 ? true : false;
?>
<script type="text/javascript">
$(function() {
	
});
</script>

	<div class="home" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox");?>
		<div class="container" id="fnBody">
			<div class="content" id="fnBodyContent">
<?php $this->widget("application.widgets.Header", array('subscriber' => $this->subscriber, 'msisdn' => $this->msisdn, 'usingServices' => $this->usingServices)); ?>
				<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
				
				<div class="divider header-bg"><h3 class="dtitle icon-film"> <?php echo CHtml::encode(mb_strtoupper($category, "UTF-8"));?></h3></div>
				<div class="tab-content videos fnRTContent" id="fnRTAssets">
<?php
if ($type == 'search') {?>
					<div class="content-items" style="color:black;">Kết quả tìm kiếm cho từ khóa: <b><?php echo CHtml::encode(utf8_encode($keyword))?></b></div>
<?php 
}
if ($type == 'search' && $records == 0) {
?>
					<div class="content-items" style="color:black;">Không tìm thấy phim nào phù hợp với điều kiện tìm kiếm.</div>
					<div class="content-items" style="color:black;">
						<?php echo CHtml::link("Quay về", "javascript:history.go(-1);");?> |
						<?php echo CHtml::link("Tìm với từ khoá khác", "javascript:$('#q').focus();");?> |
						<?php echo CHtml::link("Trang chủ", array("/"));?>
					</div>
<?php
} else {
	foreach ($assets as $i => $asset) {
		$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
		$isFree = $hasService || $asset['is_free'] == 1;
		$this->widget("application.widgets.AssetItem", array('asset' => $asset, 'posterUrl' => $posterUrl, 'isFree' => $isFree));
	}
}
?>
				</div>
<?php if ($records > 0) { $this->widget("application.widgets.Pager", array('pager' => $pager, 'baseUrl' => $requestUrl)); }?>
			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>

